<?php
	/* ----------------------------------------------------------------
		    Filename:

		Requirements:

		       Notes:
	------------------------------------------------------------------- */

	//these are the tabs
	$tabid_general=1;
	$tabid_details=2;
	$tabid_categories=3;
	$tabid_suppliers=4;
	$tabid_postage=5;
	$tabid_pictures=6;
	$tabid_stock=7;
	$tabid_links=8;

	$html->assign("TABID_GENERAL", $tabid_general);
	$html->assign("TABID_DETAILS", $tabid_details);
	$html->assign("TABID_CATEGORIES", $tabid_categories);
	$html->assign("TABID_SUPPLIERS", $tabid_suppliers);
	$html->assign("TABID_POSTAGE", $tabid_postage);
	$html->assign("TABID_PICTURES", $tabid_pictures);
	$html->assign("TABID_LINKS", $tabid_links);

	//which item are we editing?
	$ItemID=$_SESSION['itemid'];
	if(empty($ItemID)) $ItemID=0;

	//what tab are we on? (new items are always on tab 1)
	$currenttab=$tabid_general;
	if( (!empty($_SESSION['edititemtab'])) && ($ItemID!=0) ) $currenttab=$_SESSION['edititemtab'];
	$html->assign("TABCLASS_$currenttab", 'class="current"');
	$html->assign("TABID", $currenttab);


	// *=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*
	// FORM HANDLING
	// *=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*
	if (count($_POST)!=0) {
		//echo(fancy_vardump($_POST));
		//detail save button was pressed
		if (isset($_POST['imgSubmitDetails_x'])) {
			$subitemid = httppost_int('hidItemID');

			foreach($_POST as $fieldname => $fieldvalue) {

				//existing rows
				if(substr($fieldname,0,14)=='txtDetailName_') {
					$subdetailid        = intval(substr($fieldname, strpos($fieldname,'_')+1));
					$subdetailname      = httppost_string("txtDetailName_$subdetailid");
					$subdetailpriceshop = httppost_string("txtDetailPrice_$subdetailid");
					$subdetailpriceweb  = httppost_string("txtDetailWebPrice_$subdetailid");
					$subdetailrrp       = httppost_string("txtDetailRRP_$subdetailid");
					$subdetailvisweb    = httppost_string("chkDetailVisWeb_$subdetailid");
					$subdetailbuyweb    = httppost_string("chkDetailBuyWeb_$subdetailid");
					$subdetailviscat    = httppost_string("chkDetailVisCat_$subdetailid");
					$subdetailbuycat    = httppost_string("chkDetailBuyCat_$subdetailid");
					$subdetailsize      = httppost_string("txtDetailSize_$subdetailid");
					$subdetailunit      = httppost_string("selDetailUnits_$subdetailid");
					$subdetaildelete    = httppost_string("chkDetailDelete_$subdetailid");
					$newdetailid        = 0;

					//if a % was specified for the price, use the cheapest
					//supplier price to calculate a value
					//only applies to existing records
					if( ($subdetailid!=0) && (strpos($subdetailpriceshop,"%")!==false) ) {
						$markup = 0.01*(str_replace('%','',$subdetailpriceshop));
						$cheapest = GetDetailCheapestPrice($subdetailid);
						$subdetailpriceshop = ($cheapest+($cheapest*$markup))*$vatadd;
					}

					//delete, add or update item
					if($subdetaildelete==1) {
						//delete
						spDeleteItemDetails($subdetailid);
					} elseif( ($subdetailid==0) && (strlen($subdetailname)!=0) ) {
						//add
						$newdetailid = spInsertItemDetails($subitemid, $subdetailname, $subdetailsize, $subdetailunit, $subdetailpriceshop, $subdetailpriceweb, $subdetailrrp, $subdetailvisweb, $subdetailbuyweb, $subdetailviscat, $subdetailbuycat);
						spInsertDetailSupplier($newdetailid, 1, 0, 0, 0);
					} else {
						//update
						spUpdateItemDetails($subdetailid, $subdetailname, $subdetailsize, $subdetailunit, $subdetailpriceshop, $subdetailpriceweb, $subdetailrrp, $subdetailvisweb, $subdetailbuyweb, $subdetailviscat, $subdetailbuycat);
					}
				}
			}
		}


		//supplier save button was pressed
		if (isset($_POST['imgSubmitSuppliers_x'])) {
			//echo(fancy_vardump($_POST));
			foreach($_POST as $fieldname => $fieldvalue) {

				if(substr($fieldname,0,20)=='txtDetailTradePrice_') {
					$tmp = explode('_', $fieldname);
					$subdetailid    = intval($tmp[1]);
					$subsupplierid  = intval($tmp[2]);
					$subprice       = httppost_num("txtDetailTradePrice_{$subdetailid}_{$subsupplierid}");
					$subrate1       = 0.01*httppost_num("txtDetailTradeDiscount_{$subdetailid}_{$subsupplierid}");
					$subrate2       = 0.01*httppost_num("txtDetailPreSeasonDiscount_{$subdetailid}_{$subsupplierid}");
					$subdelete      = httppost_int("chkDelete_{$subdetailid}_{$subsupplierid}");
					$subnewdetail   = httppost_int("selNewDetailID");
					$subnewsupplier = httppost_int("selNewSupplierID");
					$subnewprice    = httppost_num("txtDetailTradePrice_0_0");
					$subnewrate1    = 0.01*httppost_num("txtDetailTradeDiscount_0_0");
					$subnewrate2    = 0.01*httppost_num("txtDetailPreSeasonDiscount_0_0");

					//delete, add or update item
					if($subdelete==1) {
						//delete
						spDeleteDetailSupplier($subdetailid, $subsupplierid);
					} elseif( ($subnewdetail!=0) && ($subnewsupplier!=0) ) {
						//add
						spInsertDetailSupplier($subnewdetail, $subnewsupplier, $subnewprice, $subnewrate1, $subnewrate2);
					} else {
						//update
						spUpdateDetailSupplier($subdetailid, $subsupplierid, $subprice, $subrate1, $subrate2);
					}
				}
			}
		}


		//stock save button was pressed
		if (isset($_POST['imgSubmitStock_x'])) {
			foreach($_POST as $fieldname=>$fieldvalue) {

				if(substr($fieldname,0,12)=='txtSetStock_') {
					$tmp = explode('_', $fieldname);
					$subdetailid = intval($tmp[1]);
					$substocklevel = httppost_int("txtSetStock_{$subdetailid}");
					SetStockLevel($subdetailid, $substocklevel, 0);
				}

				if(substr($fieldname,0,10)=='chkDelete_') {
					$tmp = explode('_', $fieldname);
					$substockid           = intval($tmp[1]);
					$substockdate         = httppost_string("txtStockDate_{$substockid}");
					$substockprice        = httppost_string("txtStockPrice_{$substockid}");
					$substockqtybought    = httppost_string("txtStockQtyBought_{$substockid}");
					$subdelete            = httppost_int("chkDelete_{$substockid}");
					$subnewdetailid       = httppost_int("selNewStockDetailID_{$substockid}");
					$subnewsupplierid     = httppost_int("selNewStockSupplierID_{$substockid}");

					//delete or add
					if($subdelete==1) {
						//delete
						spDeleteStock($substockid);

					} elseif( ($subnewdetailid!=0) && ($subnewsupplierid!=0) ) {
						//add
						spInsertStock($subnewdetailid, $subnewsupplierid, $substockdate, $substockprice, $substockqtybought, $substockqtybought);
					}
				}

			}
		}



		//create new general item info
		if (isset($_POST['imgAddItemGeneral_x'])) {
			$SubItemName = httppost_string('txtItemName');
			$SubItemDescription = httppost_string('txtItemDescription');
			$ItemID=spInsertItemGeneral($SubItemName, $SubItemDescription);
			spInsertItemCategory($ItemID, $_SESSION['subcatid'], true);
			$_SESSION['PersistentText'].="Created Item<br />\n";

		//delete whole item
		} elseif (isset($_POST['imgDeleteItem_x'])) {
			spDeleteItem($ItemID);
			$_SESSION['PersistentText'].="Deleted Item<br />\n";

		//undelete whole item
		} elseif (isset($_POST['imgUnDeleteItem_x'])) {
			spUnDeleteItem($ItemID);
			$_SESSION['PersistentText'].="UnDeleted Item<br />\n";

		//update general item info
		} elseif (isset($_POST['imgSaveItemGeneral_x'])) {
			$SubItemName = httppost_string('txtItemName');
			$SubItemDescription = httppost_string('txtItemDescription');
			$SubRecommended = httppost_int('chkRecommended');
			$SubWebView = httppost_int('chkWebView');
			$SubArticle = httppost_string('txtArticle');
			
			//fix lonely &'s
			$SubItemName = str_replace(' & ', ' &amp; ', $SubItemName);
			$SubItemDescription = str_replace(' & ', ' &amp; ', $SubItemDescription);
			
			//fix some obvious typo's
			$SubItemDescription = str_replace('<br>', '<br />', $SubItemDescription);
			
			spUpdateItemGeneral($ItemID, $SubItemName, $SubItemDescription, $SubRecommended, $SubWebView, $SubArticle);
			$_SESSION['PersistentText'].="Saved Item<br />\n";

		//add a new category
		} elseif (isset($_POST['imgAddCategory_x'])) {
			$SubCategoryID = httppost_int('selAddCategory');
			spInsertItemCategory($ItemID, $SubCategoryID);
			$_SESSION['PersistentText'].="Added Category $SubCategoryID to $ItemID<br />\n";

		//upload a new image
		} elseif (isset($_POST['imgAddPicture_x'])) {
			//get uploaded names
			$SubDetailID = $_POST['selNewPictureDetailID'];
			$SubItemImageName = $_FILES['fileNew']['name'];
			$SubItemImageTempLocation = $_FILES['fileNew']['tmp_name'];
			$fileextention = strstr($SubItemImageName, '.');
			if($fileextention===false) {
				$fileextention='';
			}
			$imgprops = getimagesize($SubItemImageTempLocation);

			//insert info into the db
			$NewPictureID = spInsertPicture('', $SubItemImageName, '', $imgprops);

			//generate a new file name
			$destinationfile = $_SERVER['DOCUMENT_ROOT'].$OPT->productimageroot.getPictureFileName($NewPictureID);

			//replace if no duplicates
			$filefound=file_exists($destinationfile);
			if($filefound) {
				$_SESSION['PersistentText'] .= "Image NOT added, already exists";
			} else {
				move_uploaded_file($SubItemImageTempLocation, $destinationfile);
				$_SESSION['PersistentText'] .= "Image $SubItemImageName added";
			}

			//add the item-picture link
			spInsertItemPicture($ItemID, $SubDetailID, $NewPictureID);


		} else {

			//deal with any submissions from multi-views
			foreach($_POST as $fieldname => $fieldvalue) {
				if(substr($fieldname,-2,2)=='_x') {
					$multipost = explode('_', $fieldname);
					$inputname = $multipost[0];
					$inputid = $multipost[1];

					//set current primary category?
					if (strcmp($inputname,'imgNewPrimaryCategory')==0) {
						spUpdateItemCategory($ItemID, $inputid, True);
						$_SESSION['PersistentText'].="Primaryised Detail $inputid<br />\n";

					//set current primary picture?
					} elseif (strcmp($inputname,'imgNewPrimaryPicture')==0) {
						spSetPrimaryItemPicture($inputid);
						$_SESSION['PersistentText'].="Primaryised Picture $inputid<br />\n";

					//delete a category
					} elseif (strcmp($inputname,'imgDeleteCategory')==0) {
						spDeleteItemCategory($ItemID, $inputid);
						$_SESSION['PersistentText'] .= "Deleted Category $inputid<br />\n";

					//add item-picture
					} elseif (strcmp($inputname,'imgAddPicture')==0) {
						$SubDetailID = httppost_int('selNewPictureDetailID');
						$SubPictureID = httppost_int("selNewPicture");
						spInsertItemPicture($ItemID, $SubDetailID, $SubPictureID);

					//delete item-picture
					} elseif (strcmp($inputname,'imgDeletePicture')==0) {
						spDeleteItemPicture($inputid);

					//add detail-postagecharge
					} elseif (strcmp($inputname,'imgAddPostageCharge')==0) {
						echo('add pc');
						$SubDetailID = httppost_int('selNewPostageChargeDetailID');
						$SubPostageChargeID = httppost_int("selNewPostageCharge_$inputid");
						$SubPostagePallet = httppost_int('txtNewPostagePallet');
						$SubPostageCharge = httppost_num('txtNewPostageCharge');
						if( ($SubDetailID!=0) && ($SubPostageChargeID!=0)  ) {
							spInsertDetailPostageCharge($SubDetailID, $SubPostageChargeID, $SubPostagePallet, $SubPostageCharge);
							$_SESSION['PersistentText'].="Added postagecharge ($inputid, $SubPostageChargeID, $SubPostagePallet, $SubPostageCharge)<br />\n";
						}

					//delete detail-postagecharge
					} elseif (strcmp($inputname,'imgDeletePostageCharge')==0) {
						$SubPostageChargeID = $multipost[2];
						spDeleteDetailPostageCharge($inputid, $SubPostageChargeID);
						$_SESSION['PersistentText'].="Deleted postage charge $inputid, $SubPostageChargeID<br />\n";

					//update detail price override
					} elseif (strcmp($inputname,'imgUpdateOverride')==0) {
						$SubOverridePrice = httppost_string("txtOverridePrice_$inputid");
						$SubOverrideStart = httppost_string("txtOverrideStart_$inputid");
						$SubOverrideEnd = httppost_string("txtOverrideEnd_$inputid");
						spUpdateDetailOverride($inputid, $SubOverridePrice, $SubOverrideStart, $SubOverrideEnd);
						$_SESSION['PersistentText'].="Added override $inputid, $SubOverridePrice, $SubOverrideStart, $SubOverrideEnd<br />\n";
						$_SESSION['PopupMadeChange']=True;

					//delete detail price override
					} elseif (strcmp($inputname,'imgDeleteOverride')==0) {
						$SubOverridePrice = 0;
						$SubOverrideStart = 0;
						$SubOverrideEnd = 0;
						spUpdateDetailOverride($inputid, $SubOverridePrice, $SubOverrideStart, $SubOverrideEnd);
						$_SESSION['PersistentText'].="Cleared override $inputid<br />\n";
						$_SESSION['PopupMadeChange']=True;


					//update detail dimensions
					} elseif (strcmp($inputname,'imgUpdateDimensions')==0) {
						$SubDimLength  = httppost_string("txtDimLength_$inputid");
						$SubDimWidth  = httppost_string("txtDimWidth_$inputid");
						$SubDimHeight = httppost_string("txtDimHeight_$inputid");
						$SubDimWeight = httppost_string("txtDimWeight_$inputid");
						$SubDimVolume = httppost_string("txtDimVolume_$inputid");
						$SubUnitWidth  = httppost_int("selUnitWidth_$inputid");
						$SubUnitHeight = httppost_int("selUnitHeight_$inputid");
						$SubUnitLength = httppost_int("selUnitLength_$inputid");
						$SubUnitWeight = httppost_int("selUnitWeight_$inputid");
						$SubUnitVolume = httppost_int("selUnitVolume_$inputid");

						spUpdateDetailDimensions($inputid, $SubDimWidth, $SubDimHeight, $SubDimLength, $SubDimWeight, $SubDimVolume, $SubUnitWidth, $SubUnitHeight, $SubUnitLength, $SubUnitWeight, $SubUnitVolume);
						$_SESSION['PersistentText'].="Added dimensions $inputid, $SubDimWidth, $SubDimHeight, $SubDimLength, $SubDimWeight, $SubDimVolume, $SubUnitWidth, $SubUnitHeight, $SubUnitLength, $SubUnitWeight, $SubUnitVolume<br />\n";
						$_SESSION['PopupMadeChange']=True;

					//delete detail dimensions
					} elseif (strcmp($inputname,'imgDeleteDimensions')==0) {
						$SubOverridePrice = 0;
						$SubOverrideStart = 0;
						$SubOverrideEnd = 0;
						//spUpdateDetailOverride($inputid, $SubOverridePrice, $SubOverrideStart, $SubOverrideEnd);
						//$_SESSION['PersistentText'].="Cleared override $inputid<br />\n";
						//$_SESSION['PopupMadeChange']=True;

					//add item link
					} elseif (strcmp($inputname,'imgAddLink')==0) {
            $SubChildID = httppost_int("selAddLink");
            
						spInsertItemLink($ItemID, $SubChildID);
						$_SESSION['PersistentText'] .= "Added Link $SubChildID to item $ItemID<br />\n";

					//delete item link
					} elseif (strcmp($inputname,'imgDeleteLink')==0) {
						spDeleteItemLink($ItemID, $inputid);
						$_SESSION['PersistentText'] .= "Deleted Link $inputid<br />\n";


					//end of  inserts/updates/deletes
					}
				}
			}
		}
		reload_page();
	}





	// *=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*
	// PAGE DISPLAY
	// *=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*
	//always have the basics available...
	if($ItemID!=0) {
		$rs=view_items('',$ItemID);
		$r=db_fetch_array($rs);
		$dbItemID = $r['ItemID'];
		$dbName = $r['ItemName'];
		$dbEditName = htmlentities($r['ItemName']);
		$dbDescription = htmlentities($r['Description']);
		$dbLiveDescription = hyperlink_items($r['Description']);
		$dbArticle = htmlentities($r['Article']);
		$dbCreateDate = format_datetime($r['ItemCreateDate']);
		$dbModifyDate = format_datetime($r['ItemModifyDate']);
		$dbDeleteDate = format_datetime($r['ItemDeleteDate']);
		$dbWebView = $r['WebView'];
		$dbRecommended = $r['Recommended'];
		$dbDeleted = (boolean)($r['ItemDeleteDate']!='0000-00-00 00:00:00');
		$dbPictureID = $r['PictureID'];

		$html->assign('ITEM_TITLE', "Editing item \"$dbName\"");
		$html->assign('DIVCLASS', '');
		if($dbDeleted) $html->assign('DIVCLASS', 'deleted');
	}



	//popups
	if($_SESSION['edititemtype']!=0) {
		$rs=view_details(0,$_SESSION['detailid']);
		$r=db_fetch_array($rs);

		$dbItemID=$r['ItemID'];
		$dbDetailID=$r['DetailID'];
		$dbDetailPrice=$r['OriginalRetailPrice'];
		$dbDetailWebPrice=$r['WebPrice'];
		$dbOverridePrice=$r['OverridePrice'];
		$dbOverrideStart=$r['OverrideStart'];
		$dbOverrideEnd=$r['OverrideEnd'];
		$dbDimHeight=$r['DimHeight'];
		$dbDimWidth=$r['DimWidth'];
		$dbDimLength=$r['DimLength'];
		$dbDimWeight=$r['DimWeight'];
		$dbDimVolume=$r['DimVolume'];
		$dbUnitHeight=$r['UnitIDHeight'];
		$dbUnitWidth=$r['UnitIDWidth'];
		$dbUnitLength=$r['UnitIDLength'];
		$dbUnitWeight=$r['UnitIDWeight'];
		$dbUnitVolume=$r['UnitIDVolume'];
		$dbItemName = htmlentities($r['ItemName']);
		$dbDetailName = htmlentities($r['DetailName']);

		if($_SESSION['PopupMadeChange']==True) {
			$_SESSION['PopupMadeChange']=False;
			//$html->parse('main.popup.reloadparent');
			$html->parse('main.popup.closepopup');
		}


		$html->assign('ITEM_ID', $dbItemID);
		$html->assign('ITEM_NAME', $dbItemName);
		$html->assign('DETAIL_ID', $dbDetailID);
		$html->assign('DETAIL_NAME', $dbDetailName);
		$html->assign('OUR_PRICE', format_currency($dbDetailPrice));
		$html->assign('WEB_PRICE', format_currency($dbWebPrice));
		$html->assign('OVERRIDE_PRICE', format_currency($dbOverridePrice,'','',''));
		$html->assign('OVERRIDE_START', format_datetime($dbOverrideStart));
		$html->assign('OVERRIDE_END', format_datetime($dbOverrideEnd));
		$html->assign('DIM_HEIGHT', format_numbercompact($dbDimHeight));
		$html->assign('DIM_WIDTH', format_numbercompact($dbDimWidth));
		$html->assign('DIM_LENGTH', format_numbercompact($dbDimLength));
		$html->assign('DIM_WEIGHT', format_numbercompact($dbDimWeight));
		$html->assign('DIM_VOLUME', format_numbercompact($dbDimVolume));
		$html->assign('UNIT_HEIGHT', draw_select("selUnitHeight_$dbDetailID", 'unit', 'UnitID', 'Code', $dbUnitHeight));
		$html->assign('UNIT_WIDTH', draw_select("selUnitWidth_$dbDetailID", 'unit', 'UnitID', 'Code', $dbUnitWidth));
		$html->assign('UNIT_LENGTH', draw_select("selUnitLength_$dbDetailID", 'unit', 'UnitID', 'Code', $dbUnitLength));
		$html->assign('UNIT_WEIGHT', draw_select("selUnitWeight_$dbDetailID", 'unit', 'UnitID', 'Code', $dbUnitWeight));
		$html->assign('UNIT_VOLUME', draw_select("selUnitVolume_$dbDetailID", 'unit', 'UnitID', 'Code', $dbUnitVolume));

		if($_SESSION['edititemtype']==1) {
			$html->parse('main.popup.override');
			$setFocusForm='frmEditItem';
			$setFocusField="txtOverridePrice_$dbDetailID";
		}
		if($_SESSION['edititemtype']==2) {
			$html->parse('main.popup.dimensions');
			$setFocusForm='frmEditItem';
			$setFocusField="txtDimHeight_$dbDetailID";
		}

		$html->parse('main.popup');

	} elseif($_SESSION['edititemtype']==2) {
		$html->parse('main.popup.dimensions');
		$html->parse('main.popup');

	} else {


		//start some of the interesting item output
		$html->assign('ITEM_ID', $ItemID);
		switch ($currenttab) {

			//general item info
			case $tabid_general:
				$setFocusForm='frmEditItem';
				$setFocusField='txtItemName';

				if($ItemID==0) {
					$html->assign('ITEM_ID', 0);
				} else {
					$html->assign('ITEM_ID', $dbItemID);
					$html->assign('ITEM_NAME', $dbEditName);
					$html->assign('ARTICLE', $dbArticle);
					$html->assign('WEB_VIEW', $dbWebView ? ' checked="checked"' : '');
					$html->assign('RECOMMENDED', $dbRecommended ? ' checked="checked"' : '');
					$html->assign('ITEM_DESCRIPTION', $dbDescription);
					$html->assign('LIVE_ITEM_DESCRIPTION', $dbLiveDescription);
					$html->assign('ITEM_IMAGEFILENAME', $dbImageFilename);
					$html->assign('IMAGE', img_safe($OPT->productimageroot.$dbImageFilename, $dbEditName, 'bordered'));
					$html->assign('CREATE_DATE', $dbCreateDate);
					$html->assign('MODIFY_DATE', $dbModifyDate);
					$html->assign('DELETE_DATE', $dbDeleteDate);

					if ($dbDeleteDate != '') $html->parse("main.maintabs.tab_$currenttab.deletedate");
					$html->parse("main.maintabs.tab_$currenttab.morefields");
				}

				for ($i=0; $i<count($ENIGMA_ORIENT); $i++) {
					$html->assign('OPTION_ID', $i);
					$html->assign('OPTION_NAME', $ENIGMA_ORIENT[$i]);
					$html->assign('OPTION_SELECTED','');
					if ($dbImageOrientation==$i)  {
						$html->assign('OPTION_SELECTED',' selected="selected"');
					}
					$html->parse("main.maintabs.tab_$currenttab.orientoption");
				}

				if($ItemID==0) {
					$html->parse("main.maintabs.tab_$currenttab.addbutton");
				} else {
					$html->parse("main.maintabs.tab_$currenttab.picture");
					$html->parse("main.maintabs.tab_$currenttab.savebutton");

					if($dbDeleted) $html->parse("main.maintabs.tab_$currenttab.undeletebutton");
					else $html->parse("main.maintabs.tab_$currenttab.deletebutton");
				}
				$html->parse("main.maintabs.tab_$currenttab");
			break;


			case $tabid_details:
				$setFocusForm='frmEditItem';
				$setFocusField='txtDetailName_0';

				if($ItemID!=0) {
					$rs=view_details($ItemID);
					while ($r=db_fetch_array($rs)) {
						$dbItemID = $r['ItemID'];
						$dbDetailID = htmlentities($r['DetailID']);
						$dbUnitID = $r['UnitID'];
						$dbSize = format_numbercompact($r['Size']);
						$dbEditName = htmlentities($r['DetailName']);
						$dbDisplayName = $r['DetailName'];
						$dbPrice = number_format($r['RetailPrice'],2);
						$dbPriceEx = $r['RetailPrice']*$vatrem;
						$dbWebPrice = number_format($r['WebPrice'],2);
						$dbWebPriceEx = $r['WebPrice']*$vatrem;
						$dbRRP = number_format($r['RecommendedPrice'],2);
						$dbOverridePrice = $r['OverridePrice'];
						$dbOverrideStart = strtotime($r['OverrideStart']);
						$dbOverrideEnd = strtotime($r['OverrideEnd']);
						$dbWebView = checked_value($r['WebView']);
						$dbWebBuy = checked_value($r['WebBuy']);
						$dbCatalogueView = checked_value($r['CatalogueView']);
						$dbCatalogueBuy = checked_value($r['CatalogueBuy']);
						$dbStockCode = htmlentities($r['StockCode']);
						$dbStockCodePrefix = htmlentities($r['StockCodePrefix']);
						$dbDimHeight = $r['DimHeight'];
						$dbDimWidth = $r['DimWidth'];
						$dbDimLength = $r['DimLength'];
						$dbDimWeight = $r['DimWeight'];
						$dbDimVolume = $r['DimVolume'];
						$dbDeleted = $r['DetailDeleteDate'];

						//show a different icon if an override is active
						$BeforeEnd=($dbOverrideEnd>time());
						if($BeforeEnd) {
							$html->assign('OVERRIDE_ALT', format_currency($dbOverridePrice).': '.date($OPT->longdate, $dbOverrideStart).' - '.date($OPT->longdate, $dbOverrideEnd));
							$html->assign('OVERRIDE', '1');
						} else {
							$html->assign('OVERRIDE_ALT', 'Click to add an override');
							$html->assign('OVERRIDE', '0');
						}

						//show a different icon if dimensions are set
						if(($dbDimHeight+$dbDimWidth+$dbDimLength+$dbDimWeight+$dbDimVolume)==0) {
							$html->assign('DIMS_ALT', "");
							$html->assign('DIMS', '0');
						} else {
							$alt  = "";
							$alt .= ($dbDimHeight==0) ? '' : " h:".format_numbercompact($dbDimHeight);
							$alt .= ($dbDimWidth==0) ? '' : " w:".format_numbercompact($dbDimWidth);
							$alt .= ($dbDimLength==0) ? '' : " d:".format_numbercompact($dbDimLength);
							$alt .= ($dbDimWeight==0) ? '' : " W:".format_numbercompact($dbDimWeight);
							$alt .= ($dbDimVolume==0) ? '' : " V:".format_numbercompact($dbDimVolume);
							$html->assign('DIMS_ALT', trim($alt));
							$html->assign('DIMS', '1');
						}

						$html->assign('ROW_CLASS', $dbDeleted == '0000-00-00 00:00:00' ? '' : ' class="listdeleted"');
						$html->assign('DETAIL_STOCKCODE', format_stockcode($dbStockCodePrefix,$dbStockCode));
						$html->assign('ITEM_ID', $dbItemID);
						$html->assign('DETAIL_ID', $dbDetailID);
						$html->assign('DETAIL_NAME', $dbEditName);
						$html->assign('DETAIL_SIZE', $dbSize);
						$html->assign('DETAIL_UNITSELECTOR', draw_select("selDetailUnits_$dbDetailID", 'unit', 'UnitID', 'Code', $dbUnitID));
						$html->assign('DETAIL_PRICE', $dbPrice);
						$html->assign('DETAIL_PRICEEX', format_currency($dbPriceEx,'','',''));
						$html->assign('DETAIL_WEBPRICE', $dbWebPrice);
						$html->assign('DETAIL_WEBPRICEEX', format_currency($dbWebPriceEx,'','',''));
						$html->assign('DETAIL_RRP', $dbRRP);
						$html->assign('DETAIL_VISWEB', $dbWebView);
						$html->assign('DETAIL_BUYWEB', $dbWebBuy);
						$html->assign('DETAIL_VISCAT', $dbCatalogueView);
						$html->assign('DETAIL_BUYCAT', $dbCatalogueBuy);
						$html->parse("main.maintabs.tab_$currenttab.row");

						//summary rows
						$rsb=view_detailsuppliers($dbDetailID);
						$html->assign('DETAIL_NAME', $dbDisplayName);

						//run through once to get the cheapest
						$cheapest1=0;
						$cheapest2=0;
						$cheapestprice1=0;
						$cheapestprice2=0;
						while($rb=db_fetch_array($rsb)) {
							$dbSupplierID = $rb['SupplierID'];
							$dbSupplierPrice = $rb['Price'];
							$dbBaseDiscount = $rb['BaseDiscount'];
							$dbExtraDiscount = $rb['ExtraDiscount'];
							$supplierprice1 = $dbSupplierPrice-($dbSupplierPrice*$dbBaseDiscount);
							$supplierprice2 = $dbSupplierPrice-($dbSupplierPrice*($dbBaseDiscount+$dbExtraDiscount));
							if( ($cheapestprice1==0) || ($supplierprice1<$cheapestprice1) ) {
								$cheapestprice1 = $supplierprice1;
								$cheapest1 = $dbSupplierID;
							}
							if( ($cheapestprice2==0) || ($supplierprice2<$cheapestprice2) ) {
								$cheapestprice2 = $supplierprice2;
								$cheapest2 = $dbSupplierID;
							}
						}
						//run through again and output the results
						db_data_seek($rsb,0);
						while($rb=db_fetch_array($rsb)) {
							$dbSupplierID = $rb['SupplierID'];
							$dbSupplierName = htmlentities($rb['SupplierName']);
							$dbSupplierPrice = $rb['Price'];
							$dbBaseDiscount = $rb['BaseDiscount'];
							$dbExtraDiscount = $rb['ExtraDiscount'];

							$supplierprice1 = $dbSupplierPrice-($dbSupplierPrice*$dbBaseDiscount);

							$profit1 = ($dbPriceEx)-$supplierprice1;
							$rate1 = $supplierprice1==0?0:($dbPriceEx/$supplierprice1)-1;
							$profit_web = ($dbWebPriceEx)-$supplierprice1;
							$rate_web = $supplierprice1==0?0:($dbWebPriceEx/$supplierprice1)-1;


							$html->assign('SUPPLIER_NAME', $dbSupplierName);
							$html->assign('SUPPLIER_PRICE1', format_currency($supplierprice1,'','',''));
							$html->assign('SUPPLIER_PRICE2', format_currency($supplierprice2,'','',''));
							$html->assign('PROFIT_SHOP', format_currency($profit1,'','',''));
							$html->assign('RATE_SHOP', format_percentage($rate1,'','','%',0));
							if($rate1 < 0) {
								$html->assign('RATE_SHOP_WARNING', 'warning');
							} else {
								$html->assign('RATE_SHOP_WARNING', '');
							}
							
							$html->assign('PROFIT_WEB', format_currency($profit_web,'','',''));
							$html->assign('RATE_WEB', format_percentage($rate_web,'','','%',0));
							if($rate_web < 0) {
								$html->assign('RATE_WEB_WARNING', 'warning');
							} else {
								$html->assign('RATE_WEB_WARNING', '');
							}

							//$supplierprice2 = $dbSupplierPrice-($dbSupplierPrice*($dbBaseDiscount+$dbExtraDiscount));
							//$profit2 = ($dbPriceEx)-$supplierprice2;
							//$rate2 = $supplierprice1==0?0:($dbPriceEx/$supplierprice2)-1;
							//$html->assign('PROFIT_SHOP2', format_currency($profit2,'','',''));
							//$html->assign('RATE_SHOP2', format_percentage($rate2,'','','%',0));

							$html->assign('CHEAPEST_PRICE1', ($dbSupplierID==$cheapest1)?'bold':'');
							//$html->assign('CHEAPEST_PRICE2', ($dbSupplierID==$cheapest2)?'bold':'');

							$html->parse("main.maintabs.tab_$currenttab.summary.row");
						}
						$html->assign('ROW_CLASS', ' class="sepparator"');

					}
				}

				//new detail row
				$html->assign('DETAIL_UNITSELECTOR', draw_select('selDetailUnits_0', 'unit', 'UnitID', 'Code', $dbUnitID));
				$html->assign('INPUT_SUBMIT', draw_inputimage("imgSubmitDetails", 'cmd_save', 45, 20, 'Save'));

				$html->parse("main.maintabs.tab_$currenttab.summary");
				$html->parse("main.maintabs.tab_$currenttab");
			break;


			case $tabid_categories:
				if($ItemID!=0) {
					$rs=view_itemcategories($ItemID);
					$html->assign('CATEGORY_LABEL', 'Categories:');
					while ($r=db_fetch_array($rs)) {
						$dbChildCategoryID = $r['ChildCategoryID'];
						$dbParentName=$r['CatParentName'];
						$dbChildName=$r['CatChildName'];

						if(is_null($dbParentName)) $dbParentName='Top';
						$dbCatName = htmlentities($dbParentName . ' :: ' . $dbChildName);
						$dbDeleteDate = $r['DeleteDate'];
						$dbPrimary = checked_value($r['IsPrimary']);

						$html->assign('ITEM_ID', $ItemID);
						$html->assign('CATEGORY_ID', $dbChildCategoryID);
						$html->assign('CATEGORY_SELECTOR', $dbCatName);
						$html->assign('CATEGORY_PRIMARY', $dbPrimary);

						if($dbDeleteDate==0)
							$html->parse("main.maintabs.tab_$currenttab.cat.objectnotdeleted");
						else
							$html->parse("main.maintabs.tab_$currenttab.cat.objectdeleted");

						if($dbPrimary)
							$html->parse("main.maintabs.tab_$currenttab.cat.primary");
						else
							$html->parse("main.maintabs.tab_$currenttab.cat.notprimary");

						$html->parse("main.maintabs.tab_$currenttab.cat");
						$html->assign('CATEGORY_LABEL', '&nbsp;');
					}
				}
				$html->assign('CATEGORY_SELECTOR', draw_selectcategory('selAddCategory', -1, '-- Select a category --'));
				$html->parse("main.maintabs.tab_$currenttab");
			break;

			case $tabid_suppliers:
				//show all item details (and keep a track of the detail id's for later
				$details=array();
				if($ItemID!=0) {
					$rs=view_details($ItemID);
					while ($r=db_fetch_array($rs)) {
						$dbItemID = $r['ItemID'];
						$dbDetailID = htmlentities($r['DetailID']);
						$dbEditName = $r['DetailName'];
						$details[$dbDetailID] = $dbEditName;

						$html->assign('ITEM_ID', $dbItemID);
						$html->assign('DETAIL_ID', $dbDetailID);
						$html->assign('DETAIL_NAME', $dbEditName);

						//for each detail, show any supplier info
						$rsb=view_detailsuppliers($dbDetailID);
						while($rb=db_fetch_array($rsb)) {
							$dbSupplierID = $rb['SupplierID'];
							$dbSupplierName = htmlentities($rb['SupplierName']);
							$dbSupplierPrice = $rb['Price'];
							$dbBaseDiscount = $rb['BaseDiscount'];
							$dbExtraDiscount = $rb['ExtraDiscount'];

							$html->assign('SUPPLIER_ID', $dbSupplierID);
							$html->assign('SUPPLIER_NAME', $dbSupplierName);
							$html->assign('SUPPLIER_PRICE', format_currency($dbSupplierPrice,'','',''));
							$html->assign('SUPPLIER_BASEDISC',  format_percentage($dbBaseDiscount,'','',''));
							$html->assign('SUPPLIER_EXTRADISC', format_percentage($dbExtraDiscount,'','',''));
							$html->assign('DETAIL_PRICE_BASE',  format_currency($rb['Price']-($rb['Price'] * $rb['BaseDiscount'])));
							$html->assign('DETAIL_PRICE_EXTRA',  format_currency($rb['Price']-($rb['Price'] * ($rb['BaseDiscount']+$rb['ExtraDiscount']))));
							$html->parse("main.maintabs.tab_$currenttab.row");
							$html->assign('DETAIL_NAME', '&nbsp;');
						}
						$html->parse("main.maintabs.tab_$currenttab.row.spacer");
					}
				}
				//a space to add new supplier
				$html->assign('DETAIL_SELECTOR', draw_arrayselect('selNewDetailID', $details, 0, 'select a detail'));
				$html->assign('SUPPLIER_SELECTOR', draw_select("selNewSupplierID", 'supplier', 'SupplierID', 'SupplierName', 0, 'select supplier'));
				$html->assign('INPUT_SUBMIT', draw_inputimage("imgSubmitSuppliers", 'cmd_save', 45, 20, 'Save'));

				$html->parse("main.maintabs.tab_$currenttab");
			break;

			case $tabid_postage:
				//show all item details (and keep a track of the detail id's for later
				$details=array();
				if($ItemID!=0) {
					$rs=view_details($ItemID);
					while ($r=db_fetch_array($rs)) {
						$dbItemID = $r['ItemID'];
						$dbDetailID = $r['DetailID'];
						$dbDetailName = htmlentities($r['DetailName']);

						$details[$dbDetailID] = $dbDetailName;

						$html->assign('ITEM_ID', $dbItemID);
						$html->assign('DETAIL_ID', $dbDetailID);
						$html->assign('DETAIL_NAME', $dbDetailName);

						//for each detail, show any supplier info
						$rsb=view_detailpostagecharges($dbDetailID);
						while($rb=db_fetch_array($rsb)) {
							$dbPostageChargeID = $rb['PostageChargeID'];
							$dbPostageChargeName = htmlentities($rb['PostageChargeName']);
							$dbCharge = format_currency($rb['Charge']);
							$dbPallet = $rb['PalletThreshold'];

							$html->assign('POSTAGECHARGE_ID', $dbPostageChargeID);
							$html->assign('POSTAGECHARGE_NAME', $dbPostageChargeName);
							$html->assign('POSTAGECHARGE_CHARGE', $dbCharge);
							$html->assign('POSTAGECHARGE_PALLET', $dbPallet);
							$html->parse("main.maintabs.tab_$currenttab.row");
							$html->assign('DETAIL_NAME', '&nbsp;');
						}
					}
				}
				//a space to add new supplier
				$html->assign('POSTAGECHARGE_PALLET', '');
				$html->assign('POSTAGECHARGE_CHARGE', '');
				$html->assign('DETAIL_SELECTOR', draw_arrayselect('selNewPostageChargeDetailID', $details, 0, 'select a detail'));
				$html->assign('POSTAGECHARGE_SELECTOR', draw_select("selNewPostageCharge_$dbDetailID", 'postagecharge', 'PostageChargeID', 'PostageChargeName', 0, 'select postage charge'));

				$html->parse("main.maintabs.tab_$currenttab");
			break;



			case $tabid_pictures:
				$html->assign('ITEM_ID', $ItemID);

				//list current pictures
				$rs=view_itempictures($ItemID);
				while($r=db_fetch_array($rs)) {
					$dbItemPictureID = $r['ItemPictureID'];
					$dbPictureName = $r['PictureName'];
					$dbPrimary = $r['IsPrimary'];
					$dbDetailName = $r['DetailName'];
					$dbImageFileName = $r['FileName'];
					$html->assign('ITEMPICTURE_ID', $dbItemPictureID);
					$html->assign("IMAGE", img_safe($OPT->productimageroot.$dbImageFileName, $dbDetailName));
					$html->assign('DETAIL_NAME', $dbDetailName);
					$html->assign('PICTURE_NAME', $dbPictureName);

					if($dbPrimary)
						$html->parse("main.maintabs.tab_$currenttab.row.primary");
					else
						$html->parse("main.maintabs.tab_$currenttab.row.notprimary");

					$html->parse("main.maintabs.tab_$currenttab.row");
				}

				//make a select widget for details
				$details=array();
				$rs=view_details($ItemID);
				while($r=db_fetch_array($rs)) {
					$dbDetailID = $r['DetailID'];
					$dbDetailName = $r['DetailName'];

					$details[$dbDetailID]=$dbDetailName;
				}
				$html->assign('DETAIL_SELECTOR', draw_arrayselect('selNewPictureDetailID', $details, 0, '(any)'));
				$html->assign('PICTURE_SELECTOR', draw_selectpicture('selNewPicture', 0, 'select picture'));
				$html->parse("main.maintabs.tab_$currenttab.newrow");
				$html->parse("main.maintabs.tab_$currenttab");
			break;

			case $tabid_stock:
				$setFocusForm='frmEditItem';
				$setFocusField='selNewStockDetailID_0';

				//draw existing stock lines
				$tab = 1000;
				$stockiddetails = array();
				$rs=view_stock($ItemID);
				while($r=db_fetch_array($rs)) {
					$stockiddetails[$r['StockID']]=$r['DetailID'];
					$html->assign('STOCK_ID', $r['StockID']);
					$html->assign('DETAIL_NAME', $r['DetailName']);
					$html->assign('SUPPLIER_NAME', $r['SupplierName']);
					$html->assign('DATE', format_datetime($r['DeliveryDate'],$OPT->longdate));
					$html->assign('PRICE', $r['PricePaid']);
					$html->assign('QUANTITY', $r['QuantityPurchased']);
					$html->assign('REMAINING', $r['QuantityRemaining']);
					$html->assign('TAB_INDEX', $tab++);
					$html->parse("main.maintabs.tab_$currenttab.historyrow");
				}


				//make a select widget for new details
				$details=array();
				$rs=view_details($ItemID);
				while($r=db_fetch_array($rs)) {
					$dbDetailID = $r['DetailID'];
					$dbDetailName = $r['DetailName'];

					$details[$dbDetailID]=$dbDetailName;
				}
				$html->assign('DETAIL_SELECTOR', draw_arrayselect('selNewStockDetailID_0', $details, 0, 'select item', 1));
				$html->assign('SUPPLIER_SELECTOR', draw_select("selNewStockSupplierID_0", 'supplier', 'SupplierID', 'SupplierName', 0, 'select supplier', 2));


				//draw current stock levels
				$tab = 10;
				$rs=view_details($ItemID);
				while($r=db_fetch_array($rs)) {
					//$jcmd = '';
					//$jcmd = "MM_changeProp('d1','','style.backgroundColor','#ffffaa','DIV');";
					$html->assign('DETAIL_ID', $r['DetailID']);
					$html->assign('DETAIL_NAME', $r['DetailName']);
					$html->assign('REMAINING', $r['StockRemaining']);
					$html->assign('VALUE', format_currency($r['StockValue']));
					$html->assign('TAB_INDEX', $tab++);
					$html->parse("main.maintabs.tab_$currenttab.currentrow");
				}

				//end
				$html->assign('INPUT_SUBMIT', draw_inputimage("imgSubmitStock", 'cmd_save', 45, 20, 'Save', 6));
				$html->parse("main.maintabs.tab_$currenttab");
			break;
			
			
			case $tabid_links:
				if($ItemID!=0) {
					$html->assign('LINK_LABEL', 'Links:');
					
					//draw current links
					$rs = view_itemsrelated($ItemID);
					while($r = db_fetch_array($rs)) {
						$dbRelatedItemName = htmlentities($r['ItemName']);
						$dbRelatedImageFileName = $r['Filename'];
						$dbLinkID= $r['ItemID'];
						
						$html->assign('LINK_ID', $dbLinkID);
						$html->assign('ITEM_SELECTOR', $dbRelatedItemName);
						$html->assign('LINK_IMAGE', img_safe($OPT->productimageroot.$dbRelatedImageFileName, $dbRelatedItemName, 'border', "linkimg_$dbLinkItemID", 50));
						$html->parse("main.maintabs.tab_$currenttab.link");
						$html->assign('LINK_LABEL', '&nbsp;');
					}
				}
				$html->assign('ITEM_SELECTOR', draw_selectitem('selAddLink', -1, '-- Select an item --'));
				$html->parse("main.maintabs.tab_$currenttab");
			break;

		}
		if($ItemID!=0) $html->parse('main.maintabs.moretabs');


		//show "missing photo" icon?
		if(empty($dbPictureID)) {
	 		$html->assign('ALERT_ICON', img_general('label_no_photo', 24, 22, "No Picture"));
			$html->parse('main.maintabs.alert');
		}
		$missing = itemMissingRRP($ItemID);
		for($i=0;$i<$missing;$i++) {
	 		$html->assign('ALERT_ICON', img_general('label_no_price', 19, 22, "No RRP"));
			$html->parse('main.maintabs.alert');
		}
		$missing = itemMissingSupplier($ItemID);
		for($i=0;$i<$missing;$i++) {
	 		$html->assign('ALERT_ICON', img_general('label_no_supplier', 28, 22, "No Supplier"));
			$html->parse('main.maintabs.alert');
		}
		$missing = itemMissingSupplierPrice($ItemID);
		for($i=0;$i<$missing;$i++) {
	 		$html->assign('ALERT_ICON', img_general('label_no_supplierprice', 28, 22, "No Supplier Price"));
			$html->parse('main.maintabs.alert');
		}

		$html->parse("main.maintabs");

	} //end popup if

	//echo(fancy_vardump($_POST));
?>
