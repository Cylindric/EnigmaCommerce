<?php
	/* ----------------------------------------------------------------
		    Filename: search.php

		Requirements: $_GET['q']  (**Used in DB query**)

		       Notes:
	------------------------------------------------------------------- */

	if(isset($_GET['q'])) {
		$subSearchString = $_GET['q']; //**NEEDS VALIDATING**
		
		if(strlen($subSearchString) <= 3) {
			$html->parse('main.noquery');
			
		} else {
	
			$rs=view_search($subSearchString);
			if(db_num_rows($rs)==0) {
				$html->parse('main.noresults');
				
// 			} elseif(db_num_rows($rs)==1) {
// 				while($r=db_fetch_array($rs)) {
// 					$dbItemID=$r['ItemID'];
// 				}
// 				redirect_page($OPT->enigmaroot.$OPT->controlpage.'/item/'.$dbItemID.'/1#item_'.$dbItemID);
				
			} else {
				$html->assign('RESULT_COUNT', db_num_rows($rs));
				while($r=db_fetch_array($rs)) {
					$dbSearchRank=$r['Rank'];
					$dbItemID=$r['ItemID'];
					$dbItemName=$r['ItemName'];
					$dbItemDescription=ShrinkString($r['Description'],200,"...",2);
		
					$html->assign('SEARCH_RANK', number_format($dbSearchRank*10,0).'%');
					$html->assign('ITEM_ID', $dbItemID);
					$html->assign('ITEM_NAME', $dbItemName);
					$html->assign('ITEM_DESCRIPTION', $dbItemDescription);
					$html->parse('main.results.row');
				}
				$html->parse('main.results');
			}
		}
	}
?>
