<?php
	/* ----------------------------------------------------------------
		    Filename: rightpanels.php

		Requirements:

		       Notes:
	------------------------------------------------------------------- */

	if(CategoryVisible($OPT->cat_featureditems)) {
		$rs=view_items($OPT->cat_featureditems);
		$num_featureditems=db_num_rows($rs);
		while($r=db_fetch_array($rs)) {
			$html->assign('ITEM_ID', $r['ItemID']);
			$html->assign('ITEM_NAME', $r['ItemName']);
			$html->parse('main.featureditems.row');
		}

		if($num_featureditems>0)
			$html->parse('main.featureditems');
	}

	// -------------------------------------------------------------------
	if(CategoryVisible($OPT->cat_specialoffers)) {
		$rs=view_items($OPT->cat_specialoffers);
		$num_specials=db_num_rows($rs);
		while($r=db_fetch_array($rs)) {
			$html->assign('ITEM_ID', $r['ItemID']);
			$html->assign('ITEM_NAME', $r['ItemName'].' '.$r['DetailName']);
			$html->parse('main.specials.row');
		}

		if($num_specials>0)
			$html->parse('main.specials');
	}
	// -------------------------------------------------------------------
	if(CategoryVisible($OPT->cat_christmas)) {
		$rs=view_items($OPT->cat_christmas);
		$num_specials=db_num_rows($rs);
		while($r=db_fetch_array($rs)) {
			$html->assign('ITEM_ID', $r['ItemID']);
			$html->assign('ITEM_NAME', $r['ItemName'].' '.$r['DetailName']);
			$html->parse('main.christmas.row');
		}

		if($num_specials>0)
			$html->parse('main.christmas');
	}
	// -------------------------------------------------------------------
	if(CategoryVisible($OPT->cat_newitems)) {
		$rs=view_items($OPT->cat_newitems);
		$num_specials=db_num_rows($rs);
		while($r=db_fetch_array($rs)) {
			$html->assign('ITEM_ID', $r['ItemID']);
			$html->assign('ITEM_NAME', $r['ItemName'].' '.$r['DetailName']);
			$html->parse('main.newitems.row');
		}

		if($num_specials>0)
			$html->parse('main.newitems');
	}
	// -------------------------------------------------------------------
	if(CategoryVisible($OPT->cat_stockist)) {
			$html->assign('WE_STOCK_TEXT', mosModuleText(48, $title));
  		$html->assign('WE_STOCK_TITLE', $title);

			$html->parse('main.we_stock');
	}
	// -------------------------------------------------------------------
?>
