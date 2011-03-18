<?php

	function spInsertItemDetails($itemid, $detailname,  $detailsize=0, $detailunits=0, $detailretailprice=0, $detailwebprice=0, $detailrrp=0, $detailvisweb=0, $detailbuyweb=0, $detailviscat=0, $detailbuycat=0) {
		global $dbprefix;

		$itemid = sql_html_to_int($itemid);
		$detailname = sql_html_to_string($detailname);
		$detailsize = sql_html_to_float($detailsize);
		$detailunits = sql_html_to_int($detailunits);
		$detailretailprice = sql_html_to_float($detailretailprice,0);
		$detailwebprice = sql_html_to_float($detailwebprice,0);
		$detailrrp = sql_html_to_float($detailrrp,0);
		$detailvisweb = sql_html_to_bit($detailvisweb);
		$detailbuyweb = sql_html_to_bit($detailbuyweb);
		$detailviscat = sql_html_to_bit($detailviscat);
		$detailbuycat = sql_html_to_bit($detailbuycat);

		$sql  = "INSERT INTO {$dbprefix}detail \n";
		$sql .=    "(ItemID, ";
		$sql .=     "DetailName, ";
		$sql .=     "Size, ";
		$sql .=     "UnitID, ";
		$sql .=     "RetailPrice, ";
		$sql .=     "WebPrice, ";
		$sql .=     "RecommendedPrice, ";
		$sql .=     "WebView, ";
		$sql .=     "WebBuy, ";
		$sql .=     "CatalogueView, ";
		$sql .=     "CatalogueBuy, ";
		$sql .=     "CreateDate) \n";
		$sql .= "VALUES ";
		$sql .=    "($itemid, ";
		$sql .=     "'$detailname', ";
		$sql .=     "$detailsize, ";
		$sql .=     "$detailunits, ";
		$sql .=     "$detailretailprice, ";
		$sql .=     "$detailwebprice, ";
		$sql .=     "$detailrrp, ";
		$sql .=     "$detailvisweb, ";
		$sql .=     "$detailbuyweb, ";
		$sql .=     "$detailviscat, ";
		$sql .=     "$detailbuycat, ";
		$sql .=     "NOW()) \n";
		//echo(nl2br($sql));
		$rs=db_query($sql);
		$newdetailid=db_insert_id();

		//set the stock code
		$detailstockcode = spSetStockCode($newdetailid);

		return $newdetailid;
	}

	function spUpdateItemDetails($detailid, $detailname,  $detailsize=0, $detailunits=0, $detailretailprice=0, $detailwebprice=0, $detailrrp=0, $detailvisweb=0, $detailbuyweb=0, $detailviscat=0, $detailbuycat=0) {
		global $dbprefix;

		$detailid = sql_html_to_int($detailid);
		$detailname = sql_html_to_string($detailname);
		$detailsize = sql_html_to_float($detailsize);
		$detailunits = sql_html_to_int($detailunits);
		$detailretailprice = sql_html_to_float($detailretailprice);
		$detailwebprice = sql_html_to_float($detailwebprice);
		$detailrrp = sql_html_to_float($detailrrp);
		$detailvisweb = sql_html_to_bit($detailvisweb);
		$detailbuyweb = sql_html_to_bit($detailbuyweb);
		$detailviscat = sql_html_to_bit($detailviscat);
		$detailbuycat = sql_html_to_bit($detailbuycat);

		$sql  = "UPDATE {$dbprefix}detail \n";
		$sql .= "SET DetailName='$detailname', ";
		$sql .=     "Size=$detailsize, ";
		$sql .=     "UnitID=$detailunits, ";
		$sql .=     "RetailPrice=$detailretailprice, ";
		$sql .=     "WebPrice=$detailwebprice, ";
		$sql .=     "RecommendedPrice=$detailrrp, ";
		$sql .=     "WebView=$detailvisweb, ";
		$sql .=     "WebBuy=$detailbuyweb, ";
		$sql .=     "CatalogueView=$detailviscat, ";
		$sql .=     "CatalogueBuy=$detailbuycat \n";
		$sql .= "WHERE DetailID = $detailid \n";
		db_query($sql);
	}

	//if an itemid is specified, then all details for that item are
	//deleted.
	function spDeleteItemDetails($detailid=0, $itemid=0, $deletedate=0) {
		global $dbprefix;
		//log_sp("spDeleteItemDetails","$detailid, $itemid");
		$detailid = sql_html_to_int($detailid);
		$itemid = sql_html_to_int($itemid);
		$deletedate = sql_html_to_string($deletedate);

		if(empty($deletedate)) $deletedate = date('Y-m-d H:i:s');

		$sql  = "UPDATE {$dbprefix}detail \n";
		$sql .= "SET DeleteDate = '$deletedate' \n";
		if($itemid!=0) $sql .= "WHERE ItemID=$itemid \n";
		else $sql .= "WHERE DetailID=$detailid \n";
		db_query($sql);

		//cascade
		spDeleteDetailSupplier($detailid, $deletedate);
	}


	function spUnDeleteItemDetails($detailid=0, $itemid=0, $deletedate=0) {
		global $dbprefix;

		$detailid = sql_html_to_int($detailid);
		$itemid = sql_html_to_int($itemid);
		$deletedate = sql_html_to_string($deletedate);

		$sql  = "UPDATE {$dbprefix}detail \n";
		$sql .= "SET DeleteDate = 0 \n";
		if($itemid!=0) $sql .= "WHERE ItemID=$itemid \n";
		else $sql .= "WHERE DetailID=$detailid \n";
		if(!empty($deletedate)) $sql .= "AND DeleteDate='$deletedate' ";
		db_query($sql);

		//cascade
		spUnDeleteDetailSupplier($detailid, $deletedate);
	}


	function spUpdateDetailOverride($detailid, $price, $start, $end) {
		global $dbprefix;

		$detailid = sql_html_to_int($detailid);
		$price = sql_html_to_float($price);
		$start = sql_html_to_date($start);
		$end = sql_html_to_date($end);

		$sql  = "UPDATE {$dbprefix}detail \n";
		$sql .= "SET OverridePrice=$price, \n";
		$sql .= "OverrideStart='$start', \n";
		$sql .= "OverrideEnd='$end' \n";
		$sql .= "WHERE DetailID=$detailid \n";
		//echo(nl2br($sql));
		//die();
		db_query($sql);

	}


	function spUpdateDetailDimensions($detailid, $width, $height, $length, $weight, $volume, $unitwidth, $unitheight, $unitdepth, $unitweight, $unitvolume) {
		global $dbprefix;

		$detailid = sql_html_to_int($detailid);
		$width = sql_html_to_float($width);
		$height = sql_html_to_float($height);
		$length = sql_html_to_float($length);
		$weight = sql_html_to_float($weight);
		$volume = sql_html_to_float($volume);
		$unitwidth = sql_html_to_int($unitwidth);
		$unitheight = sql_html_to_int($unitheight);
		$unitlength = sql_html_to_int($unitlength);
		$unitweight = sql_html_to_int($unitweight);
		$unitvolume = sql_html_to_int($unitvolume);

		$sql  = "UPDATE {$dbprefix}detail \n";
		$sql .= "SET ";
		$sql .= "DimWidth = $width, \n";
		$sql .= "DimHeight = $height, \n";
		$sql .= "DimLength = $length, \n";
		$sql .= "DimWeight = $weight, \n";
		$sql .= "DimVolume = $volume, \n";
		$sql .= "UnitIDWidth = $unitwidth, \n";
		$sql .= "UnitIDHeight = $unitheight, \n";
		$sql .= "UnitIDLength = $unitlength, \n";
		$sql .= "UnitIDWeight = $unitweight, \n";
		$sql .= "UnitIDVolume = $unitvolume \n";
		$sql .= "WHERE DetailID=$detailid \n";
		//echo(nl2br($sql));
		//die();
		db_query($sql);
	}

?>