<?php

	function log_view($text) {
		global $DB_ALL_PREVIOUS_SQL;
		$DB_ALL_PREVIOUS_SQL .= "<em>$text</em>\n";
		if($OPT->LogDBqueries) AddLog($text);
	}


	function view_categorydetails($categoryid) {
		global $dbprefix;
		$categoryid=sql_html_to_int($categoryid);

		$sql  = "SELECT CategoryID, CategoryName, Description, ParentID, WebView, StockCodePrefix, CreateDate, DATE_FORMAT(ModifyDate, \"%Y-%m-%d %H:%i:%S\") ModifyDate, DeleteDate \n";
		$sql .= "FROM {$dbprefix}category \n";
		$sql .= "WHERE CategoryID=$categoryid \n";
		return db_query($sql);
	}


	function view_relatedcategories($parentid=0, $childid=0) {
		global $OPT, $dbprefix;
		$parentid=sql_html_to_int($parentid);
		$childid=sql_html_to_int($childid);

		$sql  = "SELECT child.CategoryID childcatID, ";
		$sql .= "       child.ParentID parentcatID, ";
		$sql .= "       parent.CategoryName parentcatName, ";
		$sql .= "       child.CategoryName childcatName \n";
		$sql .= "FROM {$dbprefix}category child, {$dbprefix}category parent \n";
		$sql .= "WHERE child.ParentID=parent.CategoryID \n";
		if($parentID!=0) $sql .= "AND child.ParentID=$parentid \n";
		if($childID!=0) $sql .= "AND child.CategoryID=$childid \n";
		if(!$OPT->showdeletedobjects) $sql .= "AND child.DeleteDate = 0 \n";
		if(!$OPT->showdeletedobjects) $sql .= "AND parent.DeleteDate = 0 \n";
		$sql .= "ORDER BY parent.CategoryName, child.CategoryName \n";
		//echo(nl2br($sql));
		return db_query($sql);
	}


	//returns main category information, with filters determined by the
	//context and parameters.
	// If we're in the store-front then categories with no items under
	// them are hidden, unless $hideempty is set to false (such as for
	// the main categories).
	//
	// category => itemcategory => detail
	//
	function view_categories($parentid=-1, $hideempty=true) {
		global $OPT, $dbprefix;
		$parentid=sql_html_to_int($parentid);

		$joinclause='';
		if(!authorised()) $joinclause=' AND d.WebView!=0 ';

		$sql  = "SELECT c.CategoryID, c.CategoryTypeID, c.CategoryName, c.Description, c.StockCodePrefix, c.WebView, c.ListType, COUNT(DISTINCT ic.ItemID) AS ItemCount, COUNT(DISTINCT d.DetailID) AS DetailCount, c.DeleteDate \n";
		$sql .= "FROM {$dbprefix}category c \n";
		$sql .= "LEFT JOIN {$dbprefix}itemcategory ic ON ( c.CategoryID=ic.CategoryID ) \n";
		$sql .= "LEFT JOIN {$dbprefix}detail d ON ( ic.ItemID=d.ItemID $joinclause) \n";
		$sql .= "WHERE 1=1 \n";
		if($parentid!=-1) $sql .= "AND c.ParentID = $parentid \n";
		if(!$OPT->showdeletedobjects) $sql .= "AND c.DeleteDate = 0 \n";
		if(!authorised()) $sql .= "AND c.WebView!=0 \n";
		$sql .= "GROUP BY c.CategoryID \n";
		if(!authorised()&& $hideempty) $sql .= "HAVING DetailCount>0 \n";
		$sql .= "ORDER BY c.WebSequence, c.CategoryName \n";

		return db_query($sql);
	}



	function view_itemcategories($itemid, $showonlyprimaries=false) {
		global $OPT, $dbprefix;
		$itemid=sql_html_to_int($itemid);

		$sql  = "SELECT ic.ItemID, child.CategoryID ChildCategoryID, parent.CategoryID ParentCategoryID, ic.IsPrimary, parent.CategoryName CatParentName, child.CategoryName CatChildName, ic.DeleteDate \n";
		$sql .= "FROM ({$dbprefix}itemcategory AS ic INNER JOIN {$dbprefix}category AS child ON ic.CategoryID = child.CategoryID) LEFT JOIN {$dbprefix}category AS parent ON child.ParentID = parent.CategoryID \n";
		$sql .= "WHERE ic.ItemID = $itemid \n";
		if(!$OPT->showdeletedobjects) $sql .= "AND ic.DeleteDate = 0 \n";
		if($showonlyprimaries) $sql .= "AND ic.IsPrimary!=0 \n";
		$sql .= "ORDER BY parent.CategoryName, child.CategoryName \n";
		return db_query($sql);
	}



	function view_cattypeitems($categorytypeid) {
		global $OPT, $dbprefix;
		$categorytypeid=sql_html_to_int($categorytypeid);

		$itemfilter='';
		if(!authorised()) {
			$itemfilter='AND i.WebView!=0 ';
		}

		$sql  = "SELECT ";
		$sql .= 		"i.ItemID, ";
		$sql .= 		"i.ItemName, ";
		$sql .= 		"i.Description, ";
		$sql .=		"min(d.RetailPrice) LowPrice, ";
		$sql .=		"max(d.RetailPrice) HighPrice, ";
		$sql .= 		"i.CreateDate ItemCreateDate, ";
		$sql .= 		"DATE_FORMAT(i.ModifyDate, \"%Y-%m-%d %H:%i:%S\") ItemModifyDate, ";
		$sql .= 		"i.DeleteDate ItemDeleteDate \n";
		$sql .= "FROM (({$dbprefix}itemcategory ic ";
		$sql .= 		"LEFT JOIN {$dbprefix}item i ON ( i.ItemID=ic.ItemID $itemfilter)) ";
		$sql .=		"INNER JOIN {$dbprefix}detail d ON (i.ItemID=d.ItemID$detailsFilter)) \n";
		$sql .=		"LEFT JOIN {$dbprefix}category c ON (ic.CategoryID=c.CategoryID) \n";
		$sql .= "WHERE c.CategoryTypeID=$categorytypeid ";
		if(!$OPT->showdeletedobjects) $sql .= "AND i.DeleteDate = 0 \n";
		if(!$OPT->showdeletedobjects) $sql .= "AND ic.DeleteDate = 0 \n";
		$sql .= "GROUP BY i.ItemID \n";
		$sql .= "ORDER BY i.ItemName \n";
		return db_query($sql);
	}






	//detailID can be a single ID or a comma list
	// The price shown will either be the RetailPrice or the OverridePrice,
	// depending on the date.  If in admin mode, the RetailPrice is always shown.
	function view_details($itemid=0, $detailid='') {
		global $OPT, $dbprefix;
		$itemid=sql_html_to_int($itemid);

		if(strpos($detailid,',')!==false) {
			$detailid=sql_html_to_string($detailid);
		} else {
			$detailid=sql_html_to_int($detailid);
		}

		//the retail price may be affected by an override, so the SQL IF statement here
		//checks to see if we're within the override period and if so returns the
		//override price instead.
		if(authorised()) {
			$pricefield='RetailPrice, ';
		} else {
			$pricefield = "IF(d.OverrideStart<=NOW() && d.OverrideStart<>0 && d.OverrideEnd>=NOW(), d.OverridePrice, d.RetailPrice) AS RetailPrice, ";
		}

		$sql  = "SELECT ";
		$sql .=     "c2.StockCodePrefix, ";
		$sql .=		"i.ItemID, ";
		$sql .=		"i.ItemName, ";
		$sql .=		"i.ModifyDate ItemModifyDate, ";
		$sql .=		"d.DetailID, ";
		$sql .=		"d.DetailName, ";
		$sql .=		"d.Size, ";
		$sql .=		"d.UnitID, ";
		$sql .=		$pricefield;//"d.RetailPrice, ";
		$sql .=		"d.RetailPrice AS OriginalRetailPrice, ";
		$sql .=		"d.WebPrice, ";
		$sql .=		"d.RecommendedPrice, ";
		$sql .=		"d.OverridePrice, ";
		$sql .=		"d.OverrideStart, ";
		$sql .=		"d.OverrideEnd, ";
		$sql .=		"(d.OverrideStart<=NOW() && d.OverrideStart<>0 && d.OverrideEnd>=NOW()) AS OverrideActive, ";
		$sql .=		"d.WebView, ";
		$sql .=		"d.WebBuy, ";
		$sql .=		"d.CatalogueView, ";
		$sql .=		"d.CatalogueBuy, ";
		$sql .=		"d.StockCode, ";
		$sql .=     "d.DimHeight, d.DimWidth, d.DimLength, d.DimWeight, d.DimVolume, ";
		$sql .=     "d.UnitIDHeight, d.UnitIDWidth, d.UnitIDLength, d.UnitIDWeight, d.UnitIDVolume, ";
		$sql .=		"d.CreateDate DetailCreateDate, ";
		$sql .=		"d.ModifyDate DetailModifyDate, ";
		$sql .=		"d.DeleteDate DetailDeleteDate, \n";
		$sql .=		"SUM(s.QuantityRemaining) AS StockRemaining, \n";
		$sql .=		"SUM(s.QuantityRemaining*s.PricePaid) AS StockValue \n";
		$sql .=		  "FROM ((((({$dbprefix}detail d \n";
		$sql .=		"INNER JOIN {$dbprefix}item i ON d.ItemID=i.ItemID) \n";
		$sql .=		"INNER JOIN {$dbprefix}unit u ON d.UnitID=u.UnitID) \n";
		$sql .=		"INNER JOIN {$dbprefix}itemcategory ic ON (d.ItemID=ic.ItemID AND ic.IsPrimary!=0)) \n";
		$sql .=		"INNER JOIN {$dbprefix}category c ON ic.CategoryID=c.CategoryID) \n";
		$sql .=		"INNER JOIN {$dbprefix}category c2 ON c.ParentID=c2.CategoryID) \n";
		$sql .=		"LEFT JOIN {$dbprefix}stock s ON d.DetailID=s.DetailID \n";

		$sql .= "WHERE 1=1 \n";
		if(!authorised()) {
			$sql .= "AND d.WebView != 0 \n";
			$sql .= "AND i.WebView != 0 \n";
		}
		if($itemid!=0) $sql .= "AND d.ItemID = $itemid \n";
		if(strpos($detailid,',')!==false) {
			$sql .= "AND d.DetailID IN ($detailid) \n";
		} else {
			if($detailid!=0) $sql .= "AND d.DetailID = $detailid \n";
		}
		if(!$OPT->showdeletedobjects) $sql .= "AND d.DeleteDate = 0 \n";
		$sql .= "GROUP BY d.DetailID \n";
		$sql .= "ORDER BY i.ItemName, (d.Size*u.BaseMultiple), d.DetailName \n";
		//echo(nl2br($sql));
		return db_query($sql);
	}


	//detailID can be a single ID or a comma list
	function view_detailpostagecharges($detailid) {
		global $OPT, $dbprefix;
		if(strpos($detailid,',')!==false) {
			$detailid=sql_html_to_string($detailid);
		} else {
			$detailid=sql_html_to_int($detailid);
		}

		$sql  = "SELECT d.DetailID, d.DetailName, ";
		$sql .= "p.PostageChargeID, p.PostageChargeName, p.Icon, ";
		$sql .= "dp.PalletThreshold, dp.PostageCharge AS Charge \n";
		$sql .= "FROM {$dbprefix}detail AS d \n";
		$sql .= "INNER JOIN {$dbprefix}detailpostagecharge AS dp ON (d.DetailID=dp.DetailID) \n";
		$sql .= "INNER JOIN {$dbprefix}postagecharge AS p ON (dp.PostageChargeID=p.PostageChargeID) \n";
		if(strpos($detailid,',')!==false) {
			$sql .= "WHERE d.DetailID IN ($detailid) \n";
		} else {
			if($detailid!=0) $sql .= "WHERE d.DetailID = $detailid \n";
		}
		//echo(nl2br($sql));
		return db_query($sql);
	}


	function view_postagezones() {
		global $OPT, $dbprefix;

		$sql  = "SELECT PostageZoneID, PostageZoneName \n";
		$sql .= "FROM {$dbprefix}postagezone pz \n";
		return db_query($sql);
	}


	function view_postagebands() {
		global $OPT, $dbprefix;

		$sql  = "SELECT PostageBandID, PostageBandName, StartValue, EndValue \n";
		$sql .= "FROM {$dbprefix}postageband pb \n";
		$sql .= "ORDER BY StartValue \n";
		return db_query($sql);
	}


	function view_postagecharge($zoneid=0, $bandid=0) {
		global $OPT, $dbprefix;
		$zoneid=sql_html_to_int($zoneid);
		$bandid=sql_html_to_int($bandid);

    $sql  = "SELECT pbz.PostageZoneID, pbz.PostageBandID, pbz.Charge \n";
		$sql .= "FROM {$dbprefix}postagebandzone pbz \n";
		$sql .= "WHERE 1=1 \n";
		if($zoneid!=0) $sql .= "AND PostageZoneID=$zoneid \n";
		if($bandid!=0) $sql .= "AND PostageBandID=$bandid \n";
		$r = db_query($sql);
		return $r;
	}


	//returns all items where the item name or description contains the text
	//specified by $needle
	function view_search($needle) {
		global $OPT, $dbprefix;
		$needle = sql_html_to_string($needle);
		$search_stockcodes = false;
		$sc_cat = 0;
		$sc_item = 0;
		
		if( (strlen($needle)==5) && (is_numeric($needle)) ) {
			$sc_cat = intval(substr($needle, 0, 2));
			$sc_item = intval(substr($needle, 2, 3));
			$search_stockcodes = true;
		}
		
		if($search_stockcodes) {
			$sql  = "SELECT i.ItemID, i.ItemName, i.Description, i.ModifyDate ItemModifyDate, ";
			$sql .= "d.DetailID, d.DetailName, d.RetailPrice ";
			$sql .= "FROM store_item i ";
			$sql .= "INNER JOIN store_itemcategory ic2 ON (i.ItemID=ic2.ItemID) ";
			$sql .= "INNER JOIN store_category c2 ON (ic2.CategoryID=c2.CategoryID) ";
			$sql .= "INNER JOIN store_category c1 ON (c2.ParentID=c1.CategoryID) ";
			$sql .= "INNER JOIN store_detail d ON (i.ItemID=d.ItemID) ";
			$sql .= "WHERE d.StockCode='$sc_item' ";
			$sql .= "AND c1.StockCodePrefix='$sc_cat' ";
			if($OPT->instore) $sql .= "AND i.WebView != 0 \n";
			if(!$OPT->showdeletedobjects) $sql .= "AND i.DeleteDate = 0 \n";
			//$sql .= "LIMIT 1 ";

		} else {
			$sql  = "SELECT ";
			$sql .= "MATCH (ItemName, Description) AGAINST ('$needle') AS Rank, ";
			$sql .=		"i.ItemID, ";
			$sql .=		"i.ItemName, ";
			$sql .=		"i.Description, ";
			$sql .=		"i.ModifyDate ItemModifyDate ";
			$sql .= "FROM {$dbprefix}item i \n";
			$sql .= "WHERE MATCH (ItemName, Description) AGAINST ('$needle') \n";
			if($OPT->instore) $sql .= "AND i.WebView != 0 \n";
			if(!$OPT->showdeletedobjects) $sql .= "AND i.DeleteDate = 0 \n";
		}
		return db_query($sql);
	}


	function view_User($username, $password) {
		global $OPT, $dbprefix;

		//validate
		$username=sql_html_to_string($username);
		$password=sql_html_to_string($password);

		//check
		$sql  = "SELECT UserID, UserName, FirstName, LastName, AccessLevel \n";
		$sql .= "FROM {$dbprefix}user \n";
		$sql .= "WHERE UserName='$username' \n";
		$sql .= "AND Password=MD5('$password') \n";
		return db_query($sql);
	}


	function view_Picture($pictureid) {
		global $OPT, $dbprefix;

		$pictureid = sql_html_to_int($pictureid);

		$sql  = "SELECT PictureID, PictureName, FileName ";
		$sql .= "FROM {$dbprefix}picture ";
		$sql .= "WHERE PictureID=$pictureid ";
		return db_query($sql);
	}
?>