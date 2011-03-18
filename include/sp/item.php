<?php

	function spInsertItemGeneral($itemname, $itemdescription='') {
		global $dbprefix;

		$itemname = sql_html_to_string($itemname);
		$itemdescription = sql_html_to_string($itemdescription);
		$sql  = "INSERT INTO {$dbprefix}item \n";
		$sql .= 	  "(ItemName, ";
		$sql .=     "Description, ";
		$sql .=     "WebView, WebBuy, CatalogueView, CatalogueBuy, ";
		$sql .=     "CreateDate) \n";
		$sql .= "VALUES ";
		$sql .= 	  "('$itemname', ";
		$sql .=     "'$itemdescription', ";
		$sql .=     "0, 0, 1, 1, ";
		$sql .=     "NOW()) \n";
		db_query($sql);
		return db_insert_id();
	}

	function spUpdateItemGeneral($itemid, $itemname, $itemdescription='', $recommended=0, $webview=1, $article='') {
		global $dbprefix;

		$itemid = sql_html_to_int($itemid);
		$itemname = sql_html_to_string($itemname);
		$itemdescription = sql_html_to_string($itemdescription);
		$article = sql_html_to_string($article);
		$recommended = sql_html_to_int($recommended);
		$webview = sql_html_to_int($webview);

		$sql  = "UPDATE {$dbprefix}item \n";
		$sql .= "SET ItemName = '$itemname', \n";
		$sql .=     "Description = '$itemdescription', \n";
		$sql .=     "Recommended = $recommended, \n";
		$sql .=     "Article = '$article', \n";
		$sql .=     "WebView = $webview \n";
		$sql .= "WHERE ItemID = $itemid \n";
		//die(nl2br($sql));
		db_query($sql);
	}


	function spDeleteItem($itemid) {
		global $dbprefix;
		$itemid = sql_html_to_int($itemid);
		$deletedate = date('Y-m-d H:i:s');

		$sql  = "UPDATE {$dbprefix}item SET DeleteDate = $deletedate WHERE ItemID = $itemid \n";
		db_query($sql);

		//cascade delete
		spDeleteItemDetails(0, $itemid, $deletedate);
		$sql  = "UPDATE {$dbprefix}itemcategory SET DeleteDate = $deletedate WHERE ItemID = $itemid \n";
		db_query($sql);
	}


	function spUnDeleteItem($itemid) {
		global $dbprefix;
		$itemid = sql_html_to_int($itemid);

		//find out when item was deleted
		$sql  = "SELECT DeleteDate ";
		$sql .= "FROM {$dbprefix}item ";
		$sql .= "WHERE ItemID=$itemid ";
		$rs = db_query($sql);
		$r = db_fetch_array($rs);
		$deletedate = $r['DeleteDate'];

		$sql  = "UPDATE {$dbprefix}item ";
		$sql .= "SET DeleteDate = 0 ";
		$sql .= "WHERE ItemID = $itemid ";
		db_query($sql);

		//cascade undelete details
		spUnDeleteItemDetails(0, $itemid, $deletedate);

		//cascade undelete categories
		$sql  = "UPDATE {$dbprefix}itemcategory ";
		$sql .= "SET DeleteDate = 0 ";
		$sql .= "WHERE ItemID = $itemid ";
		$sql .= "AND DeleteDate = '$deletedate' ";
		db_query($sql);
	}


?>