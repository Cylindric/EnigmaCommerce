<?php


// String to String
// ----------------
// takes the given string and escapes the special characters
// if quotes have automatically been escaped, un-escape them first
	function sql_html_to_string($stringval) {
		$returnvalue=$stringval;
		if(ini_get('magic_quotes_gpc')=='1') {
			$returnvalue = stripslashes($returnvalue);
		}
		$returnvalue = db_escape_string($returnvalue);
		return $returnvalue;
	}


// String to DateTime
// ------------------
// takes a date in one of the following formats
//   d/m/y
//   d/m/yyyy
//   dd/mm/yy
//   dd/mm/yyyy
// any of the above followed by
//   h:m
//   h:m:s
//
// returns a string formatted yyyy-mm-dd hh:mm:ss
//
	function sql_html_to_date($stringval) {
		$day=0;
		$month=0;
		$year=0;
		$hour=0;
		$minute=0;
		$second=0;

		$d=$stringval;
		$d=str_replace('-', '/', $d);

		$slash1=strpos($d,'/',0);
		$slash2=@strpos($d,'/',$slash1+1);
		$space1=@strpos($d,' ',$slash2+1);
		$colon1=@strpos($d,':',$space1);
		$colon2=@strpos($d,':',$colon1+1);

		$day = substr($d, 0, $slash1);
		$month=substr($d, $slash1+1, $slash2-$slash1-1);

		if($space1===false) $year=substr($d, $slash2+1, strlen($d)-$slash2);
		else $year=substr($d, $slash2+1, $space1-$slash2-1);

		if($colon1!==false) {
			$hour=substr($d, $space1+1,$colon1-$space1-1);

			if($colon2===false) $minute=substr($d, $colon1+1, strlen($d)-$colon1);
			else $minute=substr($d, $colon1+1, $colon2-$colon1-1);

			if($colon2!==false) $second=substr($d, $colon2+1, strlen($d)-$colon2);
		}

		$datetime=mktime($hour, $minute, $second, $month, $day, $year);

		if($datetime==-1) {
			$datetime = time();
		}
		return date('Y-m-d H:i:s',$datetime);
	}


// String to number
// ----------------
	function sql_html_to_int($stringval, $default=0) {
		$stringval=trim($stringval);
		$stringval=str_replace(',','',$stringval); //strip thousands sepparators
		$stringval=str_replace('£','',$stringval); //strip pound symbols
		$stringval=str_replace('\$','',$stringval);//strip dollar symbols
		$stringval=str_replace('€','',$stringval); //strip euro symbols
		$returnvalue=$default;
		if(is_numeric($stringval)) {
			$returnvalue=intval($stringval);
		}
		return $returnvalue;
	}
	function sql_html_to_float($stringval, $default=0) {
		$stringval=trim($stringval);
		$stringval=str_replace(',','',$stringval); //strip thousands sepparators
		$stringval=str_replace('£','',$stringval); //strip pound symbols
		$stringval=str_replace('\$','',$stringval);//strip dollar symbols
		$stringval=str_replace('€','',$stringval); //strip euro symbols
		$returnvalue=$default;
		if(is_numeric($stringval)) {
			$returnvalue=floatval($stringval);
		}
		return $returnvalue;
	}



// String to Bit
// -------------
//takes the given string and returns 1 if the string equals "1"
//otherwise returns 0
	function sql_html_to_bit($stringval) {
		if ($stringval!='1') {
			return 0;
		} else {
			return 1;
		}
	}



	function spInsertCustomer(
		$FirstName, $LastName, $Email, $DayPhone,
		$invAddress1, $invAddress2, $invAddress3, $invTown, $invCounty, $invPostcode, $invCountry,
		$delAddress1, $delAddress2, $delAddress3, $delTown, $delCounty, $delPostcode, $delCountry,
		$ccType, $ccName, $ccNo, $ccExpMonth, $ccExpYear, $ccIssMonth, $ccIssYear, $ccIssue, $ccCode) {

		global $OPT, $dbprefix;
		$key=$OPT->storedatebasekey;

		//fix vars
		$subfirstname=sql_html_to_string($FirstName);
		$sublastname=sql_html_to_string($LastName);
		$subemail=sql_html_to_string($Email);
		$subdayphone=sql_html_to_string($DayPhone);
		$subinvaddress1=sql_html_to_string($invAddress1);
		$subinvaddress2=sql_html_to_string($invAddress2);
		$subinvaddress3=sql_html_to_string($invAddress3);
		$subinvtown=sql_html_to_string($invTown);
		$subinvcounty=sql_html_to_string($invCounty);
		$subinvpostcode=sql_html_to_string($invPostcode);
		$subinvcountry=sql_html_to_string($invCountry);
		$subdeladdress1=sql_html_to_string($delAddress1);
		$subdeladdress2=sql_html_to_string($delAddress2);
		$subdeladdress3=sql_html_to_string($delAddress3);
		$subdeltown=sql_html_to_string($delTown);
		$subdelcounty=sql_html_to_string($delCounty);
		$subdelpostcode=sql_html_to_string($delPostcode);
		$subdelcountry=sql_html_to_string($delCountry);
		$subcctype=sql_html_to_string($ccType);
		$subccname=sql_html_to_string($ccName);
		$subccno=sql_html_to_string($ccNo);
		$subccexpmonth=sql_html_to_int($ccExpMonth);
		$subccexpyear=sql_html_to_int($ccExpYear);
		$subccissmonth=sql_html_to_int($ccIssMonth);
		$subccissyear=sql_html_to_int($ccIssYear);
		$subccissue=sql_html_to_int($ccIssue);
		$subcccode=sql_html_to_int($ccCode);

		$sql  = "INSERT INTO {$dbprefix}customer ";
		$sql .= " (CreateDate, FirstName, LastName, Email, DayPhone";
		$sql .= ", invaddress1, invaddress2, invaddress3, invtown, invcounty, invpostcode, invcountry";
		$sql .= ", deladdress1, deladdress2, deladdress3, deltown, delcounty, delpostcode, delcountry";
		$sql .= ", cctype, ccname, ccno, ccexpmonth, ccExpYear, ccissmonth, ccissyear, ccissue, cccode";
		$sql .= ") VALUES (";
		$sql .= "  NOW()";
		$sql .= ", '$subfirstname', '$sublastname', '$subemail', '$subdayphone'";
		$sql .= ", '$subinvaddress1', '$subinvaddress2', '$subinvaddress3', '$subinvtown', '$subinvcounty', '$subinvpostcode', '$subinvcountry'";
		$sql .= ", '$subdeladdress1', '$subdeladdress2', '$subdeladdress3', '$subdeltown', '$subdelcounty', '$subdelpostcode', '$subdelcountry'";
		$sql .= ", ENCODE('$subcctype', '$key')";
		$sql .= ", ENCODE('$subccname', '$key')";
		$sql .= ", ENCODE('$subccno', '$key')";
		$sql .= ", ENCODE('$subccexpmonth', '$key')";
		$sql .= ", ENCODE('$subccexpyear', '$key')";
		$sql .= ", ENCODE('$subccissmonth', '$key')";
		$sql .= ", ENCODE('$subccissyear', '$key')";
		$sql .= ", ENCODE('$subccissue', '$key')";
		$sql .= ", ENCODE('$subcccode', '$key')";
		$sql .= ")";
		$rs=db_query($sql);
		$dbcustomerid=db_insert_id($rs);
		return $dbcustomerid;
	}


	function spInsertOrder($customerID, $deliveryDate, $postageZone, $postageCharged, $orderTotal) {
		global $OPT, $dbprefix;

		//validate
		$customerID=sql_html_to_int($customerID);
		$deliveryDate=date('Y-m-d', sql_html_to_int($deliveryDate));
		$postageZone=sql_html_to_int($postageZone);
		$postageCharged=sql_html_to_float($postageCharged);
		$orderTotal=sql_html_to_float($orderTotal);

		//insert
		$sql  = "INSERT INTO {$dbprefix}order ";
		$sql .= "(CustomerID, OrderDate, DeliveryDate, PostageZoneID, PostageCharged, IsNew, OrderTotal) ";
		$sql .= "VALUES ";
		$sql .= "($customerID, NOW(), '$deliveryDate', $postageZone, $postageCharged, -1, $orderTotal) ";
		$rs=db_query($sql);
		$dborderid=db_insert_id($rs);

		return $dborderid;
	}



	function spInsertOrderConfirmation($orderid, $confirmationtext) {
		global $OPT, $dbprefix;

		//validate
		$orderid=sql_html_to_int($orderid);
		$confirmationtext=sql_html_to_string($confirmationtext);

		//update
		$sql  = "UPDATE {$dbprefix}order ";
		$sql .= "SET OrderConfirmationText='$confirmationtext' ";
		$sql .= "WHERE OrderID=$orderid ";
		$rs=db_query($sql);
		$updates=db_affected_rows($rs);
		return $updates;

	}


	function spMarkOrderRead($orderid) {
		global $OPT, $dbprefix;

		//validate
		$orderid=sql_html_to_int($orderid);

		//update
		$sql  = "UPDATE {$dbprefix}order ";
		$sql .= "SET IsNew=0 ";
		$sql .= "WHERE OrderID=$orderid ";
		$rs=db_query($sql);

		return db_affected_rows($rs);
	}

	function spMarkOrderCustomerNotified($orderid, $to, $subject, $headers, $body) {
		global $OPT, $dbprefix;

		//validate
		$orderid = sql_html_to_int($orderid);
		$to = sql_html_to_string($to);
		$subject = sql_html_to_string($subject);
		$header = sql_html_to_string($headers);
		$body = sql_html_to_string($body);

		//log the message sent
		$sql  = "INSERT INTO {$dbprefix}ordermessage ";
		$sql .= "(OrderID, SentTo, Subject, Headers, Body) ";
		$sql .= "VALUES ";
		$sql .= "($orderid, '$to', '$subject', '$headers', '$body') ";
		$rs = db_query($sql);
		$dbordermessageid = db_insert_id($rs);
		
		//mark the order-update flag
		$sql  = "UPDATE {$dbprefix}order ";
		$sql .= "SET CustomerNotified = NOW() ";
		$sql .= "WHERE OrderID=$orderid ";
		$rs = db_query($sql);
		
		return $dbordermessageid;
	}

	function spInsertOrderLine($orderID, $detailID, $quantity, $pricePaid, $discount) {
		global $OPT, $dbprefix;

		//validate
		$orderID=sql_html_to_int($orderID);
		$detailID=sql_html_to_int($detailID);
		$quantity=sql_html_to_int($quantity);
		$pricePaid=sql_html_to_float($pricePaid);
		$discount=sql_html_to_int($discount);

		//insert
		$sql  = "INSERT INTO {$dbprefix}orderline ";
		$sql .= "(OrderID, OrderLineTypeID, OrderLineText, DetailID, Quantity, PricePaid, Discount) ";
		$sql .= "VALUES ";
		$sql .= "($orderID, 1, 'order line', $detailID, $quantity, $pricePaid, $discount)";
		$rs=db_query($sql);
		$dborderlineid=db_insert_id($rs);
		return $dborderlineid;
	}


	function spInsertOrderPostageLine($orderID, $text, $quantity, $pricePaid) {
		global $OPT, $dbprefix;

		//validate
		$orderID=sql_html_to_int($orderID);
		$quantity=sql_html_to_int($quantity);
		$text=sql_html_to_string($text);
		$pricePaid=sql_html_to_float($pricePaid);

		//insert
		$sql  = "INSERT INTO {$dbprefix}orderline ";
		$sql .= "(OrderID, OrderLineTypeID, OrderLineText, DetailID, Quantity, PricePaid, Discount) ";
		$sql .= "VALUES ";
		$sql .= "($orderID, 2, '$text', 0, $quantity, $pricePaid, 0)";
		$rs=db_query($sql);
		$dborderpostagelineid=db_insert_id($rs);
		return $dborderpostagelineid;
	}


	function spLogToHistory($entitytype, $entityid, $entityname='', $description='') {
		global $OPT, $dbprefix;

		//don't log activity by logged in users, this will change
		if(authorised()) return 0;

		//validate
		$entitytype=sql_html_to_string($entitytype);
		$entityid=sql_html_to_int($entityid);
		$entityname=sql_html_to_string($entityname);
		$description=sql_html_to_string($description);
		$useragent=sql_html_to_string($_SERVER['HTTP_USER_AGENT']);
		$userreferer=sql_html_to_string($_SERVER['HTTP_REFERER']);
		$remoteaddr=sql_html_to_string($_SERVER['REMOTE_ADDR']);
		$serverport=sql_html_to_string($_SERVER['SERVER_PORT']);
		$requesturi=sql_html_to_string($_SERVER['REQUEST_URI']);
		$sessionid=sql_html_to_string(session_id());

		//don't log activity by staff computers
		//if(strpos($useragent,'NOENIGMALOGGING')) return 0;

		$sql  = "INSERT INTO {$dbprefix}history \n";
		$sql .= "(EntityType, EntityID, EntityName, Description, ";
		$sql .=  "UserAgent, RemoteAddr, ServerPort, RequestURI, UserReferer, SessionID) \n";
		$sql .= "VALUES ";
		$sql .= "('$entitytype', $entityid, '$entityname', '$description', ";
		$sql .=  "'$useragent', '$remoteaddr', '$serverport', '$requesturi', '$userreferer', '$sessionid')";
		//echo(nl2br($sql));

		if( ($entitytype=='item') && (!$OPT->log_items) ) return 0;
		if( ($entitytype=='category') && (!$OPT->log_categories) ) return 0;
		if( ($entitytype=='article') && (!$OPT->log_articles) ) return 0;
		if( ($entitytype=='text') && (!$OPT->log_text) ) return 0;
		db_query($sql);

	}

?>