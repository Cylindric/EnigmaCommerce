<?php
	/* ----------------------------------------------------------------
		    Filename:

		Requirements:

		       Notes:
	------------------------------------------------------------------- */
	require_once('application.php');

	require_https();
	require_login();

	// ----------
	// Initialise
	// ----------
	function WizDetailSuppliers($needle) {
		$returnval=array();
		foreach($_SESSION['wiz_supplierdetail'] as $wizsupplierid=>$detailid) {
			if($detailid==$needle) {
				array_push($returnval, $wizsupplierid);
			}
		}
		return $returnval;
	}

	function ClearSession() {
		$_SESSION['wiz_status'] = 'Running New Item Wizard';
		$_SESSION['wiz_page'] = 1;
		$_SESSION['wiz_itemname'] = '';
		$_SESSION['wiz_itemdescription'] = '';
		$_SESSION['wiz_itemrecommended'] = false;
		$_SESSION['wiz_itemvisible'] = true;
		$_SESSION['wiz_itemarticle'] = '';
		$_SESSION['wiz_category'] = array();
		$_SESSION['wiz_detailname'] = array();
		$_SESSION['wiz_detailsize'] = array();
		$_SESSION['wiz_detailunit'] = array();
		$_SESSION['wiz_detailrrp'] = array();
		$_SESSION['wiz_detailprice'] = array();
		$_SESSION['wiz_detailfinalid'] = array();
		$_SESSION['wiz_supplier'] = array();
		$_SESSION['wiz_suppliername'] = array();
		$_SESSION['wiz_supplierdetail'] = array();
		$_SESSION['wiz_supplierprice'] = array();
		$_SESSION['wiz_supplierrate1'] = array();
		$_SESSION['wiz_supplierrate2'] = array();

		if(!empty($_SESSION['subcatid'])) {
			array_push($_SESSION['wiz_category'], $_SESSION['subcatid']);
		}
	}

	unset($_SESSION['postreloadpage']);
	$page=1;
	$nextpage=1;
	$abandon=false;
	if(!isset($_SESSION['wiz_status'])) {
		ClearSession();
	}


	// ------------
	// Process Form
	// ------------
	if(count($_POST)>0) {

		$page = httppost_int('currentpage');
		$nextpage = $page;
		$finish=false;

		//work out which (if any) main button was pressed
		if(!empty($_POST['cmdNext'])) {
			$nextpage = $page+1;
		} elseif (!empty($_POST['cmdBack'])) {
			$nextpage = $page-1;
		} elseif (!empty($_POST['cmdFinish'])) {
			$nextpage = $page+1;
			$finish=true;
		} elseif (!empty($_POST['cmdCancel'])) {
			ClearSession();
			reload_page();
		}

		switch($page) {
			case 1:
				if(empty($_POST['txtItemName'])) {
					$nextpage = $currentpage;
				} else {
					$_SESSION['wiz_itemname']        = httppost_string('txtItemName');
					$_SESSION['wiz_itemdescription'] = httppost_string('txtItemDescription');
					$_SESSION['wiz_itemarticle']     = httppost_string('txtItemArticle');
					$_SESSION['wiz_itemrecommended'] = httppost_int('chkItemRecommended');
					$_SESSION['wiz_itemvisible']     = httppost_int('chkItemVisible');
				}
			break;

			case 2:
				//add new category
				if(isset($_POST['imgAddCategory_x'])) {
					array_push($_SESSION['wiz_category'], httppost_int('selAddCategory'));
					$_SESSION['wiz_category'] = array_unique($_SESSION['wiz_category']);
				}

				//delete an existing category
				foreach($_SESSION['wiz_category'] as $key=>$value) {
					if(isset($_POST["imgDelCategory_{$value}_x"])) {
						unset($_SESSION['wiz_category'][$key]);
						break;
					}
				}
			break;

			case 3:
				//add new detail
				if(isset($_POST['imgAddDetail_x'])) {
					//if name is empty, assume next-button was intended
					if(empty($_POST['txtDetailName'])) {
						$nextpage = $page+2; //skipping Suppliers, so +2
					} else {
						array_push($_SESSION['wiz_detailname'],  httppost_string('txtDetailName'));
						array_push($_SESSION['wiz_detailsize'],  httppost_num('txtDetailSize'));
						array_push($_SESSION['wiz_detailunit'],  httppost_int('selDetailUnit'));
						array_push($_SESSION['wiz_detailrrp'],   httppost_num('txtDetailRRP'));
						array_push($_SESSION['wiz_detailprice'], httppost_num('txtDetailRRP'));
						array_push($_SESSION['wiz_detailfinalid'], 0);
					}
				}

				//delete an existing detail
				foreach($_SESSION['wiz_detailname'] as $key=>$value) {
					if(isset($_POST["imgDelDetail_{$key}_x"])) {
						unset($_SESSION['wiz_detailname'][$key]);
						unset($_SESSION['wiz_detailsize'][$key]);
						unset($_SESSION['wiz_detailunit'][$key]);
						unset($_SESSION['wiz_detailrrp'][$key]);
						unset($_SESSION['wiz_detailprice'][$key]);
						unset($_SESSION['wiz_detailfinalid'][$key]);
						break;
					}
				}
			break;

			case 4:
				//add new supplier link
				if(isset($_POST['imgAddSupplier_x'])) {
					//if item or supplier is not selected, assume next-button was intended
					if((httppost_int('selDetail')==-1) || (httppost_int('selSupplier')==0) ) {
						$nextpage = $page+1;
					} else {
						array_push($_SESSION['wiz_supplier'], httppost_int('selSupplier'));
						array_push($_SESSION['wiz_supplierdetail'], httppost_int('selDetail'));
						array_push($_SESSION['wiz_supplierprice'], httppost_num('txtSupplierPrice'));
						array_push($_SESSION['wiz_supplierrate1'], httppost_num('txtSupplierRate1')/100);
						array_push($_SESSION['wiz_supplierrate2'], httppost_num('txtSupplierRate2')/100);
						array_push($_SESSION['wiz_suppliername'], GetSupplierName(httppost_int('selSupplier')));
					}
				}

				//delete an existing supplier link
				foreach($_SESSION['wiz_supplier'] as $key=>$value) {
					if(isset($_POST["imgDelSupplier_{$key}_x"])) {
						unset($_SESSION['wiz_supplier'][$key]);
						unset($_SESSION['wiz_supplierdetail'][$key]);
						unset($_SESSION['wiz_suppliername'][$key]);
						unset($_SESSION['wiz_supplierprice'][$key]);
						unset($_SESSION['wiz_supplierrate1'][$key]);
						unset($_SESSION['wiz_supplierrate2'][$key]);
						break;
					}
				}
			break;

			case 5:
				//check for any new prices
				foreach($_SESSION['wiz_detailname'] as $key=>$value) {
					if(isset($_POST["txtDetailPrice_$key"])) {
						$_SESSION['wiz_detailprice'][$key]=$_POST["txtDetailPrice_$key"];
					}
				}
			break;

		}

		if($finish) {
			//add everything to the database!
			//item
			$itemid = spInsertItemGeneral($_SESSION['wiz_itemname'], $_SESSION['wiz_itemdescription']);
			spUpdateItemGeneral($itemid, $_SESSION['wiz_itemname'], $_SESSION['wiz_itemdescription'], $_SESSION['wiz_itemrecommended'],0, $_SESSION['wiz_itemarticle']);

			//categories (first one added is the primary)
			$isprimary = true;
			foreach($_SESSION['wiz_category'] as $key=>$value) {
				spInsertItemCategory($itemid, $value, $isprimary);
				$isprimary = false;
			}

			//details
			foreach($_SESSION['wiz_detailname'] as $key=>$value) {
				$_SESSION['wiz_detailfinalid'][$key] = spInsertItemDetails($itemid, $value,  $_SESSION['wiz_detailsize'][$key], $_SESSION['wiz_detailunit'][$key], $_SESSION['wiz_detailprice'][$key], $_SESSION['wiz_detailrrp'][$key], 1, 1, 1, 1);
				spInsertDetailSupplier($_SESSION['wiz_detailfinalid'][$key], 1, 0, 0, 0);
			}

			//suppliers
			foreach($_SESSION['wiz_supplier'] as $key=>$value) {
				$detailptr=$_SESSION['wiz_supplierdetail'][$key];
				$detailid=$_SESSION['wiz_detailfinalid'][$detailptr];
				spInsertDetailSupplier($detailid, $value, $_SESSION['wiz_supplierprice'][$key], $_SESSION['wiz_supplierrate1'][$key], $_SESSION['wiz_supplierrate2'][$key]);
			}
			
			//visibility
			if($_SESSION['wiz_itemvisible']!=0) {
				spSetItemVisibility($itemid, 1);
			}

			ClearSession();
		}
		//reload_page();
	}






	// --------------
	// Prepare Output
	// --------------
	$page=$nextpage;
	$setFocusForm='frmwiznewitem';
	$setFocusField='';

	//start_page($html,'hwizard_new_item.htmlead');

	$html->assign('PAGE', $page);

	switch ($page) {
		case 1:
			$html->assign('FRAME', '_f2');
			$setFocusField='txtItemName';

			//new item fields
			$html->assign('FRAME', '');
			$html->assign('ITEM_NAME',        $_SESSION['wiz_itemname']);
			$html->assign('ITEM_DESCRIPTION', $_SESSION['wiz_itemdescription']);
			$html->assign('ITEM_RECOMMENDED', $_SESSION['wiz_itemrecommended'] ? ' checked="checked"' : '');
			$html->assign('ITEM_VISIBLE',     $_SESSION['wiz_itemvisible'] ? ' checked="checked"' : '');
			$html->assign('ITEM_ARTICLE',     $_SESSION['wiz_itemarticle']);
			$html->parse('main.btn_cancel');
			$html->parse('main.btn_next');
		break;

		case 2:
			$html->assign('FRAME', '_f2');
			$setFocusField='cmdNext';

			//show existing cats
			foreach($_SESSION['wiz_category'] as $value) {
				$rs = view_categorydetails($value);
				$r = db_fetch_array($rs);
				$dbcatname = $r['CategoryName'];
				$rs = view_categorydetails($r['ParentID']);
				$r = db_fetch_array($rs);
				$dbcatname = $r['CategoryName'] . ' :: ' . $dbcatname;
				$html->assign('CATEGORY_NAME', $dbcatname);
				$html->assign('INPUT_DELCAT', draw_inputimage("imgDelCategory_$value",'button_delete', 16, 16, 'Delete'));
				$html->parse("main.page_$page.row");
			}

			//new cat
			$html->assign('CATEGORY_SELECTOR', draw_selectcategory('selAddCategory', -1, '-- Select a category --'));
			$html->assign('INPUT_ADDCAT', draw_inputimage('imgAddCategory','button_add', 16, 16, 'Add'));

			$html->parse('main.btn_cancel');
			$html->parse('main.btn_back');
			if(count($_SESSION['wiz_category'])>0) $html->parse('main.btn_next');
		break;

		case 3:
			$html->assign('FRAME', '_f3');
			$setFocusField='txtDetailName';

			//existing details
			foreach($_SESSION['wiz_detailname'] as $key=>$value) {
				$html->assign('DETAIL_NAME', $value);
				$html->assign('DETAIL_SIZE', $_SESSION['wiz_detailsize'][$key]);
				$html->assign('DETAIL_UNIT', GetUnitName($_SESSION['wiz_detailunit'][$key]));
				$html->assign('DETAIL_RRP', format_currency($_SESSION['wiz_detailrrp'][$key]));
				$html->assign('INPUT_DELDETAIL', draw_inputimage("imgDelDetail_$key", 'button_delete', 16, 16, 'Delete'));
				$html->parse("main.page_$page.row");
			}

			//new detail
			$html->assign('DETAIL_NAME', '');
			$html->assign('DETAIL_SIZE', '');
			$html->assign('DETAIL_UNIT', '');
			$html->assign('DETAIL_RRP', '');
			$html->assign('UNIT_SELECTOR', draw_select('selDetailUnit', 'unit', 'UnitID', 'Code', 0,'',3));
			$html->assign('INPUT_ADDDETAIL', draw_inputimage('imgAddDetail', 'button_add', 16, 16, 'Add', 4));

			$html->parse('main.btn_cancel');
			$html->parse('main.btn_back');
			if(count($_SESSION['wiz_detailname'])>0) $html->parse('main.btn_next');
		break;

		case 4:
			$html->assign('FRAME', '_f4');
			$setFocusField='selDetail';

			//existing supplier links
			foreach($_SESSION['wiz_supplier'] as $key=>$value) {
				$detailid=$_SESSION['wiz_supplierdetail'][$key];
				$detailname=$_SESSION['wiz_detailname'][$detailid];

				$html->assign('SUPPLIER_DETAIL', $detailname);
				$html->assign('SUPPLIER_NAME', $_SESSION['wiz_suppliername'][$key]);
				$html->assign('SUPPLIER_PRICE', format_currency($_SESSION['wiz_supplierprice'][$key]));
				$html->assign('SUPPLIER_RATE1', format_percentage($_SESSION['wiz_supplierrate1'][$key]));
				$html->assign('SUPPLIER_RATE2', format_percentage($_SESSION['wiz_supplierrate2'][$key]));
				$html->assign('INPUT_DELSUPPLIER', draw_inputimage("imgDelSupplier_$key", 'button_delete', 16, 16, 'Delete'));
				$html->parse("main.page_$page.existing.row");
			}
			if(count($_SESSION['wiz_supplier'])>0) {
				$html->parse("main.page_$page.existing");
			}


			//new supplier link
			$html->assign('DETAIL_SELECTOR', draw_arrayselect('selDetail', $_SESSION['wiz_detailname'], -1, 'Select Item', 1));
			$html->assign('SUPPLIER_SELECTOR', draw_select('selSupplier', 'supplier', 'SupplierID', 'SupplierName', 0, 'Select Supplier', 2));
			$html->assign('SUPPLIER_PRICE', 0);
			$html->assign('SUPPLIER_RATE1', 0);
			$html->assign('SUPPLIER_RATE2', 0);
			$html->assign('INPUT_ADDSUPPLIER', draw_inputimage("imgAddSupplier", 'button_add', 16, 16, 'Add', 6));

			$html->parse('main.btn_cancel');
			$html->parse('main.btn_back');
			$html->parse('main.btn_next');
		break;


		case 5:
			$html->assign('FRAME', '_f5');
			$setFocusField='';

			//existing detail prices
			foreach($_SESSION['wiz_detailname'] as $detailid=>$detailname) {
				$ourprice=$_SESSION['wiz_detailprice'][$detailid];
				$ourpriceex=$_SESSION['wiz_detailprice'][$detailid]/$vatadd;
				$rrp=$_SESSION['wiz_detailrrp'][$detailid];

				if(empty($setFocusField)) {
					$setFocusField="txtDetailPrice_$detailid";
				}
				$html->assign('DETAIL_ID', $detailid);
				$html->assign('DETAIL_NAME', $detailname);
				$html->assign('DETAIL_RRP', format_currency($rrp,'','',''));
				$html->assign('DETAIL_PRICE', format_currency($ourprice,'','',''));
				$html->assign('DETAIL_PRICEEX', format_currency($ourpriceex,'','',''));

				$suppliers=WizDetailSuppliers($detailid);

				foreach($suppliers as $id=>$supplierid) {
					$name=$_SESSION['wiz_suppliername'][$supplierid];
					$supplierprice=$_SESSION['wiz_supplierprice'][$supplierid];
					$rate1=$_SESSION['wiz_supplierrate1'][$supplierid];
					$rate2=$_SESSION['wiz_supplierrate2'][$supplierid];
					$price1=(1-$rate1)*$supplierprice;
					$price2=(1-$rate1-$rate2)*$supplierprice;

					$profit1=($ourpriceex)-$price1;
					$profit2=($ourpriceex)-$price2;
					$percent1=0;
					$percent2=0;
					if($price1!=0) $percent1 = $profit1/$price1;
					if($price2!=0) $percent2 = $profit2/$price2;

					$html->assign('SUPPLIER_NAME', $name);
					$html->assign('SUPPLIER_PRICE1', format_currency($price1,'','',' '));
					$html->assign('SUPPLIER_PRICE2', format_currency($price2,'','',' '));
					$html->assign('SUPPLIER_PROFIT1', format_currency($profit1,'','',' '));
					$html->assign('SUPPLIER_PROFIT2', format_currency($profit2,'','',' '));
					$html->assign('SUPPLIER_PERCENT1', format_percentage($percent1,0));
					$html->assign('SUPPLIER_PERCENT2', format_percentage($percent2,0));
					$html->parse("main.page_$page.row.price1");
					$html->parse("main.page_$page.row.price2");
				}
				$html->parse("main.page_$page.row");
				$html->assign('DETAIL_NAME', '');
				$html->assign('SUPPLIER_NAME', '');
			}


			$html->parse('main.btn_cancel');
			$html->parse('main.btn_back');
			$html->parse('main.btn_apply');
			$html->parse('main.btn_finish');
		break;

		case 6:
			$html->assign('FRAME', '_f6');
			$setFocusField='cmdClose';
			$html->parse('main.btn_close');
		break;
	}

	//show progress messages
	$notes = array();

	if(!empty($_SESSION['wiz_itemname'])) {
		array_push($notes, $_SESSION['wiz_itemname']);
	}
	$count=count($_SESSION['wiz_category']);
	if($count>0) {
		array_push($notes, "$count categor". (($count>1) ? 'ies': 'y'));
	}
	$count=count($_SESSION['wiz_detailname']);
	if($count>0) {
		array_push($notes, "$count detail". (($count>1) ? 's': ''));
	}
	$count=count($_SESSION['wiz_supplier']);
	if($count>0) {
		array_push($notes, "$count supplier link". (($count>1) ? 's': ''));
	}

	foreach($notes as $value) {
		$html->assign('PROGRESS', $value);
		$html->parse('main.progress.row');
	}
	if(count($notes)>0) $html->parse('main.progress');


	$html->parse("main.page_$page");


	if(!empty($setFocusForm) ) {
		$focuscmd="onLoad=\"SetFocus('$setFocusForm','$setFocusField');\"";
	} else {
		$focuscmd='';
	}
	$html->assign('AUTO_FOCUS', $focuscmd);

	//end_page($html);
?>