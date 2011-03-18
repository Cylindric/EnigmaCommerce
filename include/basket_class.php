<?php

class Basket {

	var $qty; //quantities of each item

	/*============================================================================*
	 * the class constructor
	 *============================================================================*/
	function Basket() {
		$this->create();
	}

	/*============================================================================*
	 * create an empty basket
	 *============================================================================*/
		function create() {
			$this->qty = array();
		}

	/*============================================================================*
	 * add item to basket
	 *============================================================================*/
		function add($did, $qty) {
			if (isset($did)) {
				$this->qty["x".$did] += abs($qty);
				asort($this->qty);
			}
		}

	/*============================================================================*
	 * remove item from basket
	 * if $qty is negative, then all items are removed
	 *============================================================================*/
		function remove($did, $qty=1) {
			if ($qty > 0) {
				$this->qty["x".$did] -= $qty;
			}
			if ( ($this->qty["x".$did] <= 0) || ($qty < 0) ) {
				unset($this->qty["x".$did]);
			}
		}

	/*============================================================================*
	 * a csv list of the keys
	 *============================================================================*/
		//get a list of ID's from the basket, stripping off the
		//annoying x char from the start
		function keylist() {
			$basketitems='';
			foreach ($this->qty as $detailID => $quantity) {
				$basketitems .= substr($detailID, 1).',';
			}
			$basketitems=substr($basketitems,0,-1);

			return $basketitems;
		}

	/*============================================================================*
	 * orderline quantity
	 *============================================================================*/
	 	function lineqty($detailid) {
		 	return $this->qty['x'.$detailid];
	 	}

	/*============================================================================*
	 * number of items
	 *============================================================================*/
		function itemcount() {
			$total = 0;

			foreach ($this->qty as $key => $value) {
				$total += ($value);
			}

			return $total;

		}



	/*============================================================================*
	 * number of pallets required
	 *============================================================================*/
	 	function palletcount() {
		 	$palletcount = 0;
			$rs = view_detailpostagecharges($this->keylist());
			while($r = db_fetch_array($rs)) {
				$dbDetailID = $r['DetailID'];
				$dbPalletThreshold = $r['PalletThreshold'];
				$palletcount += $this->qty["x$dbDetailID"] / $dbPalletThreshold;
			}

			return $palletcount;
		}


	/*============================================================================*
	 * total value of special charges
	 *   $specials will be set to an array with all the present special charge id's
	 *   and names.
	 *============================================================================*/
	 	function postagespecials(&$specials, $detailid=0) {
		 	$charge = 0;
		 	$specials=array();

		 	if($detailid==0) {
			 	$keylist = $this->keylist();
		 	} else {
			 	$keylist = $detailid;
		 	}
			$rs = view_detailpostagecharges($keylist);
			while($r = db_fetch_array($rs)) {
				$dbDetailID = $r['DetailID'];
				$dbPostageCharge = $r['Charge'];
				$dbPostageChargeID = $r['PostageChargeID'];
				$dbPostageChargeName = $r['PostageChargeName'];

				$specials[$dbPostageChargeID] = $dbPostageChargeName;
				$charge += $this->qty["x$dbDetailID"] * $dbPostageCharge;
			}

			return $charge;
		}


	/*============================================================================*
	 * value of items
	 *============================================================================*/
	 	function totalvalue() {
		 	$totalvalue=0;
		 	$rs = view_details(0,$this->keylist());
		 	while($r=db_fetch_array($rs)) {
		 	//var_dump($r);
			 	$dbdetailid=$r['DetailID'];
			 	$dbretailprice=$r['RetailPrice'];
			 	$totalvalue += ($this->qty['x'.$dbdetailid] * $dbretailprice);
		 	}
		 	return $totalvalue;
	 	}

	/*============================================================================*
	 * value of items excl. postage
	 *============================================================================*/
	 	function totalvalueitems() {
		 	$totalvalueitems=0;
		 	$rs = view_details(0,$this->keylist());
		 	while($r=db_fetch_array($rs)) {
			 	$dbdetailid=$r['DetailID'];
			 	$dbwebprice=$r['WebPrice'];
			 	$totalvalueitems += ($this->qty['x'.$dbdetailid] * $dbwebprice);
		 	}
		 	return $totalvalueitems;
	 	}

	/*============================================================================*
	 * what band would the current order fall into?
	 * Returns an array with the following elements:
	 *  0. BandID
	 *  1. BandName
	 *  2. StartValue
	 *  3. EndValue
	 *============================================================================*/
		function postageband($basketvalue) {
			$PostageBandID=0;
			$PostageBandName='';
			$StartValue=-1;
			$EndValue=-1;

			//start on the lowest band value, and bail out once we exceed
			//the end value
			$rs=view_postagebands();
			while(($r=db_fetch_array($rs)) && ($PostageBandID==0)) {
				$dbBandID=$r['PostageBandID'];
				$dbBandName=$r['PostageBandName'];
				$dbStartValue=$r['StartValue'];
				$dbEndValue=$r['EndValue'];

				if( ($basketvalue<=$dbEndValue) || ($dbEndValue==0) ) {
					$PostageBandID=$dbBandID;
					$PostageBandName=$dbBandName;
					$StartValue=$dbStartValue;
					$EndValue=$dbEndValue;
				}
			}

			return array($PostageBandID, $PostageBandName, $dbStartValue, $dbEndValue);
		}


	/*============================================================================*
	 * Generate Postage Info
	 *   This function takes into account the following factors, and returns
	 *   various information about the postage.
	 *
	 * Inputs
	 *   The basket session variable
	 *
	 *
	 * Outputs
	 *   The parameter $pallet is set to true or false depending on wether or not
	 *   a pallet will be required to deliver.
	 *============================================================================*/
		function getBasketPostage(&$pallet, &$specials, &$saturdaysurcharge) {
			$debug=0;
			global $OPT;

			$idlist='';
			$setprices=0;
			$palletcount=0;
			$pallet=(boolean)false;
			$specials='';
			$satcharge=0;
			$basketvalue=0;
			$basketitemvalue=0;
			$bandinfo=array();
			$zoneid=0;
			$fixedpostage=0;
			$saturdaysurcharge=0;
			$totalpostage=0;
			$specialpostage=0;

			//build up a CSV list of detail ID's for use in the queries
			$idlist = $this->keylist();
			if($debug) echo("Key List: <strong>$idlist</strong><br>");

			//find out the total value of the basket items only
			$basketitemvalue = $this->totalvalueitems();
			if($debug) echo("Basket Item Value: <strong>$basketitemvalue</strong><br>");

			//find out the total value of the basket
			$basketvalue = $this->totalvalue();
			if($debug) echo("Total Basket Value: <strong>$basketvalue</strong><br>");

			//from the value, get the band
			$bandinfo = $this->postageband($basketitemvalue);
			if($debug) echo("Postage Band: <strong>".$bandinfo[1]."</strong><br>");

			//get the user's current zone
			$zoneid = $_SESSION['userpostagezoneid'];
			if($debug) echo("Postage Zone: <strong>Zone $zoneid</strong><br>");

			//use the band and the zone to get the fixed price
			if($zoneid == 0) {
			  $fixedpostage = 0;
			} else {
				$rs=view_postagecharge($zoneid, $bandinfo[0]);
				while($r=db_fetch_array($rs)) {
					$fixedpostage = $r['Charge'];
				}
			}
			if($debug) echo("Fixed Postage: <strong>".format_currency($fixedpostage)."</strong><br>");

			//is there a surcharge for the delivery date?
			if($zoneid == 0) {
			  $saturdaysurcharge = 0;
			} else {
				$deldate = getdate($_SESSION['userpostagedate']);
				if($deldate['wday']==6) {
					$saturdaysurcharge = $OPT->saturdaysurcharge;
				}
			}
			if($debug) echo("Saturday Surcharge (".format_currency($OPT->saturdaysurcharge).") for ".date('D d-m-y',$_SESSION['userpostagedate']).": <strong>".format_currency($saturdaysurcharge)."</strong><br>");

			//total postage due to special charges
			$specialpostage = $this->postagespecials($specials);
			if($debug) echo("Total Special Postage: <strong>".format_currency($specialpostage)."</strong><br>");

			//total postage so far?
			$totalpostage = $fixedpostage + $specialpostage + $saturdaysurcharge;
			if($debug) echo("Total Fixed Postage: <strong>".format_currency($totalpostage)."</strong><br>");


			//if the total fixed postage amount is above a certain threshold,
			//a pallet price is always suggested.
			if($totalpostage >= $OPT->palletthreshold) {
				if($debug) echo("Total ($totalpostage) is above threshold ({$OPT->palletthreshold}), adding a pallete<br>");
				$palletcount += 1;
			}

			//number of pallets required
			$palletcount += $this->palletcount();
			if($debug) echo("Pallets required: <strong>$palletcount</strong><br>");

			//set the supplied variable $pallet to true if a pallet may be required
			if($palletcount>=1) {
				$pallet = (boolean)true;
			}

			//return the calculated price
			if($debug) echo("Final total postage: <strong>$totalpostage</strong><br>");
			return $totalpostage;
		}


	/*============================================================================*
	 * Calculates the next available delivery date
	 *============================================================================*/
		function getNextDeliveryDate($t=0, $skipdeliveries=0) {
// die("1");
			$debug = false;
			global $OPT;
			$datefound = false;
			$leadtimeadded = false;
			
			$pickup_date = '';
			$delivery_date = '';

			$_SESSION['getNextDeliveryDate_messages'] = '';
			if(isset($_SESSION['DEBUG'])) {
				$debug = $_SESSION['DEBUG'];
			}

			if($debug) echo('DEBUGGING<br>&nbsp;</br>');
			$lastordertime = strtotime($OPT->latestordertime);

			if($t==0) {
				$t = time();
				$cutoff = strtotime($OPT->latestordertime);
				if($debug) echo(date('d-m-Y l H:i', $t) . ' Using now as start date and '.date('d-m-Y l H:i', $cutoff).' as cuttoff<br>');
			} else {
				$cutoff = strtotime($OPT->latestordertime, $t);
				if($debug) echo(date('d-m-Y l H:i', $t) . ' Using parameter as start date and '.date('d-m-Y l H:i', $cutoff).' as cuttoff<br>');
			}

			
			//look for a valid pickup date
			if($debug) echo('Getting next pickup date<br>');				

			//if we're after the cuttoff time today, start tomorrow
			$today = getdate($t);
			$dow = $today["wday"];
			settype($dow,'string');
			if($debug) echo(date('l H:i', $t) . ' checking cutoff. (cutoff is '.date('l H:i', $cutoff).')<br>');
			if($t > $cutoff) {
				$_SESSION['getNextDeliveryDate_messages'] .= "late, ";
				$t = strtotime(date('Ymd', $t))+(60*60*24);
				if($debug) echo(date('l H:i', $t) . ' skipping, too late in the day. (cutoff was '.date('l H:i', $cutoff).')<br>');
			} else {
				if($debug) echo('cutoff okay<br>');
			}
			//check date okay for pickup
			while(!$datefound) {
				//assume today is okay
				$datefound = true;
				

				//if the current date is a general non-pickup day, move on another day
				$today = getdate($t);
				$dow = $today["wday"];
				settype($dow,'string');
				if($debug) echo('checking dow pickup for '.date('l', $t).'<br>');
				if( strpos($OPT->nopickup,$dow) !== false ) {
					$_SESSION['getNextDeliveryDate_messages'] .= "no pickup, ";
					$t = strtotime(date('Ymd', $t))+(60*60*24);
					$datefound = false;
					if($debug) echo('no pickup on this day of week, moving to '. date('l', $t) . '<br>');
				} else {
					if($debug) echo('dow availability okay<br>');
				}
				
				//if the current date is a specific non-pickup date, move on another day
				$today = getdate($t);
				$dow = $today["wday"];
				if($debug) echo('checking bh pickup for '.date('l', $t).'<br>');
				if( strpos($OPT->nopickupdates,date('Y-m-d', $t)) !== false ) {
					$_SESSION['getNextDeliveryDate_messages'] .= "no pickup bh, ";
					$t = strtotime(date('Ymd', $t))+(60*60*24);
					$datefound = false;
					if($debug) echo('no pickup on this specific day, moving to '. date('l', $t) . '<br>');
				} else {
					if($debug) echo('bh availability okay<br>');
				}
				
			}

			$pickup_date = $t;
			if($debug) echo('<strong>Pickup date found: ' . date('d-m-Y l H:i', $t) . '</strong></br>');

			//look for a valid delivery date
			if($debug) echo('Getting next delivery date<br>');				

			//add the minimum order lead time
			$t += (60*60*24*$OPT->minorderleadtime);
			if($debug) echo('added lead-time, date is now '.date('d-m-Y l H:i', $t).'<br>');
			
			
			//check date okay for delivery
			$datefound = false;
			while(!$datefound) {
				//assume today is okay
				$datefound = true;
				
				//if the current date is a general non-delivery day, move on another day
				$today = getdate($t);
				$dow = $today["wday"];
				settype($dow,'string');
				if($debug) echo('checking dow delivery for '.date('l d-m-Y l H:i', $t).'<br>');
				if( strpos($OPT->nodelivery,$dow) !== false ) {
					$_SESSION['getNextDeliveryDate_messages'] .= "no delivery, ";
					$t = strtotime(date('Ymd', $t))+(60*60*25); //move forward by 25 hours for when pickup time is BST (GMT+1) and delivery time is GMT, or vice-versa
					$datefound = false;
					if($debug) echo('no delivery on this day of week, moving to '. date('l d-m-Y l H:i', $t) . '<br>');
				} else {
					if($debug) echo('dow availability okay<br>');
				}
				
				//if the current date is a specific non-delivery date, move on another day
				$today = getdate($t);
				$dow = $today["wday"];
				if($debug) echo('checking bh delivery for '.date('l', $t).'<br>');
				if( strpos($OPT->nodeliverydates, date('Y-m-d', $t)) !== false ) {
					$_SESSION['getNextDeliveryDate_messages'] .= "no delivery bh, ";
					$t = strtotime(date('Ymd', $t))+(60*60*24);
					$datefound = false;
					if($debug) echo('no delivery on this specific day, moving to '. date('l', $t) . '<br>');
				} else {
					if($debug) echo('bh availability okay<br>');
				}
				
				//if skipping ahead, keep looping
				if($skipdeliveries > 0) {
					if($debug) echo("skipping ahead $skipdeliveries<br>");
					$skipdeliveries--;
					$t = strtotime(date('Ymd', $t))+(60*60*24);
					$datefound = false;
				}
				
				if($emergency_bailout++ > 1000) {
					die("error!");
				}
				
				if($debug) echo("<hr>");
			}				

			$delivery_date = $t;
			if($debug) echo('<strong>Delivery date found: ' . date('d-m-Y l H:i', $t) . '</strong></br>');

			return $t;
		}

	/*===========================================================================*/
}
?>
