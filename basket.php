<?php
	/* ----------------------------------------------------------------
		    Filename: basket.php

		Requirements: $_SESSION['BASKET']
		              $_SESSION['itemid']
		              $_SESSION['postreloadpage']
		              $_SESSION['userpostagezoneid']
		              $_SESSION['userpostagezonename']
		              $_SESSION['userpostagedate']
		              $_SESSION['basketvalue']

		       Notes: Displays the basket and controls additions and
		              removals
	------------------------------------------------------------------- */

	//valid actions are
	//  viewmini (default)
	//  view
	//  add detailid quantity (no output)
	//  remove (no output)
	//  clear (no output)

	//set the default zone
	if(!isset($_SESSION['userpostagezoneid'])) {
		$_SESSION['userpostagezoneid'] = $OPT->defaultpostagezone;
	}

	//set the default delivery date
	if(!isset($_SESSION['userpostagedate'])) {
		$_SESSION['userpostagedate'] = $_SESSION['BASKET']->getNextDeliveryDate();
	}


	if(!isset($PAGEOPT['action'])) {
		$PAGEOPT['action'] = 'viewmini';
	}

	//what should we be doing
	switch(strtolower($PAGEOPT['action'])) {

		case 'add':
			$_SESSION['BASKET']->add($PAGEOPT['itemid'], $PAGEOPT['quantity']);
			reload_page();
			break;


		// -------------------------------------------------------------
		case 'remove':
			$_SESSION['BASKET']->remove($PAGEOPT['itemid'], $PAGEOPT['quantity']);
			reload_page();
			break;



		// -------------------------------------------------------------
		case 'clear':
			$_SESSION['BASKET']->create();
			reload_page();
			break;



		// -------------------------------------------------------------
		case 'view':
			$saving = false;
			$orderid = 0;
			$basketvalue=0.00;
			$basketvalueitems=0.00;
			$postagecharge=0.00;
			$noteid=1;
			$manualpostage=false;

			//save as order?
			if ( ($_SESSION['userorderid']!=0) && ($PAGEOPT['confirm']!=0) ) {
				$saving = true;
				$orderid = $_SESSION['userorderid'];
				$html->parse('main.saving');
			}


			//were the postage fields changed?
			if (count($_POST)!=0) {
			  // If the zone is changed, invalidate the selected date
				if(intval($_POST['deliveryzone']) != intval($_SESSION['userpostagezoneid'])) {
					if(intval($_SESSION['userpostagezoneid']) == 0) {
					  unset($_SESSION['userpostagedate']);
					}
				}

				if($_POST['formsubmitted']=='delivery') {
					$subDeliveryZone = intval($_POST['deliveryzone']);
					$_SESSION['userpostagezoneid'] = $subDeliveryZone;
				}

				if(!empty($_POST['deliverydate'])) {
					$_SESSION['userpostagedate'] = strtotime($_POST['deliverydate']);
				}

			}

			//the destination postage zone
			$dbZoneName = 'Collect in store. (<a href="/content/view/17/35/" target="_blank" class="obvious">Find us</a>)';
			$html->assign('DELZONE_CHECKED', '');
			$html->assign('DELZONE_ID', 0);
			$html->assign('DELZONE_TEXT', $dbZoneName);
			if(0==$_SESSION['userpostagezoneid']) {
				$_SESSION['userpostagezonename'] = $dbZoneName;
				$html->assign('POSTAGE_ZONE_NAME', $dbZoneName);
				$html->assign('DELZONE_CHECKED', ' checked="checked"');
			}
			$html->parse('main.form.inputdelzone');


			$rs=view_postagezones();
			while($r=db_fetch_array($rs)) {
				$dbZoneID=$r['PostageZoneID'];
				$dbZoneName=$r['PostageZoneName'];

				$html->assign('DELZONE_CHECKED', '');
				$html->assign('DELZONE_ID', $dbZoneID);
				$html->assign('DELZONE_TEXT', $dbZoneName);

				//check if current zone and update cached info
				if($dbZoneID==$_SESSION['userpostagezoneid']) {
					$_SESSION['userpostagezonename'] = $dbZoneName;
					$html->assign('POSTAGE_ZONE_NAME', $dbZoneName);
					$html->assign('DELZONE_CHECKED', ' checked="checked"');
				}
				$html->parse('main.form.inputdelzone');
			}

			//the available delivery dates
			$t=0;
			for ($i=0; $i<$OPT->maxorderleadtime; $i++) {
				$nt = $_SESSION['BASKET']->getNextDeliveryDate($t, $i);
				if(!isset($_SESSION['userpostagedate'])) {
				  $_SESSION['userpostagedate'] = $nt;
				}
				$html->assign("DELDATE_VALUE", date("Ymd", $nt));
				$html->assign("DELDATE_TEXT", date("d-m-Y (l)", $nt));
				$html->assign("DELDATE_SELECTED", "");
				if ($nt==$_SESSION["userpostagedate"]) {
					$html->assign("DELDATE_SELECTED", " selected=\"selected\"");
				}
				$html->parse("main.form.deliverydate.inputdeldate");
			}

			if (0 == $_SESSION['userpostagezoneid']) {
			  $html->parse("main.form.nodeliverydate");
			} else {
			  $html->parse("main.form.deliverydate");
			}

			//the items
			//work out if the postage is too complex to be calculated automatically
			$postageamount = $_SESSION['BASKET']->getBasketPostage($needspallet, $specialpostage, $includessaturdaysurcharge);
/* pallet-charges are now fixed, so no need to put a disclaimer about varying prices.
			if($needspallet) {
				$html->assign('NOTE_NUMBER', $noteid++);
				$html->assign('NOTE_TEXT', $lang['postage_overcharge']);
				$html->parse('main.notes.row');
				$manualpostage = true;
			}
*/

			$basketitems=$_SESSION['BASKET']->keylist();

			$rowclass='a';
			$rs=view_details('', $basketitems);
			$_SESSION['google_transaction_lines'] = array();
			while($r=db_fetch_array($rs)) {
				$dbItemID=$r['ItemID'];
				$dbItemName=$r['ItemName'];
				$dbDetailID=$r['DetailID'];
				$dbDetailName=$r['DetailName'];
				$dbDetailPrice=$r['WebPrice'];
				$DetailQuantity=$_SESSION['BASKET']->lineqty($dbDetailID);
				$dbDetailStockCode = $r['StockCode'];
				$dbStockCodePrefix = $r['StockCodePrefix'];

				$maincats = view_itemcategories($dbItemID, true);
				while($mc=db_fetch_array($maincats)) {
				  $DetailCategory=$mc['CatParentName'].'::'.$mc['CatChildName'];
					break;
				}

				$basketvalue+=$dbDetailPrice*$DetailQuantity;
				$basketvalueitems+=$dbDetailPrice*$DetailQuantity;

				$html->assign('ITEM_NAME', "$dbItemName ($dbDetailName)");
				$html->assign('DETAIL_STOCKCODE', sprintf("%02d%03d", $dbStockCodePrefix, $dbDetailStockCode));
				$html->assign('DETAIL_ID', $dbDetailID);
				$html->assign('DETAIL_PRICE', format_currency($dbDetailPrice, 'POA'));
				$html->assign('DETAIL_QTY', $DetailQuantity);
				$html->assign('DETAIL_TOTAL', format_currency($dbDetailPrice*$DetailQuantity, 'POA'));
				$html->assign('ROW_CLASS', 'class="row_'.$rowclass.'"');

				if($saving) {
					$dbOrderLineId = spInsertOrderLine($orderid, $dbDetailID, $DetailQuantity, $dbDetailPrice, 0);

 					// Save the line for the Google tracking
 					$line = array();
					$line['id'] = $orderid;
					$line['sku'] = $dbStockCdoePrefix.$dbDetailStockCode;
					$line['name'] = $dbItemName . ' ' . $dbDetailName;
					$line['category'] = $DetailCategory;
					$line['price'] = $dbDetailPrice;
					$line['qty'] = $DetailQuantity;
 					$_SESSION['google_transaction_lines'][] = $line;

				} else {
					$html->parse('main.row.ammend');
				}
				$html->parse('main.row');
				$rowclass=($rowclass=='a'?'b':'a');


				//print a surplus postage line?
				$surpluscharge = $_SESSION['BASKET']->postagespecials($specials, $dbDetailID);
				if($surpluscharge>0) {
				    $totalFixedPostageCharges+=$surpluscharge;
						$text = sprintf($lang['postage_additional'], implode(',', $specials));
						$basketvalue += $surpluscharge;
						$html->assign('ITEM_NAME', $text);
						$html->assign('DETAIL_STOCKCODE', '');
						$html->assign('DETAIL_ID', '');
						$html->assign('DETAIL_PRICE', format_currency($surpluscharge/$DetailQuantity, 'POA'));
						$html->assign('DETAIL_QTY', $DetailQuantity);
						$html->assign('DETAIL_TOTAL', format_currency($surpluscharge, 'POA'));
						if($saving) {
							spInsertOrderPostageLine($orderid, $text, $DetailQuantity, ($surpluscharge/$DetailQuantity));
						}
					$html->parse('main.row');
					
				} else {
					if($manualpostage) {
						$text = sprintf($lang['postage_additional'], implode(',', $specials));
						$html->assign('ITEM_NAME', $text);
						$html->assign('DETAIL_STOCKCODE', '');
						$html->assign('DETAIL_ID', '');
						$html->assign('DETAIL_PRICE', '');
						$html->assign('DETAIL_QTY', '');
						$html->assign('DETAIL_TOTAL', $lang['postage_notes']);
						if($saving) {
							spInsertOrderPostageLine($orderid, $text, 1, 0);
						}
						$html->parse('main.row');
					}
				}



			}
			$_SESSION['basketvalue']=$basketvalue;
			$html->assign('DETAIL_STOCKCODE', '&nbsp;');



			//the postage due (what band)
			$basketbands=$_SESSION['BASKET']->postageband($basketvalueitems);
			$_SESSION['userpostagebandid']=$basketbands[0];
			$_SESSION['userpostagebandname']=$basketbands[1];


			//now get the actual amount for postage
			$_SESSION['userpostagebase']=0;
			
      if (0 == $_SESSION['userpostagezoneid']) {
				$_SESSION['userpostageamount']='0';
				$_SESSION['userpostageunknown']=false;
				$html->assign('NOTE_NUMBER', $noteid++);
				$html->assign('NOTE_TEXT', 'Collect-In-Store items are usually available for collection within 48 hours.  We will contact you as soon as your order is ready for collection.');
				$html->parse('main.notes.row');

				
      } else {
			
				$rs=view_postagecharge($_SESSION['userpostagezoneid'], $_SESSION['userpostagebandid']);
				if(db_num_rows($rs)==0) {
					$_SESSION['userpostageamount']='0';
					$_SESSION['userpostageunknown']=true;
				}
				while($r=db_fetch_array($rs)) {
					$dbCharge=$r['Charge'];
					$_SESSION['userpostageamount']=$dbCharge;
					$_SESSION['userpostageunknown']=false;
					$_SESSION['userpostagebase']=$dbCharge;
				}
			}

			if(!$manualpostage) {

				if($_SESSION['userpostageunknown']) {
					$text = sprintf($lang['postage_additional'], $_SESSION['userpostagezonename']);
					$html->assign('ROW_CLASS', 'class="list"');
					$html->assign('ITEM_NAME', $text);
					$html->assign('DETAIL_ID', 0);
					$html->assign('DETAIL_PRICE', '&nbsp;');
					$html->assign('DETAIL_QTY', '&nbsp;');
					$html->assign('DETAIL_TOTAL', $lang['postage_notes']);
					$html->parse('main.row');
					$html->assign('NOTE_NUMBER', $noteid++);
					$html->assign('NOTE_TEXT', sprintf($lang['postage_no_zone'], $_SESSION['userpostagezonename']));
					$html->parse('main.notes.row');
					if($saving) {
						spInsertOrderPostageLine($orderid, $text, 1, 0);
					}

				} else {
					//add postage charge
					$text = 'Delivery - '.$_SESSION['userpostagezonename'];
					$html->assign('ROW_CLASS', 'class="list"');
					$html->assign('ITEM_NAME', $text);
					$html->assign('DETAIL_ID', 0);
					$html->assign('DETAIL_PRICE', '&nbsp;');
					$html->assign('DETAIL_QTY', '&nbsp;');
					$html->assign('DETAIL_TOTAL', format_currency($_SESSION['userpostageamount'],'FREE'));
					//only show a FREE line if there are no additional charges
					if( ($_SESSION['userpostageamount']!=0)
						|| (($totalFixedPostageCharges==0) && ($manualpostage==false))

						) {
						$html->parse('main.row');
					}
					$basketvalue+=$dbCharge;
					if($saving) {
						spInsertOrderPostageLine($orderid, $text, 1, $_SESSION['userpostageamount']);
					}

					//saturday surplus
          if (!$_SESSION['userpostagezoneid']==0) {
          	
						$deldate=getdate($_SESSION['userpostagedate']);
						if( ($deldate['wday']==6) && ($OPT->saturdaysurcharge>0) ){
							$text = 'Saturday Delivery';
							$html->assign('ROW_CLASS', '');
							$html->assign('ITEM_NAME', $text);
							$html->assign('DETAIL_ID', 0);
							$html->assign('DETAIL_PRICE', '&nbsp;');
							$html->assign('DETAIL_QTY', '&nbsp;');
							$html->assign('DETAIL_TOTAL', format_currency($OPT->saturdaysurcharge,'FREE'));
							$html->parse('main.row');
							$_SESSION['userpostageamount']+=$OPT->saturdaysurcharge;
							$basketvalue+=$OPT->saturdaysurcharge;
							if($saving) {
								spInsertOrderPostageLine($orderid, $text, 1, $OPT->saturdaysurcharge);
							}
						}
					}

				}

				//add on the fixed postage charges we calculated earlier
				$_SESSION['userpostageamount']+=$totalFixedPostageCharges;
			}

			//show the grand total
			$html->assign('ORDER_TOTAL', format_currency($basketvalue));
			$html->assign('ORDER_TOTAL_NOTE', ($manualpostage || $_SESSION['userpostageunknown']) ? '+P&amp;P' : '&nbsp;');

			if($noteid>1) $html->parse('main.notes');

			//if there's an orderid set, then display the order confirmation
			//screen and clear the basket
			if($saving) {
				$html->assign('ORDER_ID', $orderid);
				$html->parse('main.orderconf');
				unset($_SESSION['userorderid']);
				unset($_SESSION['BASKET']);

			} else {
				$html->parse('main.ammendlabel');
				$html->assign('DELIVERYDATE_NOTE', $lang['postage_deliverydatenote']);
				$html->parse('main.form');
				$html->parse('main.shopbutton');
			}

			break;



		// -------------------------------------------------------------
		case 'viewmini':
			if($_SESSION['BASKET']->itemcount()>0) {
				$previousItemID=0;
				$basketvalue=0.00;

				//get a CSV list of detailID's in the basket, and using that
				//generate output for all the items and details ordered
				$basketitems=$_SESSION['BASKET']->keylist();
				$rs=view_details('', $basketitems);
				while($r=db_fetch_array($rs)) {
					$dbItemID=$r['ItemID'];
					$dbItemName=$r['ItemName'];
					$dbDetailID=$r['DetailID'];
					$dbDetailName=$r['DetailName'];
					$dbDetailPrice=$r['WebPrice'];
					$DetailQuantity=$_SESSION['BASKET']->qty["x$dbDetailID"];

					$basketvalue+=$dbDetailPrice*$DetailQuantity;

					$html->assign('ITEM_ID', $dbItemID);
					$html->assign('ITEM_NAME', $dbItemName);
					$html->assign('DETAIL_ID', $dbDetailID);
					$html->assign('DETAIL_NAME', $dbDetailName);
					$html->assign('DETAIL_QTY', $DetailQuantity.'x');
					$html->assign('DETAIL_VALUE', format_currency($dbDetailPrice*$DetailQuantity, 'POA'));

					if($previousItemID!=$dbItemID) {
						$html->parse('main.minibasket.row.headerrow');
					}
					$html->parse('main.minibasket.row.remove');
					$html->parse('main.minibasket.row');
					$previousItemID=$r['ItemID'];
				}


// MINI BASKET NO LONGER SHOWS TOTAL VALUE OF BASKET
/*
				//if there is a postage charge, display it on the mini basket
				$postageamount = $_SESSION['BASKET']->getBasketPostage($needspallet, $specialpostage, $includessaturdaysurcharge);
				if($needspallet || ($_SESSION['userpostagezonename']=='Overseas')) {
						$html->assign('ITEM_NAME', 'Additional Postage');
						$html->assign('DETAIL_ID', 0);
						$html->assign('DETAIL_NAME', 'see checkout page for details');
						$html->assign('DETAIL_QTY', '');
						$html->assign('DETAIL_VALUE', '');
						$html->parse('main.minibasket.mergedrow');
						$html->parse('main.minibasket.additionalpp');
				} else {
					if($postageamount>0) {
						$displaylabel=implode(',', $specialpostage);
						if($includessaturdaysurcharge>0) {
							if(($displaylabel!='')) $displaylabel .= ', ';
							$displaylabel .= 'Saturday';
						}

						$html->assign('ITEM_NAME', 'Additional Postage');
						$html->assign('DETAIL_ID', 0);
						$html->assign('DETAIL_NAME', $displaylabel);
						$html->assign('DETAIL_QTY', '');
						$html->assign('DETAIL_VALUE', format_currency($postageamount));
						$html->parse('main.minibasket.row.headerrownohref');
						$html->parse('main.minibasket.row');
					}
					//add all postage charges to the current basket value
					$basketvalue+=$postageamount;
				}
*/
				$html->parse('main.minibasket.additionalpp');

				//output
				$html->assign('BASKET_TOTAL', format_currency($basketvalue));
				$html->parse('main.minibasket');

			}
			break;

	}


?>