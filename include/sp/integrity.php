<?php

	function spIntegrityCheck() {
		global $OPT, $dbprefix;

		//$chance = 1/$OPT->integritychance;

		//spIntegrity_ItemsNoDetails();

	}

	function spIntegrity_ItemsNoDetails() {
		global $OPT, $dbprefix;

		$sql  = "DROP TABLE IF EXISTS {$dbprefix}integrity";
		//db_query($sql);
		$sql  = "CREATE TEMPORARY TABLE {$dbprefix}integrity (EntityID integer(11)) ";
		//db_query($sql);

		$sql  = "INSERT INTO {$dbprefix}integrity ";
		$sql .= "SELECT i.ItemID ";
		$sql .= "FROM {$dbprefix}item AS i ";
		$sql .= "LEFT JOIN {$dbprefix}detail AS d ON i.ItemID=d.ItemID ";
		$sql .= "WHERE d.DetailID IS NULL ";
		//db_query($sql);
	}

?>