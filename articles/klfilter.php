<?php

	$pagetitle="Koi Logic Filtration";

	if (!empty($ctrl[2])) {
		$page = intval($ctrl[2]);
	}
	else {
		$page = 1;
	}
	$urlprefix=$OPT->enigmaroot.$OPT->controlpage.'/article/klfilter';

	start_page($html, 'klfilter', ENIGMA_TEXT);
	$html->assign('PREURL', $urlprefix);

	//assign the price parts
	$html->assign('PRICE_COMP_1', format_currency(GetDetailPrice(648)));
	$html->assign('PRICE_EMPT_1', format_currency(GetDetailPrice(643)));
	$html->assign('PRICE_VORT_1', format_currency(GetDetailPrice(643)));
	$html->assign('PRICE_COMP_2', format_currency(GetDetailPrice(652)));
	$html->assign('PRICE_EMPT_2', format_currency(GetDetailPrice(647)));
	$html->assign('PRICE_VORT_2', format_currency(GetDetailPrice(647)));
	$html->assign('PRICE_COMP_3', format_currency(GetDetailPrice(646)));
	$html->assign('PRICE_EMPT_3', format_currency(GetDetailPrice(651)));
	$html->assign('PRICE_VORT_3', format_currency(GetDetailPrice(651)));
	$html->assign('PRICE_COMP_4', format_currency(GetDetailPrice(645)));
	$html->assign('PRICE_EMPT_4', format_currency(GetDetailPrice(642)));
	$html->assign('PRICE_VORT_4', format_currency(GetDetailPrice(642)));

	for ($i=1; $i<=5; $i++) {
		if ( ($page==0) | ($page==$i) ) $html->parse("main.part_$page");
	}


	end_page($html);

?>
