<?php
	$pagetitle="Stone And Water";

	if (!empty($ctrl[2])) {
		$page = intval($ctrl[2]);
	}
	else {
		$page = 2;
	}
	$urlprefix=$OPT->enigmaroot.$OPT->controlpage.'/article/stoneandwater';

	start_page($html, 'stoneandwater', ENIGMA_TEXT);
	$html->assign('PREURL', $urlprefix);

	$rs = view_items(100);
	while($r=db_fetch_array($rs)) {

		$rs2 = view_details($r['ItemID']);
		while($r2=db_fetch_array($rs2)) {
			$sc=getStockCode($r2['DetailID']);

			$i=$OPT->productimageroot.getPictureFromStockCode($sc);
			if (  ($i==$OPT->productimageroot) || (!file_exists($_SERVER['DOCUMENT_ROOT'].$i)) ) {
				$i=$OPT->productimageroot."blank.png";
			}

			$id[$sc] = $r2['DetailID'];
			$img[$sc] = $i;
			$imgprops_this = getimagesize($_SERVER['DOCUMENT_ROOT'].$i);
			$img[$sc] = $i . '" width="'.$imgprops_this[0].'" height="'.$imgprops_this[1];

			$price[$sc] = format_currency($r2['RetailPrice'], '£POA');
			$name[$sc] = $r['ItemName'];
			$detail[$sc] = $r2['RetailPrice']==0 ? '' : $r2['DetailName'];
			$button[$sc] = $r2['RetailPrice']==0 ? '' : draw_rolloverimage('btnAdd_'.$sc, 'button_add', $OPT->enigmaroot.$OPT->controlpage.'/basket/add/'.$r2['DetailID'].'/1', 16, 16, 'Add to Basket');

		}

	}
	$html->assign('ID', $id);
	$html->assign('IMG', $img);
	$html->assign('PRICE', $price);
	$html->assign('NAME', $name);
	$html->assign('DETAIL', $detail);
	$html->assign('ADD', $button);

	fancy_vardump($img);

	for ($i=1; $i<=6; $i++) {
		if ( ($page==0) | ($page==$i) ) $html->parse("main.part_$page");
	}

	end_page($html);

?>