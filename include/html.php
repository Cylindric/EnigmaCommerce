<?php

	define('ENIGMA_HTML',    0);
	define('ENIGMA_TEXT',    1);
	define('ENIGMA_ADMIN',   2);
	define('ENIGMA_ARTICLE', 3);


	function AddLog($text='') {
		if(!isset($_SESSION['Log'])) {
			$_SESSION['Log']=array();
			array_push($_SESSION['Log'],"New Log started\n");
		}
		if($text!='')
			array_push($_SESSION['Log'],$text."\n");

	}



	//create a new html document
	function start_page(&$objpage, $page, $pagetype=ENIGMA_HTML) {
		global $OPT;
		switch($pagetype) {
			case ENIGMA_HTML:
				$objpage = new XTemplate ($_SERVER['DOCUMENT_ROOT'].$OPT->enigmaroot."html/$page.html");
				$objpage->assign('PAGE_TITLE', $OPT->pagetitle);
				break;

			case ENIGMA_TEXT:
				$objpage = new XTemplate ($_SERVER['DOCUMENT_ROOT'].$OPT->enigmaroot."text/$page.html");
				$objpage->assign('PAGE_TITLE', $OPT->pagetitle);
				break;

			case ENIGMA_ADMIN:
				$objpage = new XTemplate ($_SERVER['DOCUMENT_ROOT'].$OPT->adminroot."html/$page.html");
				$objpage->assign('PAGE_TITLE', $OPT->pagetitle.' Admin');
				break;

			case ENIGMA_ARTICLE:
				$objpage = new XTemplate ($_SERVER['DOCUMENT_ROOT'].$OPT->enigmaroot."articles/$page.html");
				$objpage->assign('PAGE_TITLE', $OPT->pagetitle);
				break;
		}

// 		if(  (authorised() || $page=='login') && ($OPT->usessl)) {
// 			$objpage->assign('PROTOCOL', $OPT->httpsprotocol);
// 		} else {
// 			$objpage->assign('PROTOCOL', $OPT->httpprotocol);
// 		}


		$objpage->assign('PHP_SELF', $_SERVER['PHP_SELF']);
		$objpage->assign('PROTOCOL', $OPT->currentprotocol);
		$objpage->assign('SERVERHOST', $OPT->httphost);
		$objpage->assign('ENIGMA_ROOT', $OPT->enigmaroot);
		$objpage->assign('ADMIN_ROOT', $OPT->adminroot);
		$objpage->assign('CONTROL', $OPT->controlpage);
		$objpage->assign('CO_NAME', $OPT->companyname);
		$objpage->assign('CO_SHORTNAME', $OPT->shortcompanyname);
		$objpage->assign('PRODUCT_IMAGES', $OPT->productimageroot);

		$objpage->assign('SPACER_10', draw_spacer(10,10));
		$objpage->assign('SPACER_14', draw_spacer(14,14));
		$objpage->assign('SPACER_16', draw_spacer(16,16));

		$objpage->assign('YEAR', date('Y'));
		
		$objpage->assign('LANG', $lang);
	}
	
	function end_page(&$objpage) {
		global $OPT, $mosConfig_MetaDesc, $mosConfig_MetaKeys;
 		if(authorised()) {
	 		$objpage->parse('main.adminscripts');
 		}
 		$objpage->assign('SITE_DESCRIPTION', $mosConfig_MetaDesc);
 		
 		
 		if(!empty($OPT->extra_keywords)){
	 		$objpage->assign('EXTRA_KEYWORDS', $OPT->extra_keywords);
	 	} else {
	 		$objpage->assign('SITE_KEYWORDS', $mosConfig_MetaKeys);
		}

     if (!empty($_SESSION['google_transaction_total'])) {
	 		$objpage->assign('TX_ID', $_SESSION['google_transaction_id']);
	 		$objpage->assign('TX_AFFILIATION', $_SESSION['google_transaction_affiliation']);
	 		$objpage->assign('TX_TOTAL', $_SESSION['google_transaction_total']);
	 		$objpage->assign('TX_TAX', $_SESSION['google_transaction_tax']);
	 		$objpage->assign('TX_SHIPPING', $_SESSION['google_transaction_shipping']);
	 		$objpage->assign('TX_CITY', $_SESSION['google_transaction_city']);
	 		$objpage->assign('TX_COUNTY', $_SESSION['google_transaction_county']);
	 		$objpage->assign('TX_COUNTRY', $_SESSION['google_transaction_country']);

			if (!empty($_SESSION['google_transaction_lines'])) {
			  foreach ($_SESSION['google_transaction_lines'] as $txline) {
			 		$objpage->assign('TXL_ID', $txline['id']);
			 		$objpage->assign('TXL_SKU', $txline['sku']);
			 		$objpage->assign('TXL_NAME', $txline['name']);
			 		$objpage->assign('TXL_CATEGORY', $txline['category']);
			 		$objpage->assign('TXL_PRICE', $txline['price']);
			 		$objpage->assign('TXL_QTY', $txline['qty']);
					$objpage->parse('main.googleurchin.transaction.item');
			  }
			}

			$objpage->parse('main.googleurchin.transaction');
    }
		$objpage->parse('main.googleurchin');

		$objpage->parse('main');
		$objpage->out('main');

	}
	
	function redirect_page($newpage='') {
		global $OPT;

		//$protocol=$OPT->httpprotocol;
		//if($secure) $protocol=$OPT->httpsprotocol;
		//$target = $protocol . $OPT->httphost . $newpage;

		$target = $OPT->currentprotocol . $OPT->httphost . $newpage;
		header("Location: $target");
		exit("You should have been redirected to <a href=\"$target\">$target</a> by ".$_SERVER['PHP_SELF']);
	}
	
	function reload_page() {
		if(isset($_SESSION['postreloadpage'])) {
			$goto = $_SESSION['postreloadpage'];
			unset($_SESSION['postreloadpage']);
		} else {
			$goto = $_SERVER['PHP_SELF'];
		}
		redirect_page($goto);
		exit();
	}




/*
  $mosText - Article text
  $mosimages - "\n" sepparated list of images
*/
  function mosParseImages($mosText, $mosimages) {
  	$imagetags = array();
  	$images = explode("\n", $mosimages);

  	foreach($images as $key => $value) {
  		$thisimage = explode("|",$value);
  		$img_name = $thisimage[0];
  		$img_align = $thisimage[1]; //not implemented
  		$img_alt = $thisimage[2];
  		$img_border = $thisimage[3];
  		$img_caption = $thisimage[4]; //not implemented
  		$img_captionpos = $thisimage[5]; //not implemented
  		$img_captionalign = $thisimage[6]; //not implemented
  		$img_width = $thisimage[7]; //not implemented
  		
  		$style = 'style="';
  		if(!empty($img_border)) {
  			$style .= 'border: '.$img_border.'px solid black;';
  		}
  		$style .= '"';
  		
  		array_push($imagetags, '<img '.$style.' src="/images/stories/'.$img_name.'" alt="'.$img_alt.'" />');
  	}
  	
  	//replace {mosimage} tags with the image
  	$i = 0;
  	$startpos = strpos($mosText, '{mosimage}', $startpos);
  	while($startpos !== false) {
  		$img = $imagetags[$i++];
  		$new  = substr($mosText, 0, $startpos);
  		$new .= $img;
  		$new .= substr($mosText, $startpos + 10);
  		
  		$mosText = $new;
  		$startpos = strpos($mosText, '{mosimage}', $startpos + 1);
  	}

    return $mosText;
  }


  function mosModuleText($moduleid = 0, &$title) {
		$mosTitle = '';
		$mosText = '';
		$mossql  = "SELECT title, content FROM jos_modules m \n";
		$mossql .= "WHERE id = '$moduleid' \n";
		$mossql .= "LIMIT 0,1 \n";
		$rsmos = db_query($mossql);
		if($rmos = db_fetch_array($rsmos)) {
			$mosTitle = $rmos['title'];
			$mosText = $rmos['content'];
			
			//Fix urls that the editor defaulted to ../../..
			//$mosText = str_replace('../../..', '', $mosText);
			$mosText = str_replace('"store', '"/store', $mosText);
			$mosText = str_replace('"images', '"/images', $mosText);
		}

		$title = $mosTitle;
		return $mosText;
 
  
  }

	function ParseMosBots($mosText) {
		// extract the codes
		$regex = '#{store}(.*?){/store}#s';
		preg_match_all($regex, $mosText, $matches);
	
		foreach($matches[1] as $match)
		{
	
			$parts = explode("|", $match);
			
			$stockcode = 0;
			$action = '';
	
			$error = '';
			switch(count($parts))
			{
				case 0:
				case 1:
					$error = "insufficient parameters";
					$storetext = '';
					break;
				
				case 2:
					$stockcode = $parts[0];
					$action = $parts[1];
					break;
	
			}
			
			if(strlen($error) == 0)
			{
				$details = getDetailFromStockCode($stockcode);
	
				switch($action) {
					case 'details':
						$rsdetails = view_details($details['ItemID']);
						$storetext  = '<span class="itemdetails">';
						$storetext .= '<table class="grid" cellspacing="0" cellpadding="0">';
						$storetext .= '<tr class="header">';
						$storetext .= '<td class="first">&nbsp;</td>';
						$storetext .= '<td class="centre">&nbsp;</td>';
						$storetext .= '<td class="centre">RRP</td>';
						$storetext .= '<td class="centre">Our&nbsp;Price</td>';
						$storetext .= '<td>&nbsp;</td>';
						$storetext .= '</tr>';
						while ($r = db_fetch_array($rsdetails)) {
							$storetext .= '<tr>';
							$storetext .= '<td class="first" class="stockcode">'.format_stockcode($r['StockCodePrefix'],$r['StockCode']).'</td>';
							$storetext .= '<td class="currency">'.$r['DetailName'].'</td>';
							$storetext .= '<td class="currencyrrp">'.format_currency($r['RecommendedPrice']).'</td>';
							$storetext .= '<td class="currency">'.format_currency($r['RetailPrice']).'</td>';
							$storetext .= '<td style="white-space:nowrap"><a href="'.$details['BuyURL'].'" class="buy">Buy</a></td>';
							$storetext .= '</tr>';
						}
						$storetext .= '</table>';
						$storetext .= '</span>';
					break;
	
					case 'buy':
						$storetext = '<a href="'.$details['BuyURL'].'" class="buy">Buy</a>';
					break;
	
					case 'price':
						$storetext = number_format($details['RetailPrice'],2);
					break;
	
					case 'image':
						$storetext = processImage($stockcode, $details['ItemName']);
	
					break;
				}
				
			} else {
				$storetext = $error;
			}
	
			$mosText = str_replace("{store}$match{/store}", $storetext, $mosText );
	
		}
		
		return $mosText;
	}
function processImage($stockcode, $title) {
	
	$filename = getPictureFromStockCode($stockcode);
	
	// image size attibutes
	$size = '';
	if ( function_exists( 'getimagesize' ) ) {
		$size 	= @getimagesize( $mosConfig_absolute_path .'/store/img/products/' . $filename );
		if (is_array( $size )) {
			$size = ' width="'. $size[0] .'" height="'. $size[1] .'"';
		}
	}

	// assemble the <image> tag
	$image = '<img src="'. $mosConfig_live_site .'/store/img/products/'. $filename .'"'. $size;
	// no aligment variable - if caption detected
	$image .=' hspace="6" alt="'. $title .'" title="'. $title .'" />';
	
	return $image;			

}


	function mostext($categoryid = 0) {
	
		$mosCategoryID = 19;
		$mosText = '';
		$mossql  = "SELECT introtext, images FROM jos_content m \n";
		$mossql .= "WHERE catid = '$mosCategoryID' \n";
		$mossql .= "AND title_alias = '$categoryid' \n";
		$mossql .= "LIMIT 0,1 \n";
		$rsmos = db_query($mossql);
		if($rmos = db_fetch_array($rsmos)) {
			$mosText = $rmos['introtext'];
			$mosimages = $rmos['images'];
			
			//parse some mambots
			if(strpos($mosText, '{store}') !== false) {
				$mosText = ParseMosBots($mosText);
			}
			
			//handle images
			if(!empty($mosimages)) {
			  $mosText = mosParseImages($mosText, $mosimages);
			  
			}
		}
		
		return $mosText;
	}

/*============================================================================*
 * some login function
 *============================================================================*/
 	function authorised() {
	 	return !empty($_SESSION['loggedin']);
 	}

	function require_login() {
		global $OPT;

		if(!authorised()) {
			$_SESSION['postreloadpage']=$_SERVER['PHP_SELF'];
			redirect_page($OPT->enigmaroot.$OPT->controlpage.'/login');
		}
	}

	function login($userid, $username, $firstname, $lastname) {
		$_SESSION['loggedin']=true;
		$_SESSION['userid']=$userid;
		$_SESSION['username']=$username;
		$_SESSION['userfirstname']=$firstname;
		$_SESSION['userlastname']=$lastname;
	}

	function logout() {
		unset($_SESSION['loggedin']);
		unset($_SESSION['userid']);
		unset($_SESSION['username']);
		unset($_SESSION['userfirstname']);
		unset($_SESSION['userlastname']);
	}


/*============================================================================*
 * make sure we're using a secure transfer protocol
 *============================================================================*/
	function require_https() {
		global $OPT, $html;
		if ( ($OPT->usessl) && ($_SERVER["SERVER_PORT"] != 443)) {
			$OPT->currentprotocol=$OPT->httpsprotocol;
			$_SESSION['protocol']=$OPT->currentprotocol;
			redirect_page($_SERVER["PHP_SELF"]);
			exit;
		}
	}

/*============================================================================*
 * item lookups
 *============================================================================*/
	//if you put an item code of the form #xxx in the descriptions, it will be
	//replaced with a hyperlink or just text, depending on the value of
	//$hyperlink
	//
	/* USAGE:
	
	 #{SEARCH}{TYPE}{FORMAT}{CODE}#
	 
	    {SEARCH} = I|S
	    {TYPE} = L|T|B
	    {FORMAT} = A..Z
	    {CODE} = (0-9)*4 | (0-9)*5

	    
	    FORMATS
	    A  item name only
	    B  detail name only
	    C  item and detail name
	    D  price
	    
	*/	  

	function hyperlink_items($intext, $hyperlink=true) {
		global $OPT;

		$outtext=$intext;

		if(strpos($outtext, '#')!==false) {
			
			//find all item-id links
// 			$pattern='(#n([0-9]{4})#)';
// 			$numfound=preg_match_all($pattern, $outtext, $matches);
// 			for ($i=0; $i<$numfound; $i++) {
// 				$itemid=intval($matches[1][$i]);
// 				$rs=view_items(0,$itemid);
// 				$r=db_fetch_array($rs);
// 				$itemname=$r['ItemName'];
// 				//replace the codes with the names
// 				$outtext=str_replace('#n'.str_pad($itemid,4,'0',STR_PAD_LEFT).'#', '<a class="itemlink" href="'.$OPT->enigmaroot.$OPT->controlpage.'/item/'.$itemid.'/1#item_'.$itemid.'">'.$itemname.'</a>', $outtext);
// 			}

			//find all stock-code links
			$pattern='(#S([0-9,A,B,C,D,L,T]{7})#)';
			$numfound=preg_match_all($pattern, $outtext, $matches);
//			echo("found $numfound codes<br>");
			for ($i=0; $i<$numfound; $i++) {
				$linktype = substr($matches[1][$i],0,1); //first character is link type (L|T)
				$formatcode = substr($matches[1][$i],1,1); //second character is format type (A|B|C)
				$searchcode = substr($matches[1][$i],-5); //last 5 characters is stock code (xxxxx)
				
				$rs=view_search($searchcode);
				$r=db_fetch_array($rs);
				$dbitemname = $r['ItemName'];
				$dbdetailname = $r['DetailName'];
				$dbitemid = $r['ItemID'];
				$dbdetailid = $r['DetailID'];
				$dbdetailprice = GetDetailPrice($dbdetailid);

				switch($formatcode) {
					case 'A':
						$label_text = $dbitemname;
					break;
					case 'B':
						$label_text = $dbdetailname;
					break;
					case 'C':
						$label_text = $dbitemname . ' (' . $dbdetailname . ')';
					break;
					case 'D':
						$label_text = format_currency($dbdetailprice, 'POA');
					break;
				}
				switch($linktype) {
					case 'T':
						$label_text = $label_text;
					break;
					case 'L':
						$label_text = '<a class="itemlink" href="'.$OPT->enigmaroot.$OPT->controlpage.'/item/'.$dbitemid.'/1#item_'.$dbitemid.'">'.$label_text.'</a>';
					break;
					case 'B':
						$label_text = '<a class="itemlink" href="'.$OPT->enigmaroot.$OPT->controlpage.'/basket/add/'.$dbdetailid.'/1">'.$label_text.'</a>';
					break;
				}
					
				$outtext = str_replace('#S'.$linktype.$formatcode.str_pad($searchcode,5,'0',STR_PAD_LEFT).'#', $label_text, $outtext);
			}

		}


		return $outtext;
	}


/*============================================================================*
 * some other functions
 *============================================================================*/
	//create a new tab on pages with a tab setup
	function add_tab(&$objpage, $tabid, $tablabel, $currenttab) {
		$objpage->assign('TAB_ID', $tabid);
		$objpage->assign('TAB_LABEL', $tablabel);
		$objpage->assign('TAB_STATE', '_f3');
		if ($currenttab!=$tabid) {
			$objpage->assign('TAB_STATE', '');
			$objpage->parse('main.tablabel.activeA');
			$objpage->parse('main.tablabel.activeB');
		}
		$objpage->parse('main.tabtop');
		$objpage->parse('main.tablabel');
		$objpage->parse('main.tabbottom');
	}




	//returns a complete <img> tag for the file specified, unless it doesn't exist,
	//in which case it returns a "missing" image
	function img_safe($src, $alt=' ', $class='', $name='', $fix_height=0) {
		global $OPT;
		if(!empty($class)) {
			$class="class=\"$class\"";
		}

		if(!empty($name)) {
			$name = "name=\"$name\"";
		}

		$localimage=$_SERVER['DOCUMENT_ROOT'].$src;
		if (  ($src==$OPT->productimageroot) || (!file_exists($localimage)) ) {
			$newsrc=$OPT->enigmaroot."img/noproduct.png";
			$localimage=$_SERVER['DOCUMENT_ROOT'].$newsrc;
			$imageprops = getimagesize($localimage);
			$imagewidth = $imageprops[0];
			$imageheight = $imageprops[1];
			$imageaspect = $imageheight/$imagewidth;
			if($fix_height != 0) {
				if(substr($fix_height, -1) == '%') {
					$fix_height = $imageheight * 0.01 * substr($fix_height, 0, strlen($fix_height)-1);
				}
				$imageheight = $fix_height;
				$imagewidth = $imageheight / $imageaspect;
			}
			$returnval = "<img $name $class src=\"$newsrc\" width=\"$imagewidth\" height=\"$imageheight\" alt=\"Image not found (".$_SERVER['DOCUMENT_ROOT'].$src.")\" />";
		} else {
			$imageprops = getimagesize($localimage);
			$imagewidth = $imageprops[0];
			$imageheight = $imageprops[1];
			$imageaspect = $imageheight/$imagewidth;
			if($fix_height != 0) {
				if(substr($fix_height, -1) == '%') {
					$fix_height = $imageheight * 0.01 * substr($fix_height, 0, strlen($fix_height)-1);
				}
				$imageheight = $fix_height;
				$imagewidth = $imageheight / $imageaspect;
			}
			$returnval = "<img $name $class src=\"$src\" width=\"$imagewidth\" height=\"$imageheight\" alt=\"$alt\" />";
		}
		return $returnval;
	}


	function img_general($src, $width, $height, $alt) {
		global $OPT;
		$src = $OPT->enigmaroot . "img/ui/$src.gif";
		return "<img src=\"$src\" width=\"$width\" height=\"$height\" alt=\"$alt\">";
	}

	//create a spacer of the required size
	function img_spacer($width=5, $height=5) {
		global $OPT;
		$src = $OPT->enigmaroot . 'img/spacer.gif';
		return "<img src=\"$src\" width=\"$width\" height=\"$height\" alt=\" \">";
	}

	//create a menu spacer of the required size
	function img_menuspacer($count=1) {
		return str_repeat("<img src=\"".$OPT->enigmaroot."img/admin/menu_spacer.gif\" width=\"9\" height=\"18\" alt=\" | \">",$count);
	}


	//format a number as currency
	function format_currency($numberval, $zeroformat='', $negativeformat='', $currencysymbol='&pound;') {
		if(($numberval == 0) && ($zeroformat != '')) {
			return $zeroformat;

		} elseif(($numberval < 0) && ($negativeformat!='')) {
			return $negativeformat;

		} else {
			return $currencysymbol . number_format($numberval, 2);
		}
	}

	//format a number as percentage
	function format_percentage($numberval, $zeroformat='', $negativeformat='', $symbol='%', $decimals=1) {
		if(($numberval == 0) && ($zeroformat != '')) {
			return $zeroformat;

		} elseif(($numberval < 0) && ($negativeformat != '')) {
			return $negativeformat;

		} else {
			return number_format($numberval*100, $decimals) . $symbol;
		}
	}



	//formats a number, removes insignificant zeroes
	function format_numbercompact($numberval) {

		$returnval = number_format($numberval, 2);
		while(substr($returnval,-1,1)=='0') {
			$returnval = substr($returnval, 0, strlen($returnval)-1);
		}
		if(substr($returnval,-1,1)==".") {
			$returnval = substr($returnval, 0, strlen($returnval)-1);
		}

		return $returnval;
	}


	//format a timestamp as a string
	//datetime values come in the format yyyy-mm-dd hh:mm:ss
	//timestamps come as yyyymmddhhmmss
	function format_datetime($datetime='', $format='') {
		global $OPT;

		if($format=='') $format=$OPT->longdatetime;

		if ( ($datetime!=0) && (strtotime($datetime)!='943920000')) {
			$returnval = date($format, strtotime($datetime));
		}
		return $returnval;
	}


	function format_stockcode($prefix, $code) {
		 return sprintf("%02d%03d", $prefix, $code);
	}


	//returns a valid checked attribute if the value is true
	function checked_value($numberval) {
		if ($numberval!=0)
			return 'checked="checked"';
		else
			return '';
	}



	//shorten a string
	//  ('ABCDEFGHIJKLM',7,'...') => 'AB...LM'

	function shorten_string($stringval, $length=30, $joiner='...') {
		$charlen = ($length-strlen($joiner))*0.5;

		if(strlen($stringval) > $length-strlen($joiner))
			return substr($stringval, 0, $charlen) . $joiner . substr($stringval, -$charlen, $charlen);
		else
			return $stringval;

	}


	//check and return a $_GET var
	//	httpget_num(
	//		$getvar				name of the $_GET[] variable to query
	//		[$default=0]		default value if $_GET is invalid or missing
	//		[$sessdef=False]	get default from session variable
	//		[$sesssave=True]	save result to session variable
	//		[$sessvar='']		name of session variable
	//	)
	// If the $_GET var is not numeric, then return whatever is in the specified session var
	// If the session var is not numeric either, then return the specified default

	function httpget_num($getvar, $default=0, $sessdef=False, $sesssave=True, $sessvar='') {

		//should default come from session?
		if ( ($sessdef==True) & (!empty($_SESSION[$sessvar]))) {
			$default = $_SESSION[$sessvar];
		}

		//set output to default
		$returnvalue = $default;

		//get new value from $_GET var
		if (is_numeric($_GET[$getvar])) {
			$returnvalue = $_GET[$getvar];
		}

		//optionally save to session
		if ( (is_numeric($returnvalue)) && (strlen($sessvar)>0) ) {
			$_SESSION[$sessvar] = $returnvalue;
		}

		return $returnvalue;
	}






	//functions for getting validated form responses
	function httppost_num($getvar, $default=0) {
		$returnvalue = $default;
		if (  (!empty($_POST[$getvar])) && (is_numeric($_POST[$getvar]))  ) {
			$returnvalue = $_POST[$getvar];
		}
		return $returnvalue;
	}



	function httppost_string($getvar, $default='', $minlen='', $maxlen='') {
		$returnvalue = $default;
		if (!empty($_POST[$getvar])) {
			$returnvalue = $_POST[$getvar];
		}

		if( (is_numeric($minlen)) && (strlen($returnvalue)<$minlen) ) {
			$returnvalue = $default;
		}
		if(is_numeric($maxlen)) {
			$returnvalue = substr($returnvalue, 0, $maxlen);
		}

		return $returnvalue;
	}

	





	//some wrapper functions for the above
	function httpget_int($getvar, $default=0, $sessdef=False, $sesssave=True, $sessvar='') {
		return intval(httpget_num($getvar, $default, $sessdef, $sesssave, $sessvar));
	}
	function httppost_int($getvar, $default=0) {
		return intval(httppost_num($getvar, $default));
	}




	function draw_select($selectname, $table, $keyfield, $valuefield, $selectedkey=0, $zerotext='', $tabid=-1) {
		global $OPT, $dbprefix;

		$tabindex='';
		if($tabid>=0) {
			$tabindex = " tabindex=\"$tabid\"";
		}

		$sql  = "SELECT $keyfield, $valuefield \n";
		$sql .= "FROM {$dbprefix}$table \n";
		$sql .= "ORDER BY $valuefield \n";
		$returnval = "<select id=\"$selectname\" name=\"$selectname\"$tabindex>";
		if(strlen($zerotext)>0) $returnval .= "<option value=\"$selectedkey\" selected=\"selected\">$zerotext</option>";
		$rs = db_query($sql);
		while ($r=db_fetch_array($rs)) {
			$returnval .= "<option value=\"". $r[$keyfield] ."\"";
			if($r[$keyfield]==$selectedkey) $returnval.= ' selected="selected"';
			$returnval .= ">" . htmlentities($r[$valuefield]) . "</option>";
		}
		$returnval .= '</select>';
		return $returnval;
	}


	function draw_selectpicture($selectname, $selectedkey=0, $zerotext='') {
		global $OPT, $dbprefix;

		$sql  = "SELECT PictureID, PictureName, FileName \n";
		$sql .= "FROM {$dbprefix}picture \n";
		$sql .= "ORDER BY PictureName \n";
		$returnval = "<select id=\"$selectname\" name=\"$selectname\">";
		if(strlen($zerotext)>0) $returnval .= "<option value=\"$selectedkey\" selected=\"selected\">$zerotext</option>";
		$rs = db_query($sql);
		while ($r=db_fetch_array($rs)) {
			$returnval .= "<option value=\"". $r['PictureID'] ."\"";
			if($r['PictureID']==$selectedkey) $returnval.= ' selected="selected"';
			$returnval .= ">" . htmlentities($r['PictureName'].' ('.$r['FileName'].')') . "</option>";
		}
		$returnval .= '</select>';
		return $returnval;
	}


	//draws a <select> widget with a numbered range.  If defaulttext is specified,
	//then the specified number in the range is selected and has the corresponding label
	function draw_numberselect($selectname, $startval, $endval, $step, $selected, $defaulttext='') {
		$returnval = "<select id=\"$selectname\" name=\"$selectname\">";
		if($selected==0) {
			$returnval .= "<option value=\"$selected\" selected=\"selected\">$defaulttext</option>";
		}
		for($i=$startval; $i<=$endval; $i+=$step) {
			$returnval .= "<option value=\"$i\"";
			if($i==$selected) $returnval.= ' selected="selected"';
			$returnval .= ">".str_pad($i,2,'0',STR_PAD_LEFT)."</option>";
		}
		$returnval .= '</select>';
		return $returnval;
	}



	//draws a <select> widget from an array
	function draw_arrayselect($selectname, $array, $selectedkey=0, $zerotext='', $tabid=-1) {
		$tabindex='';
		if($tabid>=0) {
			$tabindex = " tabindex=\"$tabid\"";
		}

		$returnval = "<select id=\"$selectname\" name=\"$selectname\"$tabindex>";
		if(strlen($zerotext)>0) $returnval .= "<option value=\"$selectedkey\" selected=\"selected\">$zerotext</option>";
		reset($array);
		foreach($array as $key=>$value) {
			$returnval .= "<option value=\"$key\"";
			if($key==$selectedkey) $returnval.= ' selected="selected"';
			$returnval .= ">$value</option>";
		}
		$returnval .= '</select>';
		return $returnval;
	}

	//draws a <select> widget containing all the available categories
	//if $itemid is not 0 then only those categories valid for that item
	//are returned
	function draw_selectcategory($selectname, $selectedkey=-1, $zerotext='', $parentid=-1) {
		global $OPT, $dbprefix;

    if($parentid>=0) {
  		$sql  = "SELECT Child.CategoryID ChildID, Parent.CategoryName ParentName, Child.CategoryName ChildName \n";
  		$sql .= "FROM {$dbprefix}category Child LEFT JOIN {$dbprefix}category Parent ON Child.ParentID=Parent.CategoryID \n";
  		$sql .= "WHERE Child.ParentID='{$parentid}' \n";
  		$sql .= "ORDER BY ParentName, ChildName \n";
    } else {
    	$sql  = "SELECT Child.CategoryID ChildID, Parent.CategoryName ParentName, Child.CategoryName ChildName \n";
    	$sql .= "FROM {$dbprefix}category Child LEFT JOIN {$dbprefix}category Parent ON Child.ParentID=Parent.CategoryID \n";
    	$sql .= "ORDER BY ParentName, ChildName \n";
    }

		$returnval  = "<select id=\"$selectname\" name=\"$selectname\">";

		$selected='';
		if($selectedkey==0) $selected=' selected="selected"';
		$returnval .= "<option value=\"0\"\"$topselected>Top</option>";

		$selected='';
		if($selectedkey==-1) $selected=' selected="selected"';
		if(strlen($zerotext)>0) $returnval .= "<option value=\"$selectedkey\"$selected>$zerotext</option>";

		$rs = db_query($sql);
		while ($r=db_fetch_array($rs)) {
			$dbChildID = $r['ChildID'];
			if(strlen($r['ParentName'])==0) {
  			$dbCatName = htmlentities($r['ChildName']);
			} else {
  			$dbCatName = htmlentities($r['ParentName'] . '::' . $r['ChildName']);
  		}

			$selected='';
			if($selectedkey==$dbChildID) $selected=' selected="selected"';
			$returnval .= "<option value=\"$dbChildID\"$selected>$dbCatName</option>";
		}
		$returnval .= '</select>';
		return $returnval;
	}



	function draw_selectitem($selectname, $selectedkey=-1, $zerotext='') {
		global $OPT, $dbprefix;

		$sql  = "SELECT i.ItemID, i.ItemName \n";
		$sql .= "FROM {$dbprefix}item i \n";
		$sql .= "ORDER BY i.ItemName \n";

		$returnval  = "<select id=\"$selectname\" name=\"$selectname\">";

		$selected='';
		if($selectedkey==-1) $selected=' selected="selected"';
		if(strlen($zerotext)>0) $returnval .= "<option value=\"$selectedkey\"$selected>$zerotext</option>";

		$rs = db_query($sql);
		while ($r=db_fetch_array($rs)) {
			$dbItemID = $r['ItemID'];
			$dbItemName = htmlentities($r['ItemName']);

			$selected='';
			if($selectedkey==$dbItemID) $selected=' selected="selected"';
			$returnval .= "<option value=\"$dbItemID\"$selected>$dbItemName</option>";
		}
		$returnval .= '</select>';
		return $returnval;
	}



	function draw_rateselect($selectname, $startval, $endval, $step, $currentval, $baseval) {
		$startval=$startval/100;
		$endval=$endval/100;
		$step=$step/100;

		$returnval  = "<select id=\"$selectname\" name=\"$selectname\">";

		$currentadded=false;
		for($i=$startval; $i<=$endval; $i+=$step) {
			if($i==$currentval) $currentadded=true;

			if( ($i>$currentval) && ($currentadded==false) ) {
				$returnval .= "<option value=\"$currentval\" selected=\"selected\">".format_percentage($currentval)."</option>";
				$currentadded=true;
			}

			$selected='';
			if( ($currentadded==false) && ($i==$currentbal) ) $selected=' selected="selected"';
			//$returnval .= "<option value=\"$i\"$selected>".format_percentage($i)."  =  ".format_currency($baseval*(1+$i))."</option>";
			$returnval .= "<option value=\"$i\"$selected>".format_percentage($i)."</option>";
		}

		$returnval .= '</select>';

		return $returnval;
	}


	function draw_inputimage($name, $src, $width, $height, $alt=' ', $tabid=-1) {
		global $OPT;

		$tabindex='';
		if($tabid>=0) {
			$tabindex = " tabindex=\"$tabid\"";
		}

		$src1 = $OPT->enigmaroot."img/ui/{$src}.gif";
		$src2 = $OPT->enigmaroot."img/ui/{$src}_f2.gif";

		$returnval = "<input onmouseover=\"MM_swapImage('$name','','$src2', 1)\" onmouseout=\"MM_swapImgRestore()\" name=\"$name\" id=\"$name\" type=\"image\" src=\"$src1\" width=\"$width\" height=\"$height\" alt=\"$alt\"$tabindex />";

		return $returnval;
	}

	function draw_rolloverimage($name, $src, $target, $width, $height, $alt=' ', $tabid=-1, $onClick='') {
		global $OPT;

		$tabindex='';
		if($tabid>=0) {
			$tabindex = " tabindex=\"$tabid\"";
		}
		if(!empty($onClick)) {
			$onClick = " onClick=\"$onClick\"";
		}

		$src1 = $OPT->enigmaroot."img/ui/{$src}.gif";
		$src2 = $OPT->enigmaroot."img/ui/{$src}_f2.gif";

		$returnval  = "<a href=\"$target\" onmouseover=\"MM_swapImage('$name','','$src2', 1)\" onmouseout=\"MM_swapImgRestore()\"$onClick>";
		$returnval .= "<img name=\"$name\" id=\"$name\" src=\"$src1\" width=\"$width\" height=\"$height\" alt=\"$alt\"$tabindex />";
		$returnval .= "</a>";

		return $returnval;
	}

	function draw_spacer($width, $height) {
		global $OPT;

		return "<img src=\"".$OPT->enigmaroot."img/spacer.gif\" width=\"$width\" height=\"$height\" alt=\" \">";
	}




/*============================================================================*
 *limit a string to a certain length
 *  1.  Trims leading and trailing white-space
 *  2.  Shortens string to specified number of lines (based on \n)
 *  3.  Shortens to specified length (based on number of chars)
 *============================================================================*/
	function ShrinkString ($value, $length=0, $terminator="...", $maxlines=0) {

		$retval = $value;

		//1. remove leading and trailing noise
		$retval = trim($retval);

		//remove extra new-lines within text
		//$retval = str_replace("\n\n", "\n", $retval);
 		$retval = preg_replace("[\n\s\n]", "", $retval);

 		//remove html tags
 		$retval = strip_tags($retval);

		//2. shorten to specified numner of newlines
		if ( ($maxlines > 0) && (strpos($retval, "\n") !== false) ) {
			$start=0;
			//skip nl's
			for ($i=1;$i<$maxlines;$i++) {
				$start = strpos($retval, "\n", $start)+1;
			}
			$retval = trim(substr($retval, 0, strpos($retval, "\n",$start)-1)) . $terminator;
		}

		//3. shorten to specified length
		if ( ($length > 0) && (strlen($retval) > ($length-strlen($terminator))) ) {
			$retval = trim(substr($retval, 0, $length)) . $terminator;
		}

		return $retval;
	}




function parse_codes($text) {
	//skip if nothing to parse	
	if (! (strpos(' '.$text, "[") && strpos(' '.$text, "]")) ) {
		return $text;
	}

	$patterns = array();
	$replacements = array();
	
	//new lines
	$text = str_replace("\n", '<br />', $text);
	
	//heading
	$patterns[] = "#\[head\](.*?)\[/head\]#si";
	$replacements[] = "<strong class=\"ecode_header\">\\1</strong>";
	
	//subheading
	$patterns[] = "#\[sub\](.*?)\[/sub\]#si";
	$replacements[] = "<strong class=\"ecode_subheader\">\\1</strong>";

	//bold
	$patterns[] = "#\[bolder\](.*?)\[/bolder\]#si";
	$replacements[] = "<strong class=\"ecode_bolder\">\\1</strong>";
		
	//box
	$patterns[] = "#\[box\](.*?)\[/box\]#si";
	$replacements[] = "<div class=\"ecode_box\">\\1</div>";
		
	//replace all items
	$text = preg_replace($patterns, $replacements, $text);
	
	return $text;
}



function html_to_text($htmlin) {
	$lookfor 		= array('<p>', '</p>', '<br>', '<br />');
	$replacewith	= array("\n",  "\n",   "\n",   "\n");

	$textversion = $htmlin;
	$textversion = str_replace($lookfor, $replacewith, $textversion);
	$textversion = html_entity_decode($textversion);
	
	return $textversion;
}




function fancy_vardump(&$vInput, $depth = 0) {
    $bgs = array ('#DDDDDD', '#C4F0FF', '#BDE9FF', '#FFF1CA');

    $bg = &$bgs[$depth % sizeof($bgs)];

    $s = "<table border='0' cellpadding='4' cellspacing='1'><tr><td style='background: none $bg; text-align: left; ";
    if (is_int($vInput)) {
        $s .= "'>";
        $s .= sprintf('int (<b>%d</b>)', intval($vInput));
    } else if (is_float($vInput)) {
        $s .= "'>";
        $s .= sprintf('float (<b>%f</b>)', doubleval($vInput));
    } else if (is_string($vInput)) {
        $s .= "'>";
        $s .= sprintf('string[%d] (<b>"%s"</b>)', strlen($vInput), $vInput);
    } else if (is_bool($vInput)) {
        $s .= "'>";
        $s .= sprintf('bool (<b>%s</b>)', ($vInput === true ? 'true' : 'false'));
    } else if (is_resource($vInput)) {
        $s .= "'>";
        $s .= sprintf('resource (<b>%s</b>)', get_resource_type($vInput));
    } else if (is_null($vInput)) {
        $s .= "'>";
        $s .= sprintf('null');
    } else if (is_array($vInput)) {
        $s .= "border-bottom: solid 2px black;'>";
        $s .= sprintf('array[%d]', count($vInput));
        $s .= "</td></tr><tr><td style='background: none $bg; text-align: left;'>" .
              "<table border='0' cellpadding='4' cellspacing='1'>";
        foreach ($vInput as $vKey => $vVal) {
            $s .= '<tr>';
            $s .= "<td style='background-color: $bg; text-align: left;'>" .
                  sprintf('<b>%s%s%s</b>', ((is_int($vKey)) ? '' : '"'), $vKey, ((is_int($vKey)) ? '' : '"')) .
                  '</td>';
            $s .= "<td style='background-color: $bg; text-align: left;'>=></td>";
            $s .= "<td style='background-color: $bg; text-align: left;'>" .
                  fancy_vardump($vVal, $depth+1) .
                  '</td>';
            $s .= '</tr>';
        }
        $s .= '</table>';
    } else if (is_object($vInput)) {
        $s .= "border-bottom: solid 2px black;'>";
        $s .= sprintf('object (<b>%s</b>)', get_class($vInput));
        $s .= "</td></tr><tr><td style='background: none $bg; text-align: left;'>" .
              "<table border='0' cellpadding='4' cellspacing='1'>";
        foreach (get_object_vars($vInput) as $vKey => $vVal) {
            $s .= '<tr>';
            $s .= "<td style='background-color: $bg; text-align: left;'>" .
                  sprintf('<b>%s%s%s</b>', ((is_int($vKey)) ? '' : '"'), $vKey, ((is_int($vKey)) ? '' : '"')) .
                  '</td>';
            $s .= "<td style='background-color: $bg; text-align: left;'>=></td>";
            $s .= "<td style='background-color: $bg; text-align: left;'>" .
                  fancy_vardump($vVal, $depth+1) .
                  '</td>';
            $s .= '</tr>';
        }
        $s .= '</table>';
    } else {
        $s .= "'>";
        $s .= sprintf('<b>unhandled (gettype() reports "%s")', gettype($vInput));
    }
    $s .= '</td></tr></table>';

    return $s;
}






function RTESafe($strText) {
	//returns safe code for preloading in the RTE
	$tmpString = trim($strText);

	//convert all types of single quotes
	$tmpString = str_replace(chr(145), chr(39), $tmpString);
	$tmpString = str_replace(chr(146), chr(39), $tmpString);
	$tmpString = str_replace("'", "&#39;", $tmpString);

	//convert all types of double quotes
	$tmpString = str_replace(chr(147), chr(34), $tmpString);
	$tmpString = str_replace(chr(148), chr(34), $tmpString);
//	$tmpString = str_replace("\"", "\"", $tmpString);

	//replace carriage returns & line feeds
	$tmpString = str_replace(chr(10), " ", $tmpString);
	$tmpString = str_replace(chr(13), " ", $tmpString);

	return $tmpString;
}



?>
