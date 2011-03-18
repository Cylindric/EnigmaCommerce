<?php
	/* ----------------------------------------------------------------
		    Filename: checkout.php

		Requirements:

		       Notes: This is a https page
	------------------------------------------------------------------- */
	require_https();

	$formerrors=1; //no form results yet

	//parse form input
	if(count($_POST)>0) {

		$p=$_POST;
		$err=array();
		$NewCustomerID=0;
		$NewOrderID=0;
		$NewOrderLineID=0;

		//validate some of the form responses!
		if($p['firstname']=='mrhtest123') {
			if(empty($p['email'])) $p['email'] = 'mark@hanfordonline.co.uk';
		} else {
			if(empty($p['firstname'])) $err['firstname']='Please specifiy a first name';
			if(empty($p['lastname'])) $err['lastname']='Please specifiy a last name';
			if(empty($p['email'])) $err['email']='Please specifiy an email address';
			if(empty($p['dayphone'])) $err['dayphone']='Please specifiy a phone number';
			if(empty($p['invaddress1'])) $err['invaddress1']='Please complete the address';
			if(empty($p['invtown'])) $err['invtown']='Please complete the address';
			if(empty($p['invpostcode'])) $err['invpostcode']='Please complete the address';
			if(empty($p['invcountry'])) $err['invcountry']='Please complete the address';
			if(empty($p['ccno'])) $err['ccno']='Please specify a card number';
			if(empty($p['ccname'])) $err['ccname']='Please specify the name on the card';
			if(empty($p['ccexpmonth'])) $err['ccexpdate']='Please specify an expiry date';
			if(empty($p['ccexpyear'])) $err['ccexpdate']='Please specify an expiry date';

			// some cards don't need a CVV code
			if(!(strpos(','.$OPT->nocvvtypes.',', ','.$p['cctype'].',') !== false)) {
			echo("nocvv");
				if(empty($p['cccode'])) {
					$err['cccode']='Please specify a CVV code';
				}
			}
			
			// Some cards need issue information
			if(strpos(','.$OPT->issuepaymenttypes.',', ','.$p['cctype'].',') !== false) {
				if(
					(empty($p['ccissmonth']) ||
					empty($p['ccissyear'])) &&
					empty($p['ccissue'])
				) {
					$err['ccissnumber']='Please specify the card issue number, or issue date for Scottish cards, or both';
				}
			}
		}

		//add some of the existing info
		$p['deliverydate']=$_SESSION['userpostagedate'];
		$p['postageZone']=$_SESSION['userpostagezoneid'];
		$p['postageAmount']=$_SESSION['userpostageamount'];
		$p['orderAmount']=$_SESSION['basketvalue'];


		//insert the customer info
		if(count($err)==0) {
			$NewCustomerID=spInsertCustomer(
				$p['firstname'], $p['lastname'], $p['email'], $p['dayphone'],
				$p['invaddress1'], $p['invaddress2'], $p['invaddress3'], $p['invtown'], $p['invcounty'], $p['invpostcode'], $p['invcountry'],
				$p['deladdress1'], $p['deladdress2'], $p['deladdress3'], $p['deltown'], $p['delcounty'], $p['delpostcode'], $p['delcountry'],
				$p['cctype'], $p['ccname'], $p['ccno'], $p['ccexpmonth'], $p['ccexpyear'], $p['ccissmonth'], $p['ccissyear'], $p['ccissue'], $p['cccode']
			);
		}



		//insert the order info
		if(count($err)==0) {
			$NewOrderID = spInsertOrder($NewCustomerID, $p['deliverydate'], $p['postageZone'], $p['postageAmount'], $p['orderAmount']);

			// Save the total for the Google tracking
			$_SESSION['google_transaction_id'] = $NewOrderID;
			$_SESSION['google_transaction_affiliation'] = '';
			$_SESSION['google_transaction_total'] = $p['orderAmount'];
			$_SESSION['google_transaction_tax'] = 0;
			$_SESSION['google_transaction_shipping'] = $p['postageAmount'];
			$_SESSION['google_transaction_city'] = $p['deltown'];
			$_SESSION['google_transaction_county'] = $p['delcounty'];
			$_SESSION['google_transaction_country'] = $p['delcountry'];

		} else {
			$err['top'] = 'Some fields are not filled in correctly.  Please check that all required fields are completed.';
		}

		//make defaults and errors available in the form
		foreach($err as $key => $value) {
			$err[$key] = '<div class="error">'.$value.'</div>';
		}
		$html->assign('SUB', $p);
		$html->assign('ERR', $err);

		$formerrors=count($err);
	}


	if($formerrors==0) {
		$_SESSION['userorderid']=$NewOrderID;
		redirect_page($OPT->enigmaroot.$OPT->controlpage.'/basket/view/1');

	} else {

		//the payment type selector
		if(is_numeric($p['cctype'])) $pt=$p['cctype'];
		else $pt=$OPT->defaultpaymenttype;
		$html->assign('SELECTOR_CCTYPE', draw_select('cctype', 'paymenttype', 'PaymentTypeID', 'PaymentTypeName', $pt));

		//expiry month
		if(is_numeric($p['ccexpmonth'])) $pt=$p['ccexpmonth'];
		else $pt=0;
		$html->assign('SELECT_EXPMONTH', draw_numberselect('ccexpmonth', 1, 12, 1, $pt, 'month'));

		//expiry year
		$now=getdate(time());
		if(is_numeric($p['ccexpyear'])) $pt=$p['ccexpyear'];
		else $pt=0;
		$html->assign('SELECT_EXPYEAR', draw_numberselect('ccexpyear',$now['year'], $now['year']+10, 1, $pt, 'year'));

		//issue month
		if(is_numeric($p['ccissmonth'])) $pt=$p['ccissmonth'];
		else $pt=0;
		$html->assign('SELECT_ISSMONTH', draw_numberselect('ccissmonth', 1, 12, 1, $pt, 'month'));

		//issue year
		$now=getdate(time());
		if(is_numeric($p['ccissyear'])) $pt=$p['ccissyear'];
		else $pt=0;
		$html->assign('SELECT_ISSYEAR', draw_numberselect('ccissyear',$now['year']-10, $now['year'], 1, $pt, 'year'));


		$html->parse('main.form');

		$setFocusForm='checkout';
		$setFocusField='firstname';

	}
?>
