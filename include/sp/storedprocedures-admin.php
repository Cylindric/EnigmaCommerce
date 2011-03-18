<?php

// ==================================================================
//     STORED PROCEDURES TO INSERT/UPDATE/DELETE ITEM CATEGORIES
// ==================================================================
	function spInsertItemCategory($itemid, $categoryid=0, $primary=0) {
		global $dbprefix;
		//log_sp("spInseritemcategory","$itemid, $categoryid, $primary");
		$itemid = sql_html_to_int($itemid);
		$categoryid = sql_html_to_int($categoryid);
		$primary = sql_html_to_int($primary);

		//first try 'undeleting' an existing hidden link
		$sql  = "SELECT ItemID, CategoryID \n";
		$sql .= "FROM {$dbprefix}itemcategory \n";
		$sql .= "WHERE ItemID=$itemid \n";
		$sql .= "AND CategoryID=$categoryid \n";
		$rs = db_query($sql);
		if(db_num_rows($rs)>0) {
			$sql  = "UPDATE {$dbprefix}itemcategory \n";
			$sql .= "SET DeleteDate=0 \n";
			$sql .= "WHERE ItemID=$itemid \n";
			$sql .= "AND CategoryID=$categoryid \n";
			db_query($sql);

		} else {
			//othwise jus add a new one
			$sql  = "INSERT INTO {$dbprefix}itemcategory \n";
			$sql .=    "(ItemID, \n";
			$sql .=     "CategoryID, \n";
			$sql .=     "CreateDate) \n";
			$sql .= "VALUES \n";
			$sql .=    "($itemid, \n";
			$sql .=     "$categoryid, \n";
			$sql .=     "NOW()) \n";
			db_query($sql);
		}

		//make sure exactly one category is primary
		if($primary) {
			$sql  = "UPDATE {$dbprefix}itemcategory \n";
			$sql .= "SET IsPrimary=0 \n";
			$sql .= "WHERE ItemID=$itemid \n";
			db_query($sql);
			$sql  = "UPDATE {$dbprefix}itemcategory \n";
			$sql .= "SET IsPrimary=-1 \n";
			$sql .= "WHERE ItemID=$itemid \n";
			$sql .= "AND CategoryID=$categoryid \n";
			db_query($sql);
		}
	}


	function spUpdateItemCategory($itemid, $categoryid=0, $primary=0) {
		global $dbprefix;
		//log_sp("spUpdateItemCategory","$itemid, $categoryid, $primary");
		$itemid = sql_html_to_int($itemid);
		$categoryid = sql_html_to_int($categoryid);
		$primary = sql_html_to_int($primary);

		//make sure exactly one category is primary
		if($primary) {
			$sql  = "UPDATE {$dbprefix}itemcategory \n";
			$sql .= "SET IsPrimary=0 \n";
			$sql .= "WHERE ItemID=$itemid \n";
			db_query($sql);
			$sql  = "UPDATE {$dbprefix}itemcategory \n";
			$sql .= "SET IsPrimary=$primary \n";
			$sql .= "WHERE ItemID=$itemid \n";
			$sql .= "AND CategoryID=$categoryid \n";
			db_query($sql);
		}
	}


	//if only an itemid is specified, then all details for that item are
	//deleted.
	function spDeleteItemCategory($itemid, $categoryid=0) {
		global $dbprefix;
		//log_sp("spDeleteItemCategory","$itemid, $categoryid");
		$itemid = sql_html_to_int($itemid);
		$categoryid = sql_html_to_int($categoryid);

		$sql  = "UPDATE {$dbprefix}itemcategory \n";
		$sql .= "SET DeleteDate = NOW() \n";
		$sql .= "WHERE Itemid=$itemid \n";
		if($categoryid!=0) $sql .= "AND Categoryid=$categoryid \n";
		db_query($sql);
	}


	function spSetItemVisibility($itemid, $visibility) {
		global $dbprefix;
		$itemid = sql_html_to_int($itemid);
		$visibility = sql_html_to_int($visibility);

		$sql  = "UPDATE {$dbprefix}item \n";
		$sql .= "SET WebView = $visibility \n";
		$sql .= "WHERE Itemid=$itemid \n";
		db_query($sql);
	}


// ==================================================================
//        STORED PROCEDURES TO INSERT/UPDATE/DELETE SUPPLIERS
// ==================================================================

// ==================================================================
//      STORED PROCEDURES TO INSERT/UPDATE/DELETE POSTAGE CHARGES
// ==================================================================
	function spInsertDetailPostageCharge($detailid, $postagechargeid, $postagepallet, $postagecharge) {
		global $dbprefix;

		$detailid = sql_html_to_int($detailid);
		$postagechargeid = sql_html_to_int($postagechargeid);
		$postagepallet = sql_html_to_int($postagepallet);
		$postagecharge = sql_html_to_float($postagecharge);

		$sql  = "INSERT INTO {$dbprefix}detailpostagecharge \n";
		$sql .= "(DetailID, PostageChargeID, PalletThreshold, PostageCharge, CreateDate) \n";
		$sql .= "VALUES ($detailid, $postagechargeid, $postagepallet, $postagecharge, NOW()) \n";
		db_query($sql);
		return db_insert_id();
	}

	function spDeleteDetailPostageCharge($detailid, $postagechargeid) {
		global $dbprefix;

		$detailid = sql_html_to_int($detailid);
		$postagechargeid = sql_html_to_int($postagechargeid);

		$sql  = "DELETE FROM {$dbprefix}detailpostagecharge \n";
		$sql .= "WHERE DetailID=$detailid \n";
		$sql .= "AND PostageChargeID=$postagechargeid \n";
		db_query($sql);
	}



// ==================================================================
//       STORED PROCEDURES TO INSERT/UPDATE/DELETE STOCK CODES
// ==================================================================
	//this function will reset the stock code for either a detail or a
	//category, depending on which parameter is set
	function spSetStockCode($detailid=0, $categoryid=0, $clear=false) {
		global $dbprefix;

		if( ($detailid==0) && ($categoryid!=0) ) {
			if($clear) {
				//clear the codes first
				$sql = "UPDATE {$dbprefix}category SET StockCodePrefix=0 \n";
				db_query($sql);
			}
			//get the highest category code in use
			$sql  = "SELECT StockCodePrefix \n";
			$sql .= "FROM {$dbprefix}category \n";
			$sql .= "ORDER BY StockCodePrefix DESC \n";
			$sql .= "LIMIT 1";
			$rs=db_query($sql);
			$r=db_fetch_array($rs);
			$dbMaxStockCode=$r['StockCodePrefix'];

			$NewStockCode=$dbMaxStockCode+1;
			$sql  = "UPDATE {$dbprefix}category \n";
			$sql .= "SET StockCodePrefix=$NewStockCode \n";
			$sql .= "WHERE CategoryID=$categoryid \n";
			$rs=db_query($sql);

			return $NewStockCode;
		}

		if( ($detailid!=0) && ($categoryid==0) ) {
			if($clear) {
				//clear the codes first
				$sql = "UPDATE {$dbprefix}detail SET StockCode=0 \n";
				db_query($sql);
			}
			//get this detail's category id
			$sql  = "SELECT c.ParentID \n";
			$sql .= "FROM {$dbprefix}detail d \n";
			$sql .= "LEFT JOIN {$dbprefix}itemcategory ic ON (d.ItemID = ic.ItemID AND ic.IsPrimary != 0) \n";
			$sql .= "LEFT JOIN {$dbprefix}category c ON (ic.CategoryID = c.CategoryID) \n";
			$sql .= "WHERE d.DetailID = $detailid \n";
			$rs=db_query($sql);
 			$r=db_fetch_array($rs);
 			$dbParentCategoryID=$r['ParentID'];

 			//find highest stock code uesd in this parent category
			$sql  = "SELECT c1.CategoryID, MAX(d.StockCode) MaxStockCode \n";
			$sql .= "FROM {$dbprefix}category c2 \n";
			$sql .= "INNER JOIN {$dbprefix}category c1 ON (c1.CategoryID = c2.ParentID) \n";
			$sql .= "INNER JOIN {$dbprefix}itemcategory ic ON (c2.CategoryID = ic.CategoryID AND ic.IsPrimary <> 0) \n";
			$sql .= "INNER JOIN {$dbprefix}detail d ON (ic.ItemID = d.ItemID) \n";
			$sql .= "GROUP BY c1.CategoryID, c1.CategoryName \n";
			$sql .= "HAVING c1.CategoryID = {$dbParentCategoryID} \n";
			$rs = db_query($sql);
 			$r = db_fetch_array($rs);
 			$dbMaxStockCode = $r['MaxStockCode'];

 			//update the detail id with the new stock code
			$NewStockCode = $dbMaxStockCode + 1;
			$sql  = "UPDATE {$dbprefix}detail \n";
			$sql .= "SET StockCode = $NewStockCode \n";
			$sql .= "WHERE DetailID = $detailid \n";
			$rs = db_query($sql);

			return $NewStockCode;
		}

	}








	function spInsertPicture($picturename, $filename, $description, $imgprops) {
		global $OPT, $dbprefix;

		$picturename = sql_html_to_string($picturename);
		$filename = sql_html_to_string($filename);
		$description = sql_html_to_string($description);
		$width = sql_html_to_int($imgprops[0]);
		$height = sql_html_to_int($imgprops[1]);

		//insert the picture details
		$sql  = "INSERT INTO {$dbprefix}picture \n";
		$sql .= "(PictureName, FileName, Description, Width, Height, CreateDate) \n";
		$sql .= "VALUES ('$picturename', '$filename', '$description', $width, $height, NOW()) \n";
		db_query($sql);
		$newpictureid = db_insert_id();

		//update the name to reflect it's target location
		$fileextention = strstr($filename, '.');
		if($fileextention===false) $fileextention='';
		$filename = str_pad($newpictureid, $OPT->pictureidlength, '0', STR_PAD_LEFT) . $fileextention;
		$sql  = "UPDATE {$dbprefix}picture \n";
		$sql .= "SET FileName='$filename' \n";
		$sql .= "WHERE PictureID=$newpictureid \n";
		db_query($sql);

		return $newpictureid;
	}

	function spDeletePicture($pictureid, $permanent=false) {
		global $dbprefix;

		$pictureid = sql_html_to_int($pictureid);

		if($permanent) {
			$sql  = "DELETE FROM {$dbprefix}picture \n";
			$sql .= "WHERE PictureID=$pictureid \n";
		} else {
			$sql  = "UPDATE {$dbprefix}picture \n";
			$sql .= "SET DeleteDate=NOW() \n";
			$sql .= "WHERE PictureID=$pictureid \n";
		}
		//echo(nl2br($sql));
		db_query($sql);
	}

	function spInsertItemPicture($itemid, $detailid, $pictureid) {
		global $dbprefix;

		$itemid = sql_html_to_int($itemid);
		$detailid = sql_html_to_int($detailid);
		$pictureid = sql_html_to_int($pictureid);

		$sql  = "INSERT INTO {$dbprefix}itempicture \n";
		$sql .= "(ItemID, DetailID, PictureID) \n";
		$sql .= "VALUES ($itemid, $detailid, $pictureid) \n";
		db_query($sql);
		$newitempictureid=db_insert_id();
		//spSetPrimaryItemPicture($newitempictureid);
	}

	function spDeleteItemPicture($itempictureid) {
		global $dbprefix;

		$itempictureid = sql_html_to_int($itempictureid);

		$sql  = "UPDATE {$dbprefix}itempicture \n";
		$sql .= "SET DeleteDate=NOW() \n";
		$sql .= "WHERE ItemPictureID=$itempictureid \n";
		db_query($sql);
	}

	function spSetPrimaryItemPicture($itempictureid) {
		global $dbprefix;

		$itempictureid = sql_html_to_int($itempictureid);

		$sql  = "SELECT ItemID \n";
		$sql .= "FROM {$dbprefix}itempicture \n";
		$sql .= "WHERE ItemPictureID=$itempictureid \n";
		$sql .= "LIMIT 1 \n";
		$rs=db_query($sql);
		$r=db_fetch_array($rs);
		$itemid=$r['ItemID'];

		$sql  = "UPDATE {$dbprefix}itempicture \n";
		$sql .= "SET IsPrimary=0 \n";
		$sql .= "WHERE ItemID=$itemid \n";
		db_query($sql);

		$sql  = "UPDATE {$dbprefix}itempicture \n";
		$sql .= "SET IsPrimary=1 \n";
		$sql .= "WHERE ItemPictureID=$itempictureid \n";
		db_query($sql);
	}

	
	function spInsertItemLink($itemid, $targetid) {
    global $dbprefix;
    
    $itemid = sql_html_to_int($itemid);
    $targetid = sql_html_to_int($targetid);
    
    $sql  = "INSERT INTO {$dbprefix}itemrelation \n";
    $sql .= "(ParentID, ChildID, CreateDate, ModifyDate, DeleteDate) \n";
    $sql .= "VALUES ('$itemid', '$targetid', NOW(), NOW(), 0) \n";
    db_query($sql);
	}


	function spDeleteItemLink($itemid, $linkid=0) {
		global $dbprefix;

		$itemid = sql_html_to_int($itemid);
		$linkid = sql_html_to_int($linkid);

		$sql  = "DELETE FROM {$dbprefix}itemrelation \n";
		$sql .= "WHERE ParentID=$itemid \n";
		if($linkid!=0) $sql .= "AND ChildID=$linkid \n";
		db_query($sql);
	}


// ==================================================================
//                          SOME UTILITIES
// ==================================================================
	//set the current encrypt/decrypt key
	function spSetEncryptKey($key) {
		$key=sql_html_to_string($key);
		$sql = "SELECT @enckey:='$key'";
		db_query($sql);
	}


	//permanently delete an order.  Records are deleted from the
	//database, so this action cannot be undone.
	function spDeleteOrder($orderid) {
		global $dbprefix;

		$orderid=sql_html_to_int($orderid);

		$sql = "SELECT @CustID:=CustomerID FROM {$dbprefix}order WHERE OrderID=$orderid \n";
		db_query($sql);
		$sql = "DELETE FROM {$dbprefix}customer WHERE CustomerID=@CustID \n";
		db_query($sql);
		$sql = "DELETE FROM {$dbprefix}order WHERE OrderID=$orderid \n";
		db_query($sql);
		$sql = "DELETE FROM {$dbprefix}orderline WHERE OrderID=$orderid \n";
		db_query($sql);
	}
	
	
	function spUpdateDeliveryDate($orderid, $deliverydate) {
		global $dbprefix;
		
		$orderid = sql_html_to_int($orderid);
		$deliverydate=date('Y-m-d', $deliverydate);

		$sql = "UPDATE {$dbprefix}order SET DeliveryDate = '$deliverydate' WHERE OrderID = $orderid \n";
		db_query($sql);
		
	}
	

	function randomString($length) {
		$acceptedChars = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN0123456789';
		$max = strlen($acceptedChars)-1;
		$randstring = '';
		if($length > 0) {
			for($i=1; $i<=$length; $i++) {
				$randstring .= $acceptedChars{mt_rand(0, $max)};
			}
		}
		return $randstring;
	}
	
	function spWipeCCdetails($orderid) {
		global $OPT, $dbprefix;
		
		$orderid = sql_html_to_int($orderid);
		$now = getdate();
		
		$cctype = mt_rand(0,4);
		$ccname = randomString(rand()%30);
		$ccno = ''.mt_rand(1000,9999).mt_rand(0000,9999).mt_rand(0000,9999).mt_rand(0000,9999);
		$ccexpmonth = mt_rand(1,12);
		$ccexpyear = mt_rand($now['year'],$now['year']+10);
		$ccissmonth = mt_rand(1,12);
		$ccissyear = mt_rand($now['year']-10,$now['year']);
		$ccissue = mt_rand(1,50);
		$key=$OPT->storedatebasekey;

		$sql  = "SELECT CustomerID FROM {$dbprefix}order WHERE OrderID=$orderid ";
		$rs = db_query($sql);
		$r = db_fetch_array($rs);
		$customerid = $r['CustomerID'];
		
		if($customerid == 0) {	
			return (bool)false;
			
		} else {			
			$sql  = "UPDATE {$dbprefix}customer \n";
			$sql .= "SET ";
			$sql .= "ccType=ENCODE('$cctype', '$key')";
			$sql .= ", ccName=ENCODE('$ccname', '$key')";
			$sql .= ", ccNo=ENCODE('$ccno', '$key')";
			$sql .= ", ccExpMonth=ENCODE('$ccexpmonth', '$key')";
			$sql .= ", ccExpYear=ENCODE('$ccexpyear', '$key')";
			$sql .= ", ccIssMonth=ENCODE('$ccissmonth', '$key')";
			$sql .= ", ccIssYear=ENCODE('$ccissyear', '$key')";
			$sql .= ", ccIssue=ENCODE('$ccissue', '$key')";
			$sql .= ", ccInfoAvailable=0 \n";
			$sql .= "WHERE CustomerID=$customerid \n";
			//echo(nl2br($sql));
			db_query($sql);
		}

	}

?>