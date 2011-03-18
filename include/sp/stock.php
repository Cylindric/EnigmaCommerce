<?php

// ----------------------------------------------------------------------------
	//inserts the specified item into stock using the given supplier and price.
	//if the supplier isn't already listed as main supplier it is added
	function spInsertStock($detailid, $supplierid, $date, $price, $qtybuy, $qtyrem) {
		global $dbprefix;
		$detailid   = sql_html_to_int($detailid);
		$supplierid = sql_html_to_int($supplierid);
		$qtybuy     = sql_html_to_int($qtybuy);
		$qtyrem     = sql_html_to_int($qtyrem);
		$price      = sql_html_to_float($price);
		$date       = sql_html_to_date($date);

		$sql  = "INSERT INTO {$dbprefix}stock \n";
		$sql .= "(DetailID, SupplierID, PricePaid, QuantityPurchased, QuantityRemaining, DeliveryDate) \n";
		$sql .= "VALUES ($detailid, $supplierid, $price, $qtybuy, $qtyrem, '$date') \n";
		//echo(nl2br($sql));
		db_query($sql);
		$newstockid = db_insert_id();


		//add supplier as stockist if not already
		$supplierexists = false;
		$rs = view_detailsuppliers($detailid);
		while($r = db_fetch_array($rs)) {
			$dbsupplierid = $r['SupplierID'];
			if($dbsupplierid==$supplierid) {
				$supplierexists = true;
				break;
			}
		}
		if(!$supplierexists) {
			spInsertDetailSupplier($detailid, $supplierid, $price, 0, 0);
		}

		return $newstockid;
	}

// ----------------------------------------------------------------------------

	function spUpdateStock($id, $name, $description) {
		global $dbprefix;
		$id = sql_html_to_int($id);
		$name = sql_html_to_string($name);
		$description = sql_html_to_string($description);

		//$sql  = "UPDATE {$dbprefix}stock \n";
		//$sql .= "SET StockName = '$name', \n";
		//$sql .= "Description = '$description' \n";
		//$sql .= "WHERE StockID=$id \n";
		//echo($sql."<br>");
		//db_query($sql);
	}

// ----------------------------------------------------------------------------

	function spDeleteStock($id) {
		global $dbprefix;
		$id = sql_html_to_int($id);

		$sql  = "DELETE FROM {$dbprefix}stock \n";
		$sql .= "WHERE StockID=$id \n";
		//echo(nl2br($sql));
		db_query($sql);
	}

// ----------------------------------------------------------------------------

	function spInsertDetailStock($detailid, $stockid, $price=0, $base=0, $extra=0) {
		global $dbprefix;
		$detailid = sql_html_to_int($detailid);
		$stockid = sql_html_to_int($stockid);
		$price = sql_html_to_float($price);
		$base = sql_html_to_float($base);
		$extra = sql_html_to_float($extra);

		//$sql  = "INSERT INTO {$dbprefix}detailstock \n";
		//$sql .= "(DetailID, StockID, Price, BaseDiscount, ExtraDiscount) \n";
		//$sql .= "VALUES ($detailid, $stockid, $price, $base, $extra) \n";
		//db_query($sql);
		//return db_insert_id();
	}

// ----------------------------------------------------------------------------

	function spUpdateDetailStock($detailid, $stockid, $price=0, $base=0, $extra=0) {
		global $dbprefix;
		$detailid = sql_html_to_int($detailid);
		$stockid = sql_html_to_int($stockid);
		$price = sql_html_to_float($price);
		$base = sql_html_to_float($base);
		$extra = sql_html_to_float($extra);

		//$sql  = "UPDATE {$dbprefix}detailstock \n";
		//$sql .= "SET Price=$price, \n";
		//$sql .= "BaseDiscount=$base, \n";
		//$sql .= "ExtraDiscount=$extra \n";
		//$sql .= "WHERE DetailID=$detailid \n";
		//$sql .= "AND StockID=$stockid \n";
		//echo(nl2br($sql));
		//db_query($sql);
	}

// ----------------------------------------------------------------------------

	function spDeleteDetailStock($detailid, $stockid) {
		global $dbprefix;
		$detailid = sql_html_to_int($detailid);
		$stockid = sql_html_to_int($stockid);

		//$sql  = "DELETE FROM {$dbprefix}detailstock \n";
		//$sql .= "WHERE DetailID=$detailid \n";
		//if($stockid!=0) $sql .= "AND StockID=$stockid \n";
		//db_query($sql);
	}

// ----------------------------------------------------------------------------

?>