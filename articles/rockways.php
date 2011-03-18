<?php
	$pagetitle="Rockways Water Features";

	if (!empty($ctrl[2])) {
		$page = intval($ctrl[2]);
	}
	else {
		$page = 1;
	}
	$urlprefix=$OPT->enigmaroot.$OPT->controlpage.'/article/rockways';

	start_page($html, 'rockways', ENIGMA_TEXT);
	$html->assign('PREURL', $urlprefix);

	//precache all the image names and dimensions
	$rs = view_items(141);
	while($r=db_fetch_array($rs)) {

		$rs2 = view_details($r['ItemID']);
		while($r2=db_fetch_array($rs2)) {
			$sc=getStockCode($r2['DetailID']);
			$H=number_format($r2['DimHeight'],0,'','');
			$W=number_format($r2['DimWidth'],0,'','');
			$L=number_format($r2['DimLength'],0,'','');

			$i=$OPT->productimageroot.getPictureFromStockCode($sc);
			if (  ($i==$OPT->productimageroot) || (!file_exists($_SERVER['DOCUMENT_ROOT'].$i)) ) {
				$i=$OPT->productimageroot."blank.png";
			}

			$img[$sc] = $i;//$OPT->productimageroot.getPictureFromStockCode($sc);

			$dim[$sc]  = '';
			$dim[$sc] .= ($L==0) ? '' : " L<strong>{$L}</strong>mm";
			$dim[$sc] .= ($W==0) ? '' : " W<strong>{$W}</strong>mm";
			$dim[$sc] .= ($H==0) ? '' : " H<strong>{$H}</strong>mm";
			$dim[$sc]  = "<br>".trim($dim[$sc]);

		}

	}
	$html->assign('IMG', $img);
	$html->assign('DIM', $dim);


	for ($i=1; $i<=6; $i++) {
		if ( ($page==0) | ($page==$i) ) $html->parse("main.part_$page");
	}

	end_page($html);

?>
