<?php
	/* ----------------------------------------------------------------
		    Filename:

		Requirements:

		       Notes:
	------------------------------------------------------------------- */
	require_once('application.php');

	require_https();
	require_login();


	// --------------
	// Prepare Output
	// --------------
	$page = $ctrl[2];
	$detailid = sql_html_to_int($ctrl[3]);

	$html->assign('PAGE', $page);
	$html->assign('DETAIL_ID', $detailid);

	$rs = view_details(0, $detailid);
	if(db_num_rows($rs) > 0) {
		$r = db_fetch_array($rs);
		$dbItemName = $r['ItemName'];
		$dbDetailName = $r['DetailName'];
		$dbRetailPrice = format_currency($r['RetailPrice']);
		$dbStockCode = getStockCode($detailid);
		$dbPicture = getPictureFromStockCode($dbStockCode);
		
		$html->assign('ITEM_NAME', $dbItemName);
		$html->assign('DETAIL_NAME', $dbDetailName);
		$html->assign('RETAIL_PRICE', $dbRetailPrice);
		$html->assign('PICTURE_A', img_safe($OPT->productimageroot.$dbPicture));
		$html->assign('PICTURE_B', img_safe($OPT->productimageroot.$dbPicture, '', '', '', '50%'));

		switch ($page) {
			case 'a':
			break;
			case 'b':
			break;
			case 'c':
			break;
		}
	}


	$html->parse("main.type_$page");
// 	$html->parse("main.type_a");
// 	$html->parse("main.type_b");
// 	$html->parse("main.type_c");
?>