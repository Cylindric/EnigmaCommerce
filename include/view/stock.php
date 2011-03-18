<?php

// ----------------------------------------------------------------------------
	function view_stock($itemid=0) {
		global $dbprefix;
		$itemid = sql_html_to_int($itemid);

		$sql  = "SELECT s.StockID, s.DetailID, s.SupplierID, s.DeliveryDate, s.PricePaid, s.QuantityPurchased, s.QuantityRemaining, ";
		$sql .=        "supp.SupplierName, \n";
		$sql .=        "d.DetailName \n";
		$sql .= "FROM {$dbprefix}stock AS s \n";
		$sql .= "LEFT JOIN {$dbprefix}detail AS d ON (s.DetailID=d.DetailID) \n";
		$sql .= "LEFT JOIN {$dbprefix}supplier AS supp ON (s.SupplierID=supp.SupplierID) \n";
		$sql .= "LEFT JOIN {$dbprefix}unit AS u ON (d.UnitID=u.UnitID) \n";
		if($itemid!=0) $sql .= "WHERE d.ItemID=$itemid \n";
		$sql .= "ORDER BY (d.Size*u.BaseMultiple), d.DetailName, s.DeliveryDate DESC \n";

		return db_query($sql);
	}

// ----------------------------------------------------------------------------

?>