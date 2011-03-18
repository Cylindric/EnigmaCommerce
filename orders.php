<?php
	/* ----------------------------------------------------------------
		    Filename:

		Requirements:

		       Notes:
	------------------------------------------------------------------- */

	require_https();

	$override_delivery_date = '';
	if(!empty($_POST['DeliveryDate'])) {
		if(strlen($_POST['DeliveryDate']) == 10)
		{
			$day = intval(substr($_POST['DeliveryDate'], 0, 2));
			$month = intval(substr($_POST['DeliveryDate'], 3, 2));
			$year = intval(substr($_POST['DeliveryDate'], 6, 4));
		} else
		{
			echo("Date must be in the format dd-mm-yyyy or dd/mm/yyyy<br />");
		}
		if( ($day != 0) && ($month != 0) && ($year != 0) )
		{
			$override_delivery_date = mktime(0, 0, 0, $month, $day, $year);
		}
	}


	if(empty($PAGEOPT['orderid'])) {

		//list all orders
		$rs=view_orders();
		while($r=db_fetch_array($rs)) {
			$dbOrderID = $r['OrderID'];
			$dbOrderTotal = $r['OrderTotal'];
			$dbOrderDate = $r['OrderDate'];
			$dbCustomerName = $r['FirstName'].' '.$r['LastName'];
			$dbOrderNew = $r['IsNew'];

			$html->assign('ORDER_ID', $dbOrderID);
			$html->assign('ORDER_TOTAL', format_currency($dbOrderTotal));
			$html->assign('ORDER_DATE', format_datetime($dbOrderDate));
			$html->assign('CUSTOMER_NAME', $dbCustomerName);
			if($dbOrderNew) {
				$html->assign('CLASS', ' class="current"');
				$html->parse('main.ordersummary.row.new');
			} else {
				$html->assign('CLASS', '');
				$html->parse('main.ordersummary.row.notnew');
			}

			$html->parse('main.ordersummary.row');
		}
		$html->parse('main.ordersummary');

	} else {

		if($PAGEOPT['action'] == 'wipecc') {
			if($PAGEOPT['confirmed']!='confirm') {
				$html->parse('main.orderdetails.wipeccwarning');
				$html->assign('CONFIRMWIPE', '/confirm');
			} else {
				spWipeCCdetails($PAGEOPT['orderid']);
			}
		}

		//view the current order, and mail the customer if not already sent
		$rs=view_orders($PAGEOPT['orderid']);
		while($r=db_fetch_array($rs)) {

			//start formatting the email output
			start_page($mail_body_html, 'orders');
			$mail_body_text = '';

			//order details
			$dbOrderID = $r['OrderID'];
			$dbOrderTotal = $r['OrderTotal'];
			$dbOrderDate = $r['OrderDate'];
			$dbDeliveryDate = $r['DeliveryDate'];
			$dbCustomerNotified = $r['CustomerNotified'];
			$dbPostageZoneID = $r['PostageZoneID'];
//echo(fancy_vardump($r));
			$standardDeliveryDate = $_SESSION['BASKET']->getNextDeliveryDate(strtotime($dbOrderDate));
			if(!empty($override_delivery_date))
			{
				if($dbDeliveryDate != date('Y-m-d H:i:s', $override_delivery_date))
				{
					//update database with new date
					spUpdateDeliveryDate($dbOrderID, $override_delivery_date);
					//reload page?
				}
				$dbDeliveryDate = date('Y-m-d H:i:s', $override_delivery_date);
				$standardDeliveryDate = $override_delivery_date;
			}

			$html->assign('ORDER_ID', $dbOrderID);
			$html->assign('ORDER_TOTAL', format_currency($dbOrderTotal));
			$html->assign('ORDER_DATE', format_datetime($dbOrderDate, $OPT->fulldate));
			if($dbPostageZoneID == 0) {
				$html->assign('DELIVERY_DATE', 'Collect-In-Store');
			} else {
				$html->assign('DELIVERY_DATE', format_datetime($dbDeliveryDate, $OPT->fulldate));
			}
			$html->assign('CUSTOMER_NOTIFIED', format_datetime($dbCustomerNotified, $OPT->fulldate));
			$html->assign('STANDARD_DELIVERY_DATE', date($OPT->fulldate, $standardDeliveryDate));

			//indicate if non-standard delivery date was requested
			if(format_datetime($dbDeliveryDate, $OPT->longdate) != date($OPT->longdate, $standardDeliveryDate)) {
				$html->parse('main.orderdetails.specialdate');
			}

			$mail_body_html->assign('ORDER_ID', $dbOrderID);
			$mail_body_html->assign('ORDER_TOTAL', format_currency($dbOrderTotal));
			$mail_body_html->assign('ORDER_DATE', format_datetime($dbOrderDate, $OPT->fulldate));
			if($dbPostageZoneID == 0) {
				$mail_body_html->assign('DELIVERY_DATE', 'Collect-In-Store');
			} else {
				$mail_body_html->assign('DELIVERY_DATE', format_datetime($dbDeliveryDate, $OPT->fulldate));
			}

			$mail_body_text .= "OrderID: $dbOrderID\n";
			$mail_body_text .= "Order Total: ".format_currency($dbOrderTotal, '', '', ' ')."\n";
			$mail_body_text .= "Order Date: ".format_datetime($dbOrderDate, $OPT->fulldate)."\n";
			if($dbPostageZoneID == 0) {
				$mail_body_text .= "Delivery Date: Collect-In-Store\n";
			} else {
				$mail_body_text .= "Delivery Date: ".format_datetime($dbDeliveryDate, $OPT->fulldate)."\n";
			}


			//credit card details
			$cc_details  = '';
			if($r['ccInfoAvailable'] != 0) {
				$cc_details .= $r["PaymentTypeName"] . "<br />";
				if(strlen($r["ccNo"])==16) {
					$cc_details .= substr($r["ccNo"],0,4) . '&nbsp;&nbsp;' .substr($r["ccNo"],4,4) . '&nbsp;&nbsp;' . substr($r["ccNo"],8,4) . '&nbsp;&nbsp;' . substr($r["ccNo"],12,4) . '<br />';
				} else {
					$cc_details .= $r["ccNo"] . "<br />";
				}
				$cc_details .= $r["ccName"] . "<br />";
				$cc_details .= sprintf('issued: %02d-%02d<br />', $r["ccIssMonth"], substr($r["ccIssYear"],-2));
				$cc_details .= sprintf('issue #: %02d<br />', $r['ccIssue']);
				$cc_details .= sprintf('expires: %02d-%02d<br />', $r["ccExpMonth"], substr($r["ccExpYear"],-2));
				$cc_details .= sprintf('CVV: %d<br />', $r["ccCode"]);
				$html->assign("CC_DETAILS", $cc_details);
				$html->parse('main.orderdetails.stdcc');
				$mail_body_html->assign('CC_DETAILS', 'xxxx xxxx xxxx '.substr($r["ccNo"], -4));
				$mail_body_html->parse('main.orderdetails.mailcc');
				$mail_body_text .= "Credit Card: xxxx xxxx xxxx ".substr($r["ccNo"], -4)."\n\n";
			}

			//customer details
			$dbCustomerName = $r['FirstName'].' '.$r['LastName'];
			$dbCustomerPhone = $r['DayPhone'];
			$dbCustomerEmail = $r['Email'];
			$html->assign('CUSTOMER_NAME', $dbCustomerName);
			$html->assign('CUSTOMER_EMAIL', $dbCustomerEmail);
			$html->assign('CUSTOMER_PHONE', $dbCustomerPhone);

			$mail_body_html->assign('CUSTOMER_NAME', $dbCustomerName);
			$mail_body_html->assign('CUSTOMER_EMAIL', $dbCustomerEmail);
			$mail_body_html->assign('CUSTOMER_PHONE', $dbCustomerPhone);

			$mail_body_text .= "Name: $dbCustomerName\n";
			$mail_body_text .= "Email: $dbCustomerEmail\n";
			$mail_body_text .= "Phone: $dbCustomerPhone\n\n";



			//invoice address
			$inv_address  = "";
			if (strlen($r["InvAddress1"])!=0) $dbinvaddress .= $r["InvAddress1"] . "<br />";
			if (strlen($r["InvAddress2"])!=0) $dbinvaddress .= $r["InvAddress2"] . "<br />";
			if (strlen($r["InvAddress3"])!=0) $dbinvaddress .= $r["InvAddress3"] . "<br />";
			if (strlen($r["InvTown"])!=0)     $dbinvaddress .= $r["InvTown"] . "<br />";
			if (strlen($r["InvCounty"])!=0)   $dbinvaddress .= $r["InvCounty"] . "<br />";
			if (strlen($r["InvPostcode"])!=0) $dbinvaddress .= $r["InvPostcode"] . "<br />";
			if (strlen($r["InvCountry"])!=0)  $dbinvaddress .= $r["InvCountry"];
			$html->assign("INVOICE_ADDRESS", $dbinvaddress);
			$mail_body_html->assign("INVOICE_ADDRESS", $dbinvaddress);
			$mail_body_text .= "Invoice Address: ".html_to_text($dbinvaddress)."\n\n";

			//delivery address
			$del_address  = "";
			if (strlen($r["DelAddress1"])!=0) $dbdeladdress .= $r["DelAddress1"] . "<br />";
			if (strlen($r["DelAddress2"])!=0) $dbdeladdress .= $r["DelAddress2"] . "<br />";
			if (strlen($r["DelAddress3"])!=0) $dbdeladdress .= $r["DelAddress3"] . "<br />";
			if (strlen($r["DelTown"])!=0)     $dbdeladdress .= $r["DelTown"] . "<br />";
			if (strlen($r["DelCounty"])!=0)   $dbdeladdress .= $r["DelCounty"] . "<br />";
			if (strlen($r["DelPostcode"])!=0) $dbdeladdress .= $r["DelPostcode"] . "<br />";
			if (strlen($r["DelCountry"])!=0)  $dbdeladdress .= $r["DelCountry"];
			$html->assign("DELIVERY_ADDRESS", $dbdeladdress);
			$mail_body_html->assign("DELIVERY_ADDRESS", $dbdeladdress);
			$mail_body_text .= "Delivery Address: ".html_to_text($dbdeladdress)."\n\n";

			//order lines
			$_SESSION['BASKET']->create();
			$orderlinevalue=0.00;
			$orderlinepostage=0.00;
			$rs2=view_orderlines($PAGEOPT['orderid']);
			while($r2=db_fetch_array($rs2)) {
				$_SESSION['BASKET']->add($r2['DetailID'], $r2["Quantity"]);
				$orderlinevalue += ($r2["Quantity"]*$r2["PricePaid"]);
				$html->assign("ITEM_ID",        $r2["ItemID"]);
				$html->assign("ITEM_STOCKCODE", format_stockcode($r2["StockCodePrefix"], $r2['StockCode']));
				$html->assign("ITEM_NAME",      $r2["ItemName"]);
				$html->assign("DETAIL_NAME",    $r2["DetailName"]);
				$html->assign("DETAIL_PRICE",   format_currency($r2["PricePaid"]));
				$html->assign("DETAIL_QTY",     $r2["Quantity"]);
				$html->assign("DETAIL_TOTAL",   format_currency($r2["Quantity"]*$r2["PricePaid"]));
				$html->parse("main.orderdetails.detailrow");

				$mail_body_html->assign("ITEM_ID",        $r2["ItemID"]);
				$mail_body_html->assign("ITEM_STOCKCODE", format_stockcode($r2["StockCodePrefix"], $r2['StockCode']));
				$mail_body_html->assign("ITEM_NAME",      $r2["ItemName"]);
				$mail_body_html->assign("DETAIL_NAME",    $r2["DetailName"]);
				$mail_body_html->assign("DETAIL_PRICE",   format_currency($r2["PricePaid"]));
				$mail_body_html->assign("DETAIL_QTY",     $r2["Quantity"]);
				$mail_body_html->assign("DETAIL_TOTAL",   format_currency($r2["Quantity"]*$r2["PricePaid"]));
				$mail_body_html->parse("main.orderdetails.detailrow");


			}
			$html->assign("ORDER_SUBTOTAL", format_currency($orderlinevalue+$orderlinepostage));
			$mail_body_html->assign("ORDER_SUBTOTAL", format_currency($orderlinevalue+$orderlinepostage));

			//postage lines
			$orderlinepostage = 0;
			$rs2 = view_orderpostagelines($PAGEOPT['orderid']);
			while($r2=db_fetch_array($rs2)) {
				$orderlinepostage += ($r2['Quantity'] * $r2["PricePaid"]);
				$html->assign("TEXT",  $r2["OrderLineText"]);
				$html->assign("PRICE",   $r2["PricePaid"]);
				$html->assign("QTY",   $r2["Quantity"]);
				$html->assign("TOTAL", format_currency($r2["Quantity"] * $r2["PricePaid"]));
				$html->parse('main.orderdetails.postagerow');

				$mail_body_html->assign("TEXT",  $r2["OrderLineText"]);
				$mail_body_html->assign("PRICE",   $r2["PricePaid"]);
				$mail_body_html->assign("QTY",   $r2["Quantity"]);
				$mail_body_html->assign("TOTAL", format_currency($r2["Quantity"] * $r2["PricePaid"]));
				$mail_body_html->parse('main.orderdetails.postagerow');
			}

			$html->assign('ORDER_TOTAL', format_currency($orderlinevalue + $orderlinepostage));
			$mail_body_html->assign('ORDER_TOTAL', format_currency($orderlinevalue + $orderlinepostage));
		}
		$html->parse('main.orderdetails');
		$mail_body_html->parse('main.orderdetails');

		if(
			($dbCustomerNotified == "0000-00-00 00:00:00")
			|| ($PAGEOPT['action'] == 'send_conf')
			|| ($PAGEOPT['action'] == 'send_desp')
			|| ($PAGEOPT['action'] == 'send_mail')
			)
		{

			switch ($PAGEOPT['action']) {
				case '':
				case 'send_conf':
					$additional_text  = "\r\nThank you for your order.  This email is to let you know that we have received your order, and are processing it.\r\n";
					$additional_text .= "Please check your email regularly, as if we have any queries regarding the order, or any items are out of stock, we will need to get in touch with you.\r\n\r\n";
					$additional_text .= "Thank you,\r\n\r\n";
					$additional_text .= "Koi Logic\r\n\r\n";
					$mail_subject = 'Your Koi Logic Order Confirmation ('.$PAGEOPT['orderid'].')';
				break;

				case 'send_desp':
					$additional_text  = "\r\nThis email is to let you know that your order has been processed and despatched.\r\n";
					$additional_text .= "Thank you,\r\n\r\n";
					$additional_text .= "Koi Logic\r\n\r\n";
					$mail_subject = 'Your Koi Logic Despatch Notification ('.$PAGEOPT['orderid'].')';
				break;

				case 'send_mail':
					//don't send anything, just log the action
					spMarkOrderCustomerNotified($PAGEOPT['orderid'], $dbCustomerEmail, 'External email', '', '');
					redirect_page($OPT->enigmaroot . $OPT->controlpage . '/order/' . $PAGEOPT['orderid']);
				break;
			}

			//send email to customer
			$mail_body_html->parse('main');
			$stylesheet = file_get_contents($_SERVER['DOCUMENT_ROOT'].$OPT->enigmaroot.'style-mail.css');

      $htmltext  = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"'."\r\n";
      $htmltext .= '"http://www.w3.org/TR/html4/loose.dtd">'."\r\n";
      $htmltext .= '<html>'."\r\n";

			$htmltext .= '<head>'."\r\n";
			$htmltext .= '<title>'.$mail_subject.'</title>'."\r\n";
			$htmltext .= '<style type="text/css">'."\r\n";
			$htmltext .= '<!--'."\r\n";
			$htmltext .= $stylesheet."\r\n";
			$htmltext .= '-->'."\r\n";
			$htmltext .= '</style>'."\r\n";
			$htmltext .= '</head>'."\r\n";
			$htmltext .= '<body>';
			$htmltext .= nl2br($additional_text);
      $htmltext .= $mail_body_html->text('main');
			$htmltext .= '</body></html>';

      $mail_body_text = $additional_text . "\r\n" . $mail_body_text;

			$boundary = 'nextPart'.rand(10000000000,9999999999);
			$mail_to = $dbCustomerEmail;
			$mail_from = 'Koi Logic <sales@koilogic.co.uk>';

			$send_html = false;
			if($send_html) {
				$html_headers = '';
				$mail_headers .= 'From: ' . $mail_from . "\r\n";
				$mail_headers .= 'Reply-To: ' . $mail_from . "\r\n";
				$mail_headers .= 'Bcc: ' . $mail_from . "\r\n";
	      $mail_headers .= "MIME-Version: 1.0\r\n";
				$mail_headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
				$mail_headers .= "Content-Transfer-Encoding: 7bit\r\n";
	      $mail_headers .= "This is a multi-part message in MIME format.\r\n";

				$body .= "\r\n--$boundary\r\n";
				$body .= "Content-Type: text/plain; charset=iso-8859-1\r\n";
				$body .= "Content-Transfer-Encoding: 7bit\r\n";
				$body .= "\r\n";
				$body .= $mail_body_text;

				$body .= "\r\n--$boundary\r\n";
				$body .= "Content-Type: text/html; charset=iso-8859-1\r\n";
				$body .= "Content-Transfer-Encoding: 7bit\r\n";
				$body .= "\r\n";
				$body .= $htmltext;

				$body .= "\r\n--$boundary\r\n";

			} else {
				$html_headers = '';
				$mail_headers .= 'From: ' . $mail_from . "\r\n";
				$mail_headers .= 'Reply-To: ' . $mail_from . "\r\n";
				$mail_headers .= 'Bcc: ' . $mail_from . "\r\n";
				$body .= $mail_body_text;
			}


      $mail_result = @mail($dbCustomerEmail, $mail_subject, $body, $mail_headers);

  		spMarkOrderCustomerNotified($PAGEOPT['orderid'], $mail_to, $mail_subject, $mail_headers, $htmltext);

			//force page reload to prevent multiple mailings from refreshes
			redirect_page($OPT->enigmaroot . $OPT->controlpage . '/order/' . $PAGEOPT['orderid']);
		}
		spMarkOrderRead($PAGEOPT['orderid']);

		$_SESSION['BASKET']->create();


		// start order messages list
		$rs = view_ordermessages($PAGEOPT['orderid']);
		while($r = db_fetch_array($rs)) {
			$dbMessageID = $r['MessageID'];
			$dbOrderID = $r['OrderID'];
			$dbSentTo = $r['SentTo'];
			$dbSubject = $r['Subject'];
			$dbCreateDate = $r['CreateDate'];

			$html->assign('DATE', format_datetime($dbCreateDate));
			$html->assign('SUBJECT', $dbSubject);
			$html->parse('main.mail_log.row');
		}
		//some buttons
		$html->assign('SEND_DELIVERY_DATE', format_datetime($dbDeliveryDate, $OPT->longdate));
		$html->assign('IMG_SEND_DESP', draw_inputimage('btn_send_desp', 'icon_suppliers', 64, 64, 'Send Despatch Confirmation'));
// 		$html->assign('IMG_SEND_DESP', draw_rolloverimage('btn_send_desp', 'icon_suppliers', $OPT->enigmaroot . $OPT->controlpage . '/order/' . $PAGEOPT['orderid'] . '/send_desp', 64, 64, $alt='Send Despatch Confirmation'));
		$html->assign('IMG_SEND_MAIL', draw_rolloverimage('btn_send_mail', 'icon_newitem', 'mailto:' . $dbCustomerEmail, 64, 64, $alt='Send Email', -1, "window.location.href='".$OPT->enigmaroot . $OPT->controlpage . '/order/' . $PAGEOPT['orderid'] . "/send_mail'"));
		$html->assign('IMG_SEND_CONF', draw_rolloverimage('btn_send_conf', 'icon_orders', $OPT->enigmaroot . $OPT->controlpage . '/order/' . $PAGEOPT['orderid'] . '/send_conf', 64, 64, $alt='Resend Order Confirmation'));

		$html->parse('main.mail_log');
	}

?>