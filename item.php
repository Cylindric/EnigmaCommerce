<?php
	/* ----------------------------------------------------------------
		    Filename: item.php

		Requirements: $_SESSION['itemid']

		       Notes: This page retrieves the basic information for the
		              item specified by the session variable 'itemid'
		              and then shows the details for that item
	------------------------------------------------------------------- */

	if($_SESSION['itemid']==0) {
		//show nothing

	} else {
		//view specified item
		if(empty($html_hierarchy)) $html_hierarchy = 'main.';
		$rsitem=view_items('',$_SESSION['itemid']);
		if($r=db_fetch_array($rsitem)) {
			$dbItemID = $r['ItemID'];
			$dbItemName = $r['ItemName'];
			$dbItemDescription = hyperlink_items($r['Description']);
			$dbImageFileName  =  $r['Filename'];
			$dbRecommended   =   $r['Recommended'];
			$dbArticle     =     $r['Article'];

			$html->assign("ITEM_ID", $dbItemID);
			$html->assign("ITEM_NAME", $dbItemName);
			$html->assign("ITEM_DESCRIPTION", $dbItemDescription);
			$html->assign("ARTICLE", $dbArticle);
			$html->assign("IMAGE", img_safe($OPT->productimageroot.$dbImageFileName, $dbItemName, '',"prodimg_$dbItemID"));

		 	if($dbRecommended!=0) $html->parse($html_hierarchy.'icons.recommended');
		 	if(!empty($dbArticle)) $html->parse($html_hierarchy.'icons.article');
			if( ($dbRecommended!=0) || !empty($dbArticle) ) {
				$html->parse($html_hierarchy.'icons');
			}

 			if($addlog['item']==true) spLogToHistory('item', $dbItemID, $dbItemName, "Viewed item \"$dbItemName\"");


		} else {
			exit("item not found");
		}

	 	//show all the item variants
	 	$rsitem = view_details($_SESSION['itemid']);
		$html->assign("ROW_CLASS", ' class="first"');
		
		//pre-parse
		$show_rrp = false;
		$show_buy = false;
		while ($r = db_fetch_array($rsitem)) {
			$dbDetailRRP = $r['RecommendedPrice'];
			$dbWebBuy = $r["WebBuy"];
			$dbDetailPrice = $r['WebPrice'];
			if(intval($dbDetailRRP) != 0) {
				$show_rrp = true;
			}
	 		if ( ($dbWebBuy != 0) && ($dbDetailPrice != 0) ) {
				$show_buy = true;
			}
		}
		db_data_seek($rsitem, 0);
		
		//re-parse for output
		while ($r = db_fetch_array($rsitem)) {
			$dbDetailID = $r['DetailID'];
			$dbDetailName = $r['DetailName'];
			$dbDetailPrice = $r['WebPrice'];
			$dbDetailOriginalPrice = $r['OriginalRetailPrice'];
			$dbDetailOverride = $r['OverrideActive'];
			$dbDetailRRP = $r['RecommendedPrice'];
			$dbDetailStockCode = $r['StockCode'];
			$dbStockCodePrefix = $r['StockCodePrefix'];
			$dbWebBuy = $r["WebBuy"];

			if($dbDetailOverride!=0) {
				$html->assign("DETAIL_PRICE", '<em class="currencyrrp">was '.format_currency($dbDetailOriginalPrice, '').'</em><br />now '.format_currency($dbDetailPrice, '&pound;POA'));
			} else {
				$html->assign("DETAIL_PRICE", format_currency($dbDetailPrice, '&pound;POA'));
			}


			$html->assign("DETAIL_STOCKCODE", format_stockcode($dbStockCodePrefix,$dbDetailStockCode));
			$html->assign("DETAIL_ID", $dbDetailID);
			$html->assign("DETAIL_NAME", $dbDetailName);
			$html->assign("DETAIL_RRP", format_currency($dbDetailRRP, '&nbsp;'));

			//check to see if the item can be purchased online
	 		if ( ($dbWebBuy != 0) && ($dbDetailPrice != 0) ) {
				$html->parse($html_hierarchy.'row.buy_cell.buy');
	 		}
	 		else {
				$html->parse($html_hierarchy.'row.buy_cell.nobuy');
	 		}

	 		//see if there are any additional postage charges, and track their
	 		//usage so there can be a key at the bottom of the page somewhere
	 		$rsitem2=view_detailpostagecharges($dbDetailID);
	 		while($r2=db_fetch_array($rsitem2)) {
				$dbPostageChargeID = $r2['PostageChargeID'];
				$dbPostageChargeName = $r2['PostageChargeName'];
				$dbPostageChargeIcon = $r2['Icon'];
				$dbPostageCharge = $r2['Charge'];
		 		$legendpostagecharge[$dbPostageChargeIcon] = $dbPostageChargeName;
				$html->assign("POSTAGECHARGE_ID", $dbPostageChargeID);
				$html->assign("POSTAGECHARGE_NAME", $dbPostageChargeName);
				$html->assign("POSTAGECHARGE_ICON", $dbPostageChargeIcon);
		 		$html->parse($html_hierarchy.'row.buy_cell.postagecharge');
			}


	 		//print a detail row
			if($show_buy) $html->parse($html_hierarchy.'row.buy_cell');
			if($show_rrp) $html->parse($html_hierarchy.'row.rrp');
	 		$html->parse($html_hierarchy.'row');
			$html->assign("ROW_CLASS", '');
	 	}
		
		if($show_buy) $html->parse($html_hierarchy.'buy_header');
		if($show_rrp) $html->parse($html_hierarchy.'rrp_header');

		//show related items
		$rsrelated = view_itemsrelated($dbItemID);
		if(db_num_rows($rsrelated) > 0) {
			$cell = 0;
			while($r = db_fetch_array($rsrelated)) {
				$dbRelatedItemID = $r['ItemID'];
				$dbRelatedItemName = $r['ItemName'];
				$dbRelatedImageFileName = $r['Filename'];
        $dbParentID = 0;
        $dbChildID = 0;

			  $rscats = view_itemcategories($dbRelatedItemID, true);
        while($rcat = db_fetch_array($rscats)) {
          $dbParentID = $rcat['ParentCategoryID'];
          $dbChildID = $rcat['ChildCategoryID'];
			  }
			
				$cell++;
				$html->assign('RELATED_ITEM_CATEGORY', $dbParentID);
				$html->assign('RELATED_ITEM_SUBCATEGORY', $dbChildID);
				$html->assign('RELATED_ITEM_CATEGORY_PAGE', 1);
				$html->assign('RELATED_ITEM_NAME', $dbRelatedItemName);
				$html->assign('RELATED_ITEM_IMAGE', img_safe($OPT->productimageroot.$dbRelatedImageFileName, $dbRelatedItemName, 'border', "prodimg_$dbItemID", 50));
				$html->parse($html_hierarchy.'related.row.cell');
				
				if($cell % 2 == 0) {
					$html->parse($html_hierarchy.'related.row');
					$cell = 0;
				}
			}
			if($cell % 2 != 0) {
				$html->parse($html_hierarchy.'related.row.empty_cell');
				$html->parse($html_hierarchy.'related.row');
			}
						
			$html->parse($html_hierarchy.'related');
		}

	}
?>
