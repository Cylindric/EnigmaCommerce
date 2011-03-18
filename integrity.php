<?php

	if(!authorised()) {
		die('unauthorised access');
	}
	
	//    /integrity/action/tests
	//    /integrity/check/all
	//
	// tests are:
	//    all
	//    itemcat1
	//    itemcat2
	//    detailitem
	//    itemsupplier1
	//    itemsupplier2
	//    itemnocat
		
	$page_action = 'check';
	if($ctrl[1] == 'fix') {
		$page_action = 'fix';
	}
	
	$page_tests = $ctrl[2];
	if(empty($page_tests)) {
		$page_tests = 'all';
	}

	$show_sql = (bool)$ctrl[3];

	echo("<br>Action: $page_action<br>Tests: $page_tests<br>");

	//update all orphaned records
	echo("<h1>Orphans</h1>");
	
	//itemcategories with no item
	if(($page_tests == 'all') || ($page_tests == 'itemcat1')) {
		echo('<h3>Item/Category links with bad items</h3>');
		$sql  = "SELECT ic.ItemID, ic.CategoryID ";
		$sql .= "FROM {$dbprefix}itemcategory ic ";
		$sql .= "LEFT JOIN {$dbprefix}item i ON (ic.ItemID = i.ItemID) ";
		$sql .= "LEFT JOIN {$dbprefix}category c ON (ic.CategoryID = c.CategoryID) ";
		$sql .= "WHERE i.ItemID IS NULL ";
		if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
		$rs = db_query($sql);
		while($r = db_fetch_array($rs)) {
			$itemid = $r['ItemID'];
			$categoryid = $r['CategoryID'];
			echo("Item $itemid does not exist in category $categoryid<br>");
			if($page_action == 'fix') {
				$sql = "DELETE FROM {$dbprefix}itemcategory WHERE ItemID = $itemid AND CategoryID = $categoryid ";
				if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
				db_query($sql);
			}
		}
	}
	
	//itemcategories with no category
	if(($page_tests == 'all') || ($page_tests == 'itemcat2')) {
		echo('<h3>Item/Category links with bad categories</h3>');
		$sql  = "SELECT ic.ItemID, ic.CategoryID, i.ItemName ";
		$sql .= "FROM {$dbprefix}itemcategory ic ";
		$sql .= "LEFT JOIN {$dbprefix}item i ON (ic.ItemID = i.ItemID) ";
		$sql .= "LEFT JOIN {$dbprefix}category c ON (ic.CategoryID = c.CategoryID) ";
		$sql .= "WHERE c.CategoryID IS NULL ";
		if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
		$rs = db_query($sql);
		while($r = db_fetch_array($rs)) {
			$itemid = $r['ItemID'];
			$itemname = $r['ItemName'];
			$categoryid = $r['CategoryID'];
			echo("Category $categoryid does not exist in \"<a href=\"{$OPT->enigmaroot}{$OPT->controlpage}/item/{$itemid}/1#item_{$itemid}\" target=\"_blank\">$itemname</a>\"<br>");
			if($page_action == 'fix') {
				echo(" fixing...");
				$sql = "DELETE FROM {$dbprefix}itemcategory WHERE ItemID = $itemid AND CategoryID = $categoryid ";
				if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
				db_query($sql);
			}
		}
	}
	
	//details with no item
	if(($page_tests == 'all') || ($page_tests == 'detailitem')) {
		echo('<h3>Details with bad items</h3>');
		$sql  = "SELECT d.DetailID, d.ItemID ";
		$sql .= "FROM {$dbprefix}detail d ";
		$sql .= "LEFT JOIN {$dbprefix}item i ON (d.ItemID = i.ItemID) ";
		$sql .= "WHERE i.ItemID IS NULL ";
		if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
		$rs = db_query($sql);
		while($r = db_fetch_array($rs)) {
			$detail = $r['DetailID'];
			$itemid = $r['ItemID'];
			echo("The detail $detail has a non-existant item $itemid<br>");
			if($page_action == 'fix') {
				$sql = "DELETE FROM {$dbprefix}detail WHERE DetailID = $detail ";
				if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
				db_query($sql);
				}
		}
	}

	//detail/supplier with no supplier)
	if(($page_tests == 'all') || ($page_tests == 'itemsupplier1')) {
		echo('<h3>Detail/Supplier links with bad suppliers</h3>');
		$sql  = "SELECT ds.DetailID, ds.SupplierID ";
		$sql .= "FROM {$dbprefix}detailsupplier ds ";
		$sql .= "LEFT JOIN {$dbprefix}detail d ON (d.DetailID = ds.DetailID) ";
		$sql .= "LEFT JOIN {$dbprefix}supplier s ON (ds.SupplierID = s.SupplierID) ";
		$sql .= "WHERE s.SupplierID IS NULL ";
		if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
		$rs = db_query($sql);
		while($r = db_fetch_array($rs)) {
			$detail = $r['DetailID'];
			$supplierid = $r['SupplierID'];
			echo("The detail $detail has a non-existant supplier $supplierid<br>");
			if($page_action == 'fix') {
				$sql = "DELETE FROM {$dbprefix}detailsupplier WHERE DetailID = $detail AND SupplierID = $supplierid ";
				if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
				db_query($sql);
			}
		}
	}


	//detail/supplier with no detail)
	if(($page_tests == 'all') || ($page_tests == 'itemsupplier2')) {
		echo('<h3>Detail/Supplier links with bad details</h3>');
		$sql  = "SELECT ds.DetailID, ds.SupplierID ";
		$sql .= "FROM {$dbprefix}detailsupplier ds ";
		$sql .= "LEFT JOIN {$dbprefix}detail d ON (d.DetailID = ds.DetailID) ";
		$sql .= "LEFT JOIN {$dbprefix}supplier s ON (ds.SupplierID = s.SupplierID) ";
		$sql .= "WHERE d.DETAILID IS NULL ";
		if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
		$rs = db_query($sql);
		while($r = db_fetch_array($rs)) {
			$detail = $r['DetailID'];
			$supplierid = $r['SupplierID'];
			echo("The supplier $supplierid has a non-existant detail $detail<br>");
			if($page_action == 'fix') {
				$sql = "DELETE FROM {$dbprefix}detailsupplier WHERE DetailID = $detail AND SupplierID = $supplierid ";
				if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
				db_query($sql);
			}
		}
	}
	
	//items with no category
	if(($page_tests == 'all') || ($page_tests == 'itemnocat')) {
		echo('<h3>Items with no categories (fix will place item in "Database Errors" category)</h3>');
		$sql  = "SELECT i.ItemID, i.ItemName, COUNT(ic.ItemID) CategoryCount ";
		$sql .= "FROM {$dbprefix}item i ";
		$sql .= "LEFT JOIN {$dbprefix}itemcategory ic ON (i.ItemID = ic.ItemID) ";
		$sql .= "GROUP BY i.ItemID ";
		$sql .= "HAVING CategoryCount = 0 ";
		$sql .= "ORDER BY CategoryCount ";
		if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
		$rs = db_query($sql);
		while($r = db_fetch_array($rs)) {
			$item = $r['ItemID'];
			$itemname = $r['ItemName'];
			echo("The Item \"$itemname\" ($item) has no categories<br>");
			if($page_action == 'fix') {
				$sql = "INSERT INTO {$dbprefix}itemcategory (ItemID, CategoryID, IsPrimary, CreateDate) VALUES ($item, {$OPT->cat_databaseerrors}, 1, NOW()) ";
				if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
				db_query($sql);
			}
		}
	}	

	//items with no primary category
	if(($page_tests == 'all') || ($page_tests == 'itemnocat')) {
		echo('<h3>Items with no primary categories (fix will mark first category primary)</h3>');
		$sql  = "SELECT i.ItemID, i.ItemName, COUNT(ic.ItemID) CategoryCount ";
		$sql .= "FROM {$dbprefix}item i ";
		$sql .= "LEFT JOIN {$dbprefix}itemcategory ic ON (i.ItemID = ic.ItemID) ";
		$sql .= "WHERE ic.IsPrimary <> 0 ";
		$sql .= "GROUP BY i.ItemID ";
		$sql .= "HAVING CategoryCount = 0 ";
		$sql .= "ORDER BY CategoryCount ";
		if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
		$rs = db_query($sql);
		while($r = db_fetch_array($rs)) {
			$item = $r['ItemID'];
			$itemname = $r['ItemName'];
			echo("The Item \"$itemname\" ($item) has no categories<br>");
			if($page_action == 'fix') {
				$sql = "UPDATE {$dbprefix}itemcategory SET IsPrimary = 1 WHERE ItemID = $item LIMIT 1 ";
				if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
				db_query($sql);
			}
		}
	}	
	
	//check stock codes
	echo("<h1>Stock Codes</h1>");

	if(($page_tests == 'all') || ($page_tests == 'stockcodes')) {
		echo('<h3>Duplicate stock codes</h3>');
		$sql  = "SELECT c1.StockCodePrefix, d.StockCode, COUNT(*) CodeCount ";
		$sql .= "FROM {$dbprefix}detail d ";
		$sql .= "INNER JOIN {$dbprefix}item i ON (d.ItemID=i.ItemID) ";
		$sql .= "INNER JOIN {$dbprefix}itemcategory ic ON (d.ItemID=ic.ItemID AND ic.IsPrimary!=0) ";
		$sql .= "INNER JOIN {$dbprefix}category c2 ON ic.CategoryID=c2.CategoryID ";
		$sql .= "INNER JOIN {$dbprefix}category c1 ON c2.ParentID=c1.CategoryID ";
		$sql .= "GROUP BY c1.StockCodePrefix, d.StockCode ";
		$sql .= "HAVING CodeCount > 1 ";
		$sql .= "ORDER BY CodeCount DESC, c1.StockCodePrefix, d.StockCode ";
		if($show_sql) echo("<code style=\"color:red;\">$sql</code><br>");
		$rs = db_query($sql);

		if(db_num_rows($rs) > 0) {
			echo('<table>');
			echo('<tr><td>Code</td><td class="numeric">Count</td><td>&nbsp;</td></tr>');

			while($r = db_fetch_array($rs)) {
				$sc1 = $r['StockCodePrefix'];
				$sc2 = $r['StockCode'];
				$stockcode = format_stockcode($sc1, $sc2);
				$count = $r['CodeCount'];

				$sql  = "SELECT i.ItemName, d.DetailID, d.DetailName ";
				$sql .= "FROM {$dbprefix}detail d ";
				$sql .= "INNER JOIN {$dbprefix}item i ON (d.ItemID = i.ItemID) ";
				$sql .= "INNER JOIN {$dbprefix}itemcategory ic ON (d.ItemID = ic.ItemID AND ic.IsPrimary <> 0) ";
				$sql .= "INNER JOIN {$dbprefix}category c2 ON (ic.CategoryID = c2.CategoryID) ";
				$sql .= "INNER JOIN {$dbprefix}category c1 ON (c1.CategoryID = c2.ParentID) ";
				$sql .= "WHERE d.StockCode = $sc2 ";
				$sql .= "AND c1.StockCodePrefix = $sc1 ";
				$sql .= "ORDER BY i.ItemName, d.DetailName ";
				$rs2 = db_query($sql);
	
				echo("<tr><td>$stockcode</td><td class=\"numeric\">$count</td><td>&nbsp;</td></tr>");
				$codes_fixed = 0;
				while($r2 = db_fetch_array($rs2)) {
					$itemname = $r2['ItemName'];
					$detailname = $r2['DetailName'];
					$detailid = $r2['DetailID'];
					echo("<tr><td>&nbsp;</td><td>$detailid</td><td>$itemname - $detailname");
	
					if($page_action == 'fix') {
						//change all-but-one of the codes
						if($codes_fixed < $count-1) {
							echo(' fixing...');
							$newcode = spSetStockCode($detailid);
							echo(" $newcode");
							$codes_fixed++;
						}
						
					}
					echo("</td></tr>");
				}
			}
			echo('</table>');
		}
	}


?>