<?php

// ----------------------------------------------------------------------------

	function spInsertSupplier($name, $description) {
		global $dbprefix;
		$name = sql_html_to_string($name);
		$description = sql_html_to_string($description);

		$sql  = "INSERT INTO {$dbprefix}supplier \n";
		$sql .= "(SupplierName, Description) \n";
		$sql .= "VALUES ('$name', '$description') \n";
		db_query($sql);
		return db_insert_id();
	}

// ----------------------------------------------------------------------------

	function spUpdateSupplier($id, $name, $description, $primary) {
		global $dbprefix;
		$id = sql_html_to_int($id);
		$name = sql_html_to_string($name);
		$description = sql_html_to_string($description);
		$primary = sql_html_to_int($primary);

		if($primary) {
			$sql  = "UPDATE {$dbprefix}supplier \n";
			$sql .= "SET IsPrimary = 0 \n";
			db_query($sql);
		}

		$sql  = "UPDATE {$dbprefix}supplier \n";
		$sql .= "SET SupplierName = '$name', \n";
		$sql .= "Description = '$description', \n";
		$sql .= "IsPrimary = $primary \n";
		$sql .= "WHERE SupplierID=$id \n";
		//echo($sql."<br>");
		db_query($sql);
	}

// ----------------------------------------------------------------------------

	function spDeleteSupplier($id, $deletedate=0) {
		global $dbprefix;
		$id = sql_html_to_int($id);
		$deletedate = sql_html_to_string($deletedate);

		if(empty($deletedate)) $deletedate = date('Y-m-d H:i:s');

		$sql  = "UPDATE {$dbprefix}supplier \n";
		$sql .= "SET DeleteDate=$deletedate \n";
		$sql .= "WHERE SupplierID=$id \n";
		//echo($sql."<br>");
		db_query($sql);
	}

// ----------------------------------------------------------------------------

	function spInsertDetailSupplier($detailid, $supplierid, $price=0, $base=0, $extra=0) {
		global $dbprefix;
		//log_sp("spInsertDetailSupplier","$detailid, $supplierid, $price");
		$detailid = sql_html_to_int($detailid);
		$supplierid = sql_html_to_int($supplierid);
		$price = sql_html_to_float($price);
		$base = sql_html_to_float($base);
		$extra = sql_html_to_float($extra);

		$sql  = "INSERT INTO {$dbprefix}detailsupplier \n";
		$sql .= "(DetailID, SupplierID, Price, BaseDiscount, ExtraDiscount, CreateDate) \n";
		$sql .= "VALUES ($detailid, $supplierid, $price, $base, $extra, NOW()) \n";
		//echo(nl2br($sql));
		db_query($sql);
		return db_insert_id();
	}

// ----------------------------------------------------------------------------

	function spUpdateDetailSupplier($detailid, $supplierid, $price=0, $base=0, $extra=0) {
		global $dbprefix;
		$detailid = sql_html_to_int($detailid);
		$supplierid = sql_html_to_int($supplierid);
		$price = sql_html_to_float($price);
		$base = sql_html_to_float($base);
		$extra = sql_html_to_float($extra);

		$sql  = "UPDATE {$dbprefix}detailsupplier \n";
		$sql .= "SET Price=$price, \n";
		$sql .= "BaseDiscount=$base, \n";
		$sql .= "ExtraDiscount=$extra \n";
		$sql .= "WHERE DetailID=$detailid \n";
		$sql .= "AND SupplierID=$supplierid \n";
		//echo(nl2br($sql));
		db_query($sql);
	}

// ----------------------------------------------------------------------------

	function spDeleteDetailSupplier($detailid, $supplierid=0) {
		global $dbprefix;
		$detailid = sql_html_to_int($detailid);
		$supplierid = sql_html_to_int($supplierid);

		$sql  = "DELETE FROM {$dbprefix}detailsupplier \n";
		$sql .= "WHERE DetailID=$detailid \n";
		if($supplierid!=0) $sql .= "AND SupplierID=$supplierid \n";
		db_query($sql);
	}

// ----------------------------------------------------------------------------

?>