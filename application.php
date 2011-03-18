<?php
//any php configuration changes
//error_reporting(E_ALL ^ E_NOTICE);
	error_reporting(E_ALL | E_NOTICE);
	ini_set('arg_seperator.output', '&amp;');
	ini_set('session.use_only_cookies', 1);
	ini_set('session.use_trans_sid', false);
	ini_set('session.name', 'ENIGMASESS');

	class object{};
	$OPT = new object;
		
// load db settings from file
	require_once('config/config.php');

//pull in the common include files
	require_once("include/db.php");
	require_once("include/views.php");
	require_once("include/storedprocedures.php");
	require_once("include/interface.php");
	require_once("include/xtpl.php");
	require_once("include/html.php");
	require_once("include/basket_class.php");
	require_once("include/connect.php");
	require_once("language/en/lang_main.php");

//load in the Joomla configuration
	if(!defined('_VALID_MOS')) {
//		require_once("../configuration.php");
	}


// load main settings from db
	$sql  = "SELECT ConfigKey, ConfigValue ";
	$sql .= "FROM {$OPT->tableprefix}config ";
	$rs = db_query($sql);
	while($r = db_fetch_array($rs)) {
		eval('$OPT->'.$r['ConfigKey'].'="'.$r['ConfigValue'].'";');
	}

//set some shortcuts
	if($OPT->installedadmin) $OPT->adminroot=$OPT->enigmaroot.'admin/';
	if($OPT->installedstore) $OPT->storeroot=$OPT->enigmaroot.'frontend/';
	$dbprefix = $OPT->tableprefix;

	$vat = (float)$OPT->vatrate; //actual percentage, like 17.5;
	$vatadd = (float)(1+($vat/100));  //value to multiply price by to get ex-vat price, like 1.175
	$vatrem = (float)(1/$vatadd); //value to multiply an inc-vat price to get ex-vat

	$OPT->inadmin=false;
	$OPT->instore=true;

/*
//error reporting
	if($OPT->showerrors) {
		error_reporting(E_ALL ^ E_NOTICE);
	} else {
		error_reporting(0);
	}
*/


//set some non-user-configurable options
	$OPT->pictureidlength = 6;


//set some of the DB error display options as per the user options
	$DB_DEBUG=True;
	$DB_KEEP_SQL=False;
	$DB_LOG_QUERY_PARAMS=False;
	if($OPT->ShowDBerrors) {
		$DB_DEBUG = True;
	}
	if ($OPT->LogDBqueries) {
		$DB_KEEP_SQL = True;
	}
	if ($OPT->LogDBqueryparameters) {
		$DB_KEEP_SQL = True;
		$DB_LOG_QUERY_PARAMS = True;
	}


//declare and initialise some logging vars
	$addlog = array(
		'maincat'=>false,
		'subcat'=>false,
		'item'=>false
		);



//load options from the database
	$CFG = array();
	$sql = "SELECT OptionName, OptionValue FROM {$dbprefix}option";
	$rs = db_query($sql);
	while ($r = db_fetch_array($rs)) {
		$CFG[$r['OptionName']] = $r['OptionValue'];
	}

//prepare the session.  All used session vars should be listed here for reference
	session_cache_limiter('none');
	session_start();

//check to see what kind of protocol we should be using
	if(empty($_SESSION['protocol'])) {
		$OPT->currentprotocol=$OPT->httpprotocol;
	} else {
		$OPT->currentprotocol=$_SESSION['protocol'];
	}


//make sure we're in the right place
   if($_SERVER['HTTP_HOST']!=$OPT->httphost) {
//      redirect_page();
      exit;
   }

   //init the basket, if need be
   if (!isset($_SESSION["BASKET"])) {
      $_SESSION["BASKET"] = new Basket;
   }





//check auth
	if(authorised()) {
		$OPT->currentprotocol=$OPT->httpsprotocol;
		require_once("include/views-admin.php");
		require_once("include/storedprocedures-admin.php");
	}


//initialise the log
	AddLog();


//keep pages fresh
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header("Expires: Tue, 28 Jan 2003 04:45:00 GMT"); //date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //always modified
	header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP/1.1

?>
