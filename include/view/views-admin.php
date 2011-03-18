<?php

	function view_supplier($supplierID=0, $primaryonly=false) {
		global $OPT, $dbprefix;

		$sql  = "SELECT s.SupplierID, s.SupplierName, s.Description, s.IsPrimary, COUNT(ds.SupplierID) AS DetailCount \n";
		$sql .= "FROM {$dbprefix}supplier s \n";
		$sql .= "LEFT JOIN {$dbprefix}detailsupplier ds ON s.SupplierID=ds.SupplierID \n";
		$sql .= "WHERE 1=1 \n";
		if($supplierid!=0) $sql .= "AND SupplierID=$supplierID \n";
		if($primaryonly) $sql .= "AND s.IsPrimary!=0 \n";
		$sql .= "GROUP BY s.SupplierID \n";
		$sql .= "ORDER BY s.SupplierName \n";
		return db_query($sql);
	}

  function view_detailsuppliers($detailID) {
	
    global $OPT, $dbprefix;

		$sql  = "SELECT s.SupplierID, s.SupplierName, ";
		$sql .= "ds.Price, ds.BaseDiscount, ds.ExtraDiscount \n";
		$sql .= "FROM {$dbprefix}detailsupplier ds, {$dbprefix}supplier s \n";
		$sql .= "WHERE ds.SupplierID=s.SupplierID \n";
		$sql .= "AND ds.DetailID=$detailID \n";
		return db_query($sql);
	}

	function view_orders($orderid=0) {
		global $OPT, $dbprefix;
		$key=$OPT->storedatebasekey;

		$sql  = "SELECT o.OrderID, o.OrderTotal, o.OrderDate, o.DeliveryDate, o.PostageCharged, o.IsNew, o.CustomerNotified, ";
		$sql .=        "c.FirstName, c.LastName, c.Email, c.DayPhone, ";
		$sql .=        "c.InvAddress1, c.InvAddress2, c.InvAddress3, c.InvTown, c.InvCounty, c.InvPostcode, ";
		$sql .=        "c.DelAddress1, c.DelAddress2, c.DelAddress3, c.DelTown, c.DelCounty, c.DelPostcode, ";
		$sql .=			"c.ccInfoAvailable, ";
		$sql .=        "DECODE(c.ccType, '$key') AS ccType, ";
		$sql .=        "DECODE(c.ccName, '$key') AS ccName, ";
		$sql .=        "DECODE(c.ccNo, '$key') AS ccNo, ";
		$sql .=        "DECODE(c.ccIssue, '$key') AS ccIssue, ";
		$sql .=        "DECODE(c.ccIssMonth, '$key') AS ccIssMonth, ";
		$sql .=        "DECODE(c.ccIssYear, '$key') AS ccIssYear, ";
		$sql .=        "DECODE(c.ccExpMonth, '$key') AS ccExpMonth, ";
		$sql .=        "DECODE(c.ccExpYear, '$key') AS ccExpYear, ";
		$sql .=        "DECODE(c.ccCode, '$key') AS ccCode, ";
		$sql .=        "pt.PaymentTypeName, \n";
		$sql .=        "pz.PostageZoneID, pz.PostageZoneName \n";
		$sql .= "FROM {$dbprefix}order AS o \n";
		$sql .= "LEFT JOIN {$dbprefix}customer AS c ON o.CustomerID=c.CustomerID \n";
		$sql .= "LEFT JOIN {$dbprefix}paymenttype AS pt ON DECODE(c.ccType, '$key')=pt.PaymentTypeID \n";
		$sql .= "LEFT JOIN {$dbprefix}postagezone AS pz ON o.PostageZoneID=pz.PostageZoneID \n";
		if($orderid!=0) $sql .= "WHERE OrderID=$orderid \n";
		$sql .= "ORDER BY o.OrderID DESC \n";
		//echo(nl2br($sql));
		return db_query($sql);
	}

	function view_orderlines($orderid) {
		global $OPT, $dbprefix;

		$sql  = "SELECT ol.OrderLineID, ol.OrderID, ol.Quantity, ol.PricePaid, ";
		$sql .=        "d.DetailID, d.DetailName, d.StockCode, ";
		$sql .=        "c2.StockCodePrefix, ";
		$sql .=        "i.ItemID, i.ItemName \n";
		$sql .= "FROM (((({$dbprefix}orderline AS ol \n";
		$sql .=   "LEFT JOIN {$dbprefix}detail AS d ON ol.DetailID=d.DetailID) ";
		$sql .=   "LEFT JOIN {$dbprefix}item AS i ON d.ItemID=i.ItemID) \n";
		$sql .=   "LEFT JOIN {$dbprefix}itemcategory ic ON (d.ItemID=ic.ItemID AND ic.IsPrimary!=0)) \n";
		$sql .=   "LEFT JOIN {$dbprefix}category AS c ON ic.CategoryID=c.CategoryID) \n";
		$sql .=   "LEFT JOIN {$dbprefix}category AS c2 ON c.ParentID=c2.CategoryID \n";
		$sql .= "WHERE ol.OrderID=$orderid \n";
		$sql .= "AND ol.OrderLineTypeID=1 \n";
		//echo(nl2br($sql));
		return db_query($sql);
	}

	function view_orderpostagelines($orderid) {
		global $OPT, $dbprefix;

		$sql  = "SELECT ol.OrderLineID, ol.OrderID, ol.Quantity, ol.PricePaid, ol.OrderLineText \n";
		$sql .= "FROM {$dbprefix}orderline AS ol \n";
		$sql .= "WHERE ol.OrderID=$orderid \n";
		$sql .= "AND ol.OrderLineTypeID=2 \n";
		//echo(nl2br($sql));
		return db_query($sql);
	}

	function view_ordermessages($orderid) {
		global $OPT, $dbprefix;

		$orderid =sql_html_to_int($orderid);

		$sql  = "SELECT om.MessageID, om.OrderID, om.SentTo, om.Subject, DATE_FORMAT(om.CreateDate, \"%Y-%m-%d %H:%i:%S\") CreateDate \n";
		$sql .= "FROM {$dbprefix}ordermessage AS om \n";
		$sql .= "WHERE om.OrderID=$orderid \n";
		//echo(nl2br($sql));
		return db_query($sql);
	}

	function view_history($entitytype='', $entityid=0, $limit=0) {
		global $OPT, $dbprefix;

		$entityid=sql_html_to_int($entityid);
		$limit=sql_html_to_int($limit);

		$entitytype=sql_html_to_string($entitytype);
		if(strpos($entitytype,',')!==false) {
			$a=explode(',',$entitytype);
			$entitytype="'".implode("','", $a)."'";
		} else {
		}

		$sql  = "SELECT EntityType, EntityID, EntityName, COUNT(HistoryID) AS Counter \n";
		$sql .= "FROM {$dbprefix}history \n";
		if(strpos($entitytype,',')!==false) {
			$sql .= "WHERE EntityType IN ($entitytype) \n";
		} else {
			if($entitytype!='') $sql .= "WHERE EntityType='$entitytype' \n";
		}
		$sql .= "GROUP BY EntityName \n";
		$sql .= "ORDER BY Counter DESC \n";
		if($limit!=0) $sql .= "LIMIT $limit \n";

		//echo(nl2br($sql));
		return db_query($sql);
	}

	function view_itempictures($itemid) {
		global $OPT, $dbprefix;

		$itemid=sql_html_to_int($itemid);

		$sql  = "SELECT ip.ItemPictureID, ip.ItemID, ip.DetailID, ip.PictureID, ip.IsPrimary, \n";
		$sql .= "d.DetailName, \n";
		$sql .= "p.PictureName, p.FileName \n";
		$sql .= "FROM {$dbprefix}itempicture AS ip \n";
		$sql .= "LEFT JOIN {$dbprefix}picture AS p ON (ip.PictureID=p.PictureID) \n";
		$sql .= "LEFT JOIN {$dbprefix}detail AS d ON (ip.DetailID=d.DetailID) \n";
		$sql .= "WHERE ip.ItemID=$itemid \n";
		if(!$PAGEOPT['deletedvis']) {
			$sql .= "AND ip.DeleteDate=0 \n";
			$sql .= "AND p.DeleteDate=0 \n";
		}

		return db_query($sql);
	}

	function view_pictures($pictureid=0) {
		global $OPT, $dbprefix;

		$pictureid=sql_html_to_int($pictureid);

		$sql  = "SELECT PictureID, PictureName, FileName, CreateDate, ModifyDate, DeleteDate \n";
		$sql .= "FROM {$dbprefix}picture \n";
		if($pictureid!=0) $sql .= "WHERE PictureID=$pictureid \n";
		$sql .= "ORDER BY PictureName \n";

		return db_query($sql);
	}

	function view_units($unitid=0) {
		global $OPT, $dbprefix;

		$unitid = sql_html_to_int($unitid);

		$sql  = "SELECT UnitID, UnitName \n";
		$sql .= "FROM {$dbprefix}unit \n";
		if($unitid!=0) $sql .= "WHERE UnitID=$unitid \n";

		return db_query($sql);
	}

?>