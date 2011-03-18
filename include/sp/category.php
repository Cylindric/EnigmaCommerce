<?php

	function spInsertCategory($name, $description, $parentid, $webview) {
		global $dbprefix;
		$name = sql_html_to_string($name);
		$description = sql_html_to_string($description);
		$parentid = sql_html_to_int($parentid);
		$webview = sql_html_to_bit($webview);
		$sql  = "INSERT INTO {$dbprefix}category \n";
		$sql .= "(CategoryName, Description, ParentID, WebView) \n";
		$sql .= "VALUES ('$name', '$description', '$parentid', '$webview') \n";
		db_query($sql);

		$newcategoryid=db_insert_id();
		spSetStockCode(0, $newcategoryid);
		return $newcategoryid;
	}

	function spUpdateCategory($categoryid, $name, $description, $parentid, $webview) {
		global $dbprefix;
		$categoryid  = sql_html_to_int($categoryid);
		$name        = sql_html_to_string($name);
		$description = sql_html_to_string($description);
		$parentid    = sql_html_to_int($parentid);
		$webview     = sql_html_to_bit($webview);

		$sql  = "UPDATE {$dbprefix}category \n";
		$sql .= "SET CategoryName='$name', \n";
		$sql .= "    Description='$description', \n";
		$sql .= "    ParentID=$parentid, \n";
		$sql .= "    WebView=$webview \n";
		$sql .= "WHERE CategoryID=$categoryid \n";
		db_query($sql);
	}

	function spDeleteCategory($categoryid) {
		global $dbprefix;
		$categoryid = sql_html_to_int($categoryid);

		//mark the category as deleted
		$sql  = "UPDATE {$dbprefix}category \n";
		$sql .= "SET DeleteDate=NOW() \n";
		$sql .= "WHERE CategoryID=$categoryid \n";
		db_query($sql);
	}

	function spUnDeleteCategory($categoryid) {
		global $dbprefix;
		$categoryid = sql_html_to_int($categoryid);

		//mark the category as not deleted
		$sql  = "UPDATE {$dbprefix}category \n";
		$sql .= "SET DeleteDate=0 \n";
		$sql .= "WHERE CategoryID=$categoryid \n";
		db_query($sql);
	}

?>