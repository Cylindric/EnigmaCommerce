<?php
	/* ----------------------------------------------------------------
		    Filename:

		Requirements:

		       Notes:
	------------------------------------------------------------------- */

	require_https();

	//view categories
	$rs=view_history('category',0,5);
	$html->assign('TITLE', 'Categories');
	while($r=db_fetch_array($rs)) {
		$dbEntityType=$r['EntityType'];
		$dbEntityName=$r['EntityName'];
		$dbCounter=$r['Counter'];

		$html->assign('NAME', $dbEntityName);
		$html->assign('COUNTER', $dbCounter);
		$html->parse('main.block.row');
	}
	$html->parse('main.block');


	//view items
	$rs=view_history('item',0,5);
	$html->assign('TITLE', 'Items');
	while($r=db_fetch_array($rs)) {
		$dbEntityType=$r['EntityType'];
		$dbEntityID=$r['EntityID'];
		$dbEntityName=$r['EntityName'];
		$dbCounter=$r['Counter'];

		$html->assign('NAME', "<a href=\"".$OPT->enigmaroot.$OPT->controlpage."/item/$dbEntityID/1\">$dbEntityName</a>");
		$html->assign('COUNTER', $dbCounter);
		$html->parse('main.block.row');
	}
	$html->parse('main.block');


	//view articles
	$rs=view_history('article',0,5);
	$html->assign('TITLE', 'Articles');
	while($r=db_fetch_array($rs)) {
		$dbEntityType=$r['EntityType'];
		$dbEntityName=$r['EntityName'];
		$dbCounter=$r['Counter'];

		$html->assign('NAME', "<a href=\"".$OPT->enigmaroot.$OPT->controlpage."/article/$dbEntityName\">$dbEntityName</a>");
		$html->assign('COUNTER', $dbCounter);
		$html->parse('main.block.row');
	}
	$html->parse('main.block');


	//view text pages
	$rs=view_history('text',0,5);
	$html->assign('TITLE', 'Static Pages');
	while($r=db_fetch_array($rs)) {
		$dbEntityName=$r['EntityName'];
		$dbEntityType=$r['EntityType'];
		$dbCounter=$r['Counter'];

		$html->assign('NAME', $dbEntityName);
		$html->assign('COUNTER', $dbCounter);
		$html->parse('main.block.row');
	}
	$html->parse('main.block');



?>