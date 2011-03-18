<?php

/*

	 view_items([categoryid], [itemid], [pagesize], [pagenumber])
	 view_specials()

*/

// ============================================================================

	function view_items($categoryid=0,$itemid=0, $pagesize=0, $pagenumber=0, $randomise=0) {
		global $OPT, $dbprefix;
		$itemid=sql_html_to_int($itemid);
		$pagesize=sql_html_to_int($pagesize);
		$pagenumber=sql_html_to_int($pagenumber);
		$randomise=sql_html_to_bit($randomise);

		if(strpos($categoryid,',')!==false) {
			$categoryid=sql_html_to_string($categoryid);
		} else {
			$categoryid=sql_html_to_int($categoryid);
		}

	
		$itemfilter='';
		if(!authorised()) {
			$itemfilter='AND i.WebView!=0 ';
		}

		$sql  = "SELECT ";
		$sql .= 		"i.ItemID, ";
		$sql .= 		"i.ItemName, ";
		$sql .= 		"i.Description, ";
		$sql .= 		"i.WebView, ";
		$sql .= 		"i.Recommended, ";
		$sql .= 		"i.Article, ";
		$sql .= 		"c.ListType, ";
		$sql .= 		"ic.CategoryID, ";
		$sql .= 		"p.PictureID, p.Filename, ";
		$sql .=   "COUNT(d.DetailID) DetailCount, ";
		$sql .=		"MIN(d.WebPrice) LowPrice, ";
		$sql .=		"MAX(d.WebPrice) HighPrice, ";
		$sql .= 		"i.CreateDate ItemCreateDate, ";
		$sql .= 		"DATE_FORMAT(i.ModifyDate, \"%Y-%m-%d %H:%i:%S\") ItemModifyDate, ";
		$sql .= 		"i.DeleteDate ItemDeleteDate \n";
		$sql .= "FROM {$dbprefix}itemcategory ic \n";
		$sql .= 		"LEFT JOIN {$dbprefix}item i ON ( i.ItemID=ic.ItemID $itemfilter) \n";
		$sql .= 		"LEFT JOIN {$dbprefix}category c ON ( ic.CategoryID=c.CategoryID) \n";
		$sql .=			"LEFT JOIN {$dbprefix}detail d ON (i.ItemID=d.ItemID$detailsFilter AND d.WebView!=0) \n";
		$sql .= 		"LEFT JOIN {$dbprefix}itempicture ip ON ( i.ItemID=ip.ItemID AND ip.IsPrimary<>0 AND ip.DeleteDate=0 ) \n";
		$sql .= 		"LEFT JOIN {$dbprefix}picture p ON ( ip.PictureID=p.PictureID ) \n";
		$sql .= "WHERE 1=1 ";
		if(strpos($categoryid,',')!==false) {
			$sql .= "AND ic.CategoryID IN ($categoryid) \n";
		} else {
			if($categoryid!=0) $sql .= "AND ic.CategoryID=$categoryid \n";
		}
		if($itemid!=0) $sql .= "AND i.ItemID = $itemid \n";
		if(!$OPT->showdeletedobjects) {
			$sql .= "AND i.DeleteDate = 0 \n";
			$sql .= "AND (ic.DeleteDate = 0 OR ic.DeleteDate IS NULL) \n";
			$sql .= "AND (p.DeleteDate = 0 OR p.DeleteDate IS NULL) \n";
		}
		$sql .= "GROUP BY i.ItemID \n";
		if(!authorised()) $sql .= "HAVING COUNT(d.DetailID)>0 \n";
		if($randomise) {
			$sql .= "ORDER BY RAND() \n";
		} else {
			$sql .= "ORDER BY i.ItemName \n";
		}
		if ( ($pagesize != 0) && ($pagenumber != 0) ) {
			$offset = ($pagenumber-1)*$pagesize;
			$sql .= "LIMIT " . $offset . ", " . $pagesize;
		}
		//echo(nl2br($sql));
		return db_query($sql);
	}

// ============================================================================

	function view_specials() {
		global $OPT, $dbprefix;

		$sql  = "SELECT i.ItemID, i.ItemName, d.DetailID, d.DetailName \n";
		$sql .= "FROM {$dbprefix}item AS i \n";
		$sql .= "INNER JOIN {$dbprefix}detail AS d ON (i.ItemID=d.ItemID) \n";
		$sql .= "WHERE (d.OverrideStart<=NOW() && d.OverrideStart<>0 && d.OverrideEnd>=NOW())<>0 \n";
		return db_query($sql);
	}

// ============================================================================
	
	function view_itemsrelated($itemid, $onlyactive=true) {
		global $OPT, $dbprefix;
		$itemid = sql_html_to_int($itemid);

		$sql  = "SELECT i.ItemID, i.ItemName, p.PictureID, p.Filename \n";
		$sql .= "FROM {$dbprefix}itemrelation AS ir \n";
		$sql .= "LEFT JOIN {$dbprefix}item i ON ( i.ItemID=ir.ChildID ) \n";
		$sql .= "LEFT JOIN {$dbprefix}itempicture ip ON ( i.ItemID=ip.ItemID AND ip.IsPrimary<>0 AND ip.DeleteDate=0 ) \n";
		$sql .= "LEFT JOIN {$dbprefix}picture p ON ( ip.PictureID=p.PictureID ) \n";
		$sql .= "WHERE ir.ParentID = $itemid \n";
		if($onlyactive===true) {
		  $sql .= "AND i.WebView != 0 \n";
		}
		//echo(nl2br($sql));
		return db_query($sql);
	}
// ============================================================================
?>