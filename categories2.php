<?php
	/* ----------------------------------------------------------------
		Filename: categories.php

		Requirements: $_SESSION['maincatid']

		Notes: This page retrieves a list of categories from the
			database and outputs three columns linking to the
			item list
	------------------------------------------------------------------- */

	$_SESSION['subcatdesc'] = '';
	if($_SESSION['maincatid']!=0) {
		$rs = view_categories($_SESSION['maincatid']);
		$html->assign('MAINCAT_ID', $_SESSION['maincatid']);

		//save the information I need in arrays, as we may need to jump around a bit later
		$i = 0;
		while ($r = db_fetch_array($rs)) {
			$CatNames[$i] = $r['CategoryName'];
			$CatCodes[$i] = $r['CategoryID'];
			$CatDeleted[$i] = (boolean)($r['DeleteDate']!='0000-00-00 00:00:00');
			if(authorised()) $CatNames[$i].=' ('.$r['ItemCount'].')';

			if($_SESSION['subcatid']==$r['CategoryID']) {
				//get category text from Mambo
				$mosText = mostext($_SESSION['subcatid']);
				if(!empty($mosText)) {
					$_SESSION['subcatdesc'] = $mosText;
				} else {
					$_SESSION['subcatdesc'] = $r['Description'];
				}
			}

			$i++;
		}
		if(authorised()) {
			$CatNames[$i] = 'Add New Category';
			$CatCodes[$i] = -1;
			$CatText[$i] = 'Add a new category';
			$CatDeleted[$i] = false;
		}

		$CatsPerRow=5;
		$CategoryNum=0;
		$RowNum=1;

		for ($cell = 0; $cell < count($CatNames); $cell++) {
			$html->assign("SUBCAT_ID", $CatCodes[$cell]);
			$html->assign("SUBCAT_NAME", $CatNames[$cell]);
			$html->assign("DELETED", '');

			if($CatDeleted[$cell]) $html->assign("DELETED", 'deleted');
			if($CatCodes[$cell]==$_SESSION['subcatid']) {
			 $_SESSION['subcatname'] = $CatNames[$cell];
				$html->parse("main.row.cell.currentcatcell");
				if($addlog['subcat']==true) spLogToHistory('category', $CatCodes[$cell], $CatNames[$cell], 'Viewed category "'.$CatNames[$cell].'"');
			} else {
				$html->parse("main.row.cell.catcell");
			}
			$html->parse("main.row.cell");

			//an end of row, if this is the last cell in a row
			if ( $CategoryNum == ($CatsPerRow-1) ) {
				$CategoryNum = -1;
				$html->parse("main.row");
			}
			$CategoryNum++;
		}

		//chances are that we need to put in some blank category cells and close the row
		if ($CategoryNum != 0) {
			$ColumnsToSpan = (($CatsPerRow-$CategoryNum)*2);
			$html->assign("COLSPAN", $ColumnsToSpan);
			$html->parse("main.row.span");
			$html->parse("main.row");
		}

	$categorylabel = $_SESSION['maincatname'];
	if ((!empty($_SESSION['maincatname'])) && (!empty($_SESSION['subcatname']))) {
		$categorylabel .= ' :: ';
	}
	$categorylabel .= $_SESSION['subcatname'];
	$html->assign('CAT_NAME', $categorylabel);

	//the description block under the subcats will come from the sub-category.
	//if there is no description on the sub-category or no sub-cat is selected,
	//use the main category's description
	$sub_text = $_SESSION['subcatdesc'];
	$main_text = $_SESSION['maincatdesc'];

	//only show description if not in an item
	if($_SESSION['itemid']==0) {

		//show sub-text if present
		if(!empty($sub_text)) {
			// there is sub-text, so use that
			$html->assign('CAT_DESCRIPTION', parse_codes($sub_text));
			$html->parse('main.maindescription');
		
		} else {
			// there is no subtext, so check maintext
			if(!empty($main_text) && ($_SESSION['subcatid']==$r['CategoryID'])) {
				$html->assign('CAT_DESCRIPTION', parse_codes($main_text));
				$html->parse('main.maindescription');
			
			} else {
				// There is no maintext or subtext, so just do nothing
				
			}

		}
/*
		//if there is no main text definend, look for sub text
		if(empty($main_text)) {

			if(empty($sub_text)) {
				//no main text, and no sub text, so do nothing

			} else {
				//no main text, but sub-text found.  Use that
				$html->assign('CAT_DESCRIPTION', parse_codes($sub_text));
				$html->parse('main.maindescription');
			}

		} else {
			//main text found, use that as long as we're not viewing a sub-cat
			if(empty($_SESSION['subcatid'])) {
				$html->assign('CAT_DESCRIPTION', parse_codes($main_text));
				$html->parse('main.maindescription');
			}

		}
		*/
	}
		unset ($CatNames);
		unset ($CatCodes);
	unset ($CatText);
	unset ($CatDeleted);
	}
?>
