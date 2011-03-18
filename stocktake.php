<?php

	require_https();

	$rowsonpage = 0;
	$maxrowsonpage = 40;

	//form handling
	$parent_catid=-1;
	if(count($_POST)>0) {
    $parent_catid = httppost_int('category_selector', -1);

    if(strlen($_POST['cmdSelectCategory_x']) > 0) {
      // category-select was used, don't update anything
      echo("changing category<br />");
      
    } else {
  		echo "Processing form submission...<br><ul>";
  
  		foreach($_POST as $key=>$value) {
  			$subfieldname = substr($key, 0, strpos($key, '_'));
  			$subfieldid = substr($key, strpos($key, '_')+1);
  
  			if( ($subfieldname=='txtnewstocklevel') && ($value!='') ) {
  				$subsupplierid = httppost_int("selSupplier_$subfieldid");
  
  				$value = intval($value);
  				echo "<li>New level specified for item $subfieldid, supplier $subsupplierid: $value</li>";
  				SetStockLevel($subfieldid, $value, $subsupplierid);
  			}
  		}
  		echo "</ul>Form Complete<br>";
  	}
	}


  //Populate the category-selector
  $html->assign('CATEGORY_SELECTOR', draw_selectcategory('category_selector', $parent_catid, '', 0));
	$html->assign('SELECT_BUTTON', draw_inputimage("cmdSelectCategory", 'cmd_show', 45, 20, 'Go'));


  if($parent_catid >= 0) {
  	//List the categories
  	$tabid = 0;

  	$defaultsupplier = GetDefaultSupplierID();
  	$total_grand = 0;
  
    $cat1_details = db_fetch_array(view_categorydetails($parent_catid));
    
		$dbcat1id   = $cat1_details['CategoryID'];
		$dbcat1name = $cat1_details['CategoryName'];
		$total_cat = 0;

		$html->assign('CAT1_NAME', $dbcat1name);

		//List the sub categories
		$rscat2 = view_categories($dbcat1id);
		while($rcat2 = db_fetch_array($rscat2)) {
			$dbcat2id   = $rcat2['CategoryID'];
			$dbcat2name = $rcat2['CategoryName'];
			$total_subcat = 0;

			$html->assign('CAT2_NAME', $dbcat2name);
			$html->assign('WIDGET_SAVE', draw_inputimage("cmdSave_$dbcat2id", 'cmd_save_inv', 45, 20, 'save', $tabid++));

			//List the items
			$rsitem = view_items($dbcat2id);
			while($ritem = db_fetch_array($rsitem)) {
				$dbitemid = $ritem['ItemID'];
				$dbitemname = $ritem['ItemName'];

				$html->assign('ITEM_ID', $dbitemid);
				$html->assign('ITEM_NAME', $dbitemname);

				//List the details
				$rsdetail = view_details($dbitemid);
				while($rdetail = db_fetch_array($rsdetail)) {
					$rowsonpage++;
					$dbdetailid = $rdetail['DetailID'];
					$dbdetailname = $rdetail['DetailName'];
					$dbdetailstock = $rdetail['StockRemaining'];
					$dbdetailvalue = $rdetail['StockValue'];
					$total_grand += $dbdetailvalue;
					$total_cat += $dbdetailvalue;
					$total_subcat += $dbdetailvalue;

					$suppliers = array();
					$cheapestid = 0;
					$cheapestprice = 0;

					$rssupplier = view_detailsuppliers($dbdetailid);

					if(db_num_rows($rssupplier)==0) {
						//no suppliers found
						//echo(".");
					} else {
						while($rsupplier = db_fetch_array($rssupplier)) {
							$dbsupplierid = $rsupplier['SupplierID'];
							$dbsuppliername = $rsupplier['SupplierName'];
							$dbprice = $rsupplier['Price'];
							$dbbase =  $rsupplier['BaseDiscount'];
							$value = $dbprice - ($dbprice*$dbbase);

							$suppliers[$dbsupplierid] = $dbsuppliername;

							if( ($cheapestid==0) || ($value<$cheapestprice) ) {
								$cheapestid = $dbsupplierid;
								$cheapestprice = $value;
							}
						}
					}

					$html->assign('DETAIL_ID', $dbdetailid);
					$html->assign('STOCKCODE', getStockCode($dbdetailid));
					$html->assign('DETAIL_NAME', $dbdetailname);
					$html->assign('CURRENT_STOCK', format_numbercompact($dbdetailstock));
					$html->assign('CURRENT_VALUE', format_currency($dbdetailvalue));
					$html->assign('TABINDEX', $tabid++);
					if(count($suppliers)==0) {
						$html->assign('WIDGET_SUPPLIER', 'no suppliers');
					} else {
						$html->assign('WIDGET_SUPPLIER', draw_arrayselect("selSupplier_$dbdetailid", $suppliers, $cheapestid, '', $tabid++));
					}
					$html->parse('main.mainlist.category.row');
					if($bailout++ == 100) break 2;
				}

				//sub-total for the sub-category
				$html->assign('TOTAL_CATEGORY', format_currency($total_subcat));

			}

			//sub-total for the category
			$html->assign('TOTAL_MAINCATEGORY', format_currency($total_cat));
			$html->parse('main.mainlist.category.maintotal');

			//do we need a page-break
			$html->assign('SEPP_STYLE', '');
			$html->assign('SEPP_TEXT', '');
			if($rowsonpage>=$maxrowsonpage) {
				$html->assign('SEPP_STYLE', 'style="page-break-before: always;"');
				$html->assign('SEPP_TEXT', '');
				$rowsonpage = 0;
			}

			$html->parse('main.mainlist.category');
		}

		//grand-total
		$html->assign('TOTAL_GRAND', format_currency($total_grand));
			$html->parse('main.mainlist');

  }	

?>