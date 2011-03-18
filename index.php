<?php

/* ----------------------------------------------------------------
  Notes: This page does all the clever stuff.
  Which elements to display is determined here from
  the requested URL, and any special actions, such
  as adding/removing items from the basket are done
  here too (actually by basket.php, but controlled
  from here.)
  The first option should always be the target page,
  such as item subsequent options depend on the
  first:

  article    articlename [options]
  basket     action [itemid quantity]
  category   maincatid [subcatid [itemlistpage] [showeditbox]]
  checkout
  edit_list  list
  item       itemid [resetnav [editpage [edittype detailid]]
  logon
  order      [orderid]
  picture    action [pictureid]
  search     needle
  stocktake
  text       textfile
  tools      toolname [options]
  weblog
  wizard     name
  ------------------------------------------------------------------- */

function CleanNavigationSessions() {
    unset($_SESSION['maincatid']);
    unset($_SESSION['subcatid']);
    unset($_SESSION['itemid']);
    unset($_SESSION['itemlistpage']);
    unset($_SESSION['postreloadpage']);
}

require_once('application.php');

//this array will hold the options set on the URL
$PAGEOPT = array();
$pagebuffering = true;

// $_SESSION['google_transaction_total']='';
// $_SESSION['google_transaction_lines'] = array();
// $_SESSION['google_transaction_affiliation'] = 123;
// $_SESSION['google_transaction_total'] = 456;
// $_SESSION['google_transaction_tax'] = 0;
// $_SESSION['google_transaction_shipping'] = 575;
// $_SESSION['google_transaction_city'] = 'here';
// $_SESSION['google_transaction_county'] = 'there';
// $_SESSION['google_transaction_country'] = 'everywhere';
// $_SESSION['google_transaction_lines'] = array();
// $line = array();
// $line['id'] = 123;
// $line['sku'] = '';
// $line['name'] = 'item 1 sub item 3';
// $line['category'] = '';
// $line['price'] = '333';
// $line['qty'] = '1';
// $_SESSION['google_transaction_lines'][] = $line;
// ----------------------------------------------------------------------------
//default visibility for all elements
$encryptpage = false;
$requireuser = false;
$PAGEOPT['deletedvis'] = $OPT->showdeletedobjects;
if (!empty($_SESSION['deletedvis'])) {
    $PAGEOPT['deletedvis'] = $_SESSION['deletedvis'];
}

$PAGEVIS = array(
    'MiniSearch' => true,
    'Brands' => false,
    'SecureLogo' => true,
    'MainCategories' => true,
    'SubCategories' => true,
    'ItemList' => true,
    'Item' => true,
    'MiniBasket' => true,
    'FullBasket' => false,
    'KLFilterBox' => false,
    'KusuriBox' => true,
    'Search' => false,
    'Text' => false,
    'Article' => false,
    'Checkout' => false,
    'Legend' => false,
    'RightPanels' => true,
    'PromoPanels' => false,
    'Login' => false,
    'EditCategory' => false,
    'EditPictures' => false,
    'EditList' => false,
    'Orders' => false,
    'WebLog' => false,
    'Integrity' => false,
    'StockTake' => false
);



// ----------------------------------------------------------------------------
// Find out what page and which options were requested
// ----------------------------------------------------------------------------

if (!isset($_SERVER['PATH_INFO'])) {
    //if no controls set, clean up some stuff
    CleanNavigationSessions();
} else {
// ----------------------------------------------------------------------------
    //get all vars from the URL
    $ctrl = explode('/', substr($_SERVER['PATH_INFO'], 1));
    $ctrlnum = count($ctrl) - 1;
    $failed = false;
    $pagebuffering = true;

    $PAGEOPT['resetnav'] = false;

    //first var should be the destination page
    $PAGEOPT['targetpage'] = $ctrl[0];

    //what the subsequent vars mean depends on the first
    switch ($PAGEOPT['targetpage']) {

// ----------------------------------------------------------------------------
// Articles
        case 'article':
            if ($ctrlnum < 1) {
                $failed = true;
                break;
            }

            $PAGEOPT['articlename'] = $ctrl[1];

            //Output control
            $PAGEVIS['SubCategories'] = false;
            $PAGEVIS['ItemList'] = false;
            $PAGEVIS['Item'] = false;
            $PAGEVIS['Text'] = true;
            $PAGEVIS['Article'] = true;

            break;


// ----------------------------------------------------------------------------
// Basket
// let the basket take care of this, there's a lot to do...
        case 'basket':
            $PAGEOPT['action'] = $ctrl[1];
            $PAGEOPT['confirm'] = intval($ctrl[2]);
            $PAGEOPT['itemid'] = intval($ctrl[2]);
            $PAGEOPT['quantity'] = intval($ctrl[3]);

            if ($PAGEOPT['action'] == 'view') {
                CleanNavigationSessions();
                if ($_SESSION['BASKET']->itemcount() == 0) {
                    redirect_page($OPT->enigmaroot . $OPT->controlpage);
                }
            }

            //Output control
            $PAGEVIS['SubCategories'] = false;
            $PAGEVIS['ItemList'] = false;
            $PAGEVIS['Item'] = false;
            $PAGEVIS['MiniBasket'] = true;

            if ($PAGEOPT['action'] == 'view') {
                $PAGEVIS['MiniBasket'] = false;
                $PAGEVIS['FullBasket'] = true;
            }

            break;



// ----------------------------------------------------------------------------
// Category
        case 'category':
            //validate
            if (($ctrlnum != 1) && ($ctrlnum != 2) && ($ctrlnum != 3) && ($ctrlnum != 4)) {
                echo("Parse failed for {$PAGEOPT['targetpage']}, parameter count=$ctrlnum<br>");
                $failed = true;
                break;
            }

            $PAGEOPT['maincatid'] = intval($ctrl[1]);
            $PAGEOPT['subcatid'] = intval($ctrl[2]);
            $PAGEOPT['itemlistpage'] = intval($ctrl[3]);
            $PAGEOPT['showcateditbox'] = intval($ctrl[4]);

            //commit some of this stuff to session variables
            unset($_SESSION['itemid']);
            $_SESSION['maincatid'] = $PAGEOPT['maincatid'];
            $_SESSION['subcatid'] = $PAGEOPT['subcatid'];
            $_SESSION['itemlistpage'] = $PAGEOPT['itemlistpage'];
            if ($ctrlnum == 4) {
                if ($PAGEOPT['showcateditbox'] == 1) {
                    $_SESSION['showEditCategoryDetails'] = true;
                } else {
                    $_SESSION['showEditCategoryDetails'] = false;
                }
            }

            //log activity later
            if ($_SESSION['maincatid'] != $PAGEOPT['maincatid'])
                $addlog['maincat'] = true;
            if ($_SESSION['subcatid'] != $PAGEOPT['subcatid'])
                $addlog['subcat'] = true;

            break;

// ----------------------------------------------------------------------------
// Checkout
        case 'checkout':
            //Output control
            $encryptpage = true;
            $PAGEVIS['SubCategories'] = false;
            $PAGEVIS['ItemList'] = false;
            $PAGEVIS['Item'] = false;
            $PAGEVIS['Checkout'] = true;
            break;

// ----------------------------------------------------------------------------
// Toggle Deleted Vis
        case 'deleted':
            $PAGEOPT['deletedvis'] = (boolean) intval($ctrl[1]);
            $_SESSION['deletedvis'] = (boolean) $PAGEOPT['deletedvis'];
            break;



// ----------------------------------------------------------------------------
// Edit List
        case 'edit_list':
            CleanNavigationSessions();
            $PAGEOPT['list'] = $ctrl[1];

            //Output control
            $PAGEVIS['EditList'] = true;

            break;


// ----------------------------------------------------------------------------
// Integrity
        case 'integrity':
            $PAGEVIS['SubCategories'] = false;
            $PAGEVIS['ItemList'] = false;
            $PAGEVIS['Item'] = false;
            $PAGEVIS['Integrity'] = true;
            break;

// ----------------------------------------------------------------------------
// Item
        case 'item':
            //validate
            if (($ctrlnum != 1) && ($ctrlnum != 2) && ($ctrlnum != 3) && ($ctrlnum != 4) && ($ctrlnum != 5)) {
                $failed = true;
                break;
            }

            $PAGEOPT['itemid'] = intval($ctrl[1]);
            $PAGEOPT['resetnav'] = intval($ctrl[2]);
            $PAGEOPT['edittab'] = intval($ctrl[3]);
            $PAGEOPT['edittype'] = intval($ctrl[4]);
            $PAGEOPT['detailid'] = intval($ctrl[5]);

            //commit some of this stuff to session variables
            $_SESSION['itemid'] = $PAGEOPT['itemid'];
            $_SESSION['edititemtab'] = $PAGEOPT['edittab'];
            $_SESSION['edititemtype'] = $PAGEOPT['edittype'];

            //log activity later
            if ($_SESSION['itemid'] != $PAGEOPT['itemid'])
                $addlog['item'] = true;

            break;

// ----------------------------------------------------------------------------
// Login
        case 'login':
            CleanNavigationSessions();

            //Output control
            $encryptpage = true;
            $PAGEVIS['Login'] = true;
            $PAGEVIS['SubCategories'] = false;
            $PAGEVIS['ItemList'] = false;
            $PAGEVIS['Item'] = false;

            break;


// ----------------------------------------------------------------------------
// Order
        case 'order':
            CleanNavigationSessions();
            $PAGEOPT['orderid'] = intval($ctrl[1]);
            $PAGEOPT['action'] = $ctrl[2];
            if ($PAGEOPT['action'] == 'wipecc') {
                $PAGEOPT['confirmed'] = $ctrl[3];
            }

            //Output control
            $encryptpage = true;
            $PAGEVIS['Orders'] = true;
            $PAGEVIS['SubCategories'] = false;
            $PAGEVIS['ItemList'] = false;
            $PAGEVIS['Item'] = false;

            break;

// ----------------------------------------------------------------------------
// Picture
        case 'picture':
            CleanNavigationSessions();

            $PAGEOPT['action'] = $ctrl[1];
            $PAGEOPT['pictureid'] = $ctrl[2];

            if (empty($PAGEOPT['action']))
                $PAGEOPT['action'] = 'list';

            //Output control
            $PAGEVIS['EditPictures'] = true;

            break;

// ----------------------------------------------------------------------------
// Search
        case 'search':
            $PAGEOPT['needle'] = $ctrl[1];

            //Output control
            $PAGEVIS['SubCategories'] = false;
            $PAGEVIS['ItemList'] = false;
            $PAGEVIS['Item'] = false;
            $PAGEVIS['Search'] = true;

            break;


// ----------------------------------------------------------------------------
// Stock Taking
        case 'stocktake':
            CleanNavigationSessions();

            //Output control
            $PAGEVIS['StockTake'] = true;
            break;

// ----------------------------------------------------------------------------
// Text
        case 'text':
            if ($ctrlnum != 1) {
                $failed = true;
                break;
            }

            $PAGEOPT['textfile'] = $ctrl[1];

            //output control
            $PAGEVIS['SubCategories'] = false;
            $PAGEVIS['ItemList'] = false;
            $PAGEVIS['Item'] = false;
            $PAGEVIS['Text'] = true;

            break;


// ----------------------------------------------------------------------------
// Web Log
        case 'weblog':
            CleanNavigationSessions();

            //Output control
            $encryptpage = true;
            $PAGEVIS['WebLog'] = true;
            $PAGEVIS['SubCategories'] = false;
            $PAGEVIS['ItemList'] = false;
            $PAGEVIS['Item'] = false;

            break;


// ----------------------------------------------------------------------------
// Wizard
        case 'wizard':
            $PAGEOPT['wizardname'] = $ctrl[1];
            break;
    }




// ----------------------------------------------------------------------------
// Now process the pages
// ----------------------------------------------------------------------------
    if ($failed) {
        echo("Parameters incorrect!<br>$ctrlnum<br>");
        //$_SESSION = array();
        //session_destroy();
        //redirect_page($OPT->enigmaroot);
    }
}


//set the visibility options into $OPT, as that's what the views can reference
$OPT->showdeletedobjects = $PAGEOPT['deletedvis'];


//the first thing to check for is wether or not we need to calculate the
//correct categories to highlight
if ($PAGEOPT['resetnav']) {
    $rs = view_itemcategories($_SESSION['itemid'], true);
    $r = db_fetch_array($rs);
    $_SESSION['maincatid'] = $r['ParentCategoryID'];
    $_SESSION['subcatid'] = $r['ChildCategoryID'];
}


if (($PAGEOPT['targetpage'] != 'basket') || ($PAGEOPT['action'] == 'view')) {
    $_SESSION['postreloadpage'] = $_SERVER['PHP_SELF'];
}

//if no options set, use default
if (!isset($ctrlnum)) {
    $PAGEVIS['SubCategories'] = false;
    $PAGEVIS['ItemList'] = false;
    $PAGEVIS['Item'] = false;
    $PAGEVIS['Text'] = true;
    $PAGEVIS['PromoPanels'] = true;
    $PAGEVIS['HomePage'] = true;
    //$PAGEOPT['textfile']='main_welcome';
}


if (!isset($_SESSION['maincatid'])) {
    $PAGEVIS['SubCategories'] = false;
}
if (!isset($_SESSION['itemid'])) {
    $PAGEVIS['Item'] = false;
}
if (empty($_SESSION['subcatid'])) {
    $PAGEVIS['ItemList'] = false;
}

if (authorised()) {
    //always encrypt all authorised pages
    $encryptpage = true;
    require_https();

    //show the category editor on all pages where the subcats appear
    $PAGEVIS['EditCategory'] = $PAGEVIS['SubCategories'];
}


//buffer output?
if ($pagebuffering) {
    ob_start();
}


//generic html header
start_page($html, 'head');
end_page($html);


//popup page?
if ($PAGEOPT['targetpage'] == 'item' && $_SESSION['edititemtype'] != 0) {
    if (authorised()) {
        $_SESSION['detailid'] = $_SESSION['itemid'];
        $_SESSION['itemid'] = 0;
        start_page($html, 'edit_item');
        require('edit_item.php');
        end_page($html);
    }
} elseif ($PAGEOPT['targetpage'] == 'wizard') {
    start_page($html, 'wizard_' . $PAGEOPT['wizardname']);
    require_once('wizard_' . $PAGEOPT['wizardname'] . '.php');
    end_page($html);


    //otherwise a "normal" page
} else {

    //these get set if a from needs it's focus set
    $setFocusForm = '';
    $setFocusField = '';


    //top structure of page
    start_page($html, 'top');

    if (($encryptpage) && !($OPT->usessl)) {
        $html->assign('CRITICAL_MESSAGE', 'This page should be using SSL, but this is not configured.  Please update the store config to increase security');
        $html->parse('main.criticalnote');
    }
    if (authorised()) {
        //draw the icons
        $html->assign('TARGET', $OPT->enigmaroot . $OPT->controlpage . '/login');
        $html->assign('ICON', 'logout');
        $html->assign('ALT', 'Logout');
        $html->assign('LABEL', 'Logout');
        $html->parse('main.admintools.icon');
        $html->parse('main.admintools.label');

        $html->assign('TARGET', "javascript:MM_openBrWindow('{$OPT->enigmaroot}{$OPT->controlpage}/wizard/new_item','WizardNewItemPopup','width=800,height=600,resizable=no')");
        $html->assign('ICON', 'newitem');
        $html->assign('ALT', 'New Item');
        $html->assign('LABEL', 'New Item');
        $html->parse('main.admintools.icon');
        $html->parse('main.admintools.label');

        $html->assign('TARGET', $OPT->enigmaroot . $OPT->controlpage . '/picture');
        $html->assign('ICON', 'pictures');
        $html->assign('ALT', 'View Pictures');
        $html->assign('LABEL', 'Pictures');
        $html->parse('main.admintools.icon');
        $html->parse('main.admintools.label');

        $html->assign('TARGET', $OPT->enigmaroot . $OPT->controlpage . '/order');
        $html->assign('ICON', 'orders');
        $html->assign('ALT', 'View Orders');
        $html->assign('LABEL', 'Orders');
        $html->parse('main.admintools.icon');
        $html->parse('main.admintools.label');

        $html->assign('TARGET', $OPT->enigmaroot . $OPT->controlpage . '/edit_list/supplier');
        $html->assign('ICON', 'suppliers');
        $html->assign('ALT', 'View Suppliers');
        $html->assign('LABEL', 'Suppliers');
        $html->parse('main.admintools.icon');
        $html->parse('main.admintools.label');

        $html->assign('TARGET', $OPT->enigmaroot . $OPT->controlpage . '/stocktake');
        $html->assign('ICON', 'stocktake');
        $html->assign('ALT', 'Stock Taking');
        $html->assign('LABEL', 'Stock Take');
        $html->parse('main.admintools.icon');
        $html->parse('main.admintools.label');

        $html->parse('main.loggedin');
        $html->parse('main.admintools1');
        $html->parse('main.admintools');
    } else {
        //pick a banner
        /*
          $banners=array();
          echo("<!-- ".$OPT->bannerroot."-->");
          if ($handle = opendir($OPT->bannerroot)) {
          while (false !== ($file = readdir($handle))) {
          if ($file != "." && $file != "..") {
          $page_name=substr($file, 0, strpos($file, "."));
          echo("<!-- file:" . $file . " name:" . $page_name . "-->");
          $banners[] = array($file, $page_name);
          }
          }
          }
          closedir($handle);
          if(count($banners)>0) {
          $bannerid = rand(0, count($banners)-1);
          list($width, $height, $size, $attributes) = getimagesize($OPT->bannerroot.'/'.$banners[$bannerid][0]);
          $html->assign('BANNER_FILE', $banners[$bannerid][0]);
          $html->assign('BANNER_NAME', $banners[$bannerid][1]);
          $html->assign('BANNER_ATTR', $attributes);
          $html->parse('main.banner');
          }
         */
    }
    //some of the special categories, check that they're not empty
    if (ParentCategoryItemCount(144) > 0)
        $html->parse('main.bulkbuys');
    if (ParentCategoryItemCount(151) > 0)
        $html->parse('main.keyproducts');

    if ($PAGEVIS['MiniSearch'])
        $html->parse('main.minisearch');
    if ($PAGEVIS['Brands'])
        $html->parse('main.brands');
    if ($PAGEVIS['SecureLogo'])
        $html->parse('main.securelogo');

    $html->assign('PAGE_HEADING', 'Koi Logic');
    $html->assign('FOCUS_FORM', '**~FOCUS_FORM~**');
    $html->assign('FOCUS_FIELD', '**~FOCUS_FIELD~**');
    if ($PAGEVIS['SubCategories'])
        $html->assign('PAGE_HEADING', 'Please click a sub-category:');
    if ($PAGEVIS['ItemList'])
        $html->assign('PAGE_HEADING', '&nbsp;');
    if ($PAGEVIS['Search'])
        $html->assign('PAGE_HEADING', 'Search Results');
    if ($PAGEVIS['FullBasket'])
        $html->assign('PAGE_HEADING', 'Checkout');
    if ($PAGEVIS['Item'])
        $html->assign('PAGE_HEADING', '**~PAGE_HEADING~**');
    if ($PAGEVIS['Article'])
        $html->assign('PAGE_HEADING', '**~PAGE_HEADING~**');
    if ($PAGEVIS['Checkout'])
        $html->assign('PAGE_HEADING', 'Checking out...');
    if ($PAGEVIS['WebLog'])
        $html->assign('PAGE_HEADING', 'Web Log');
    if ($PAGEVIS['EditPictures'])
        $html->assign('PAGE_HEADING', 'Pictures');
    if ($PAGEVIS['EditList'])
        $html->assign('PAGE_HEADING', 'Edit List');
    if ($PAGEVIS['StockTake'])
        $html->assign('PAGE_HEADING', 'Stock Taking');

    $html->parse('main.heading');

    //list primary categories
    if ($PAGEVIS['MainCategories']) {
        require('categories1.php');
    }

    //promo panels
    if ($PAGEVIS['PromoPanels']) {
        //require('promopanels.php');
    }

    //end top of page
    end_page($html);

    //list of current sub-categories
    if ($PAGEVIS['SubCategories']) {
        start_page($html, 'subcats');
        require('categories2.php');
        end_page($html);
    }

    //edit category
    if ($PAGEVIS['EditCategory']) {
        start_page($html, 'edit_category');
        require('edit_category.php');
        end_page($html);
    }

    //web log
    if ($PAGEVIS['WebLog'] && authorised()) {
        start_page($html, 'weblog');
        require('weblog.php');
        end_page($html);
    }

    //pictures
    if ($PAGEVIS['EditPictures'] && authorised()) {
        start_page($html, 'picture');
        require('picture.php');
        end_page($html);
    }

    //lists
    if ($PAGEVIS['EditList'] && authorised()) {
        start_page($html, 'edit_list');
        require('edit_list.php');
        end_page($html);
    }

    //orders
    if ($PAGEVIS['Orders'] && authorised()) {
        start_page($html, 'orders');
        require('orders.php');
        end_page($html);
    }

    //checkout form
    if ($PAGEVIS['Checkout']) {
        start_page($html, 'checkout');
        require('checkout.php');
        end_page($html);
    }

    //integrity checker
    if ($PAGEVIS['Integrity']) {
        start_page($html, 'integrity');
        require('integrity.php');
        end_page($html);
    }

    //login form
    if ($PAGEVIS['Login']) {
        start_page($html, 'login');
        require('login.php');
        end_page($html);
    }

    //stock take
    if ($PAGEVIS['StockTake']) {
        start_page($html, 'stocktake');
        require('stocktake.php');
        end_page($html);
    }

    //current item
    if ($PAGEVIS['Item']) {
        if (authorised()) {
            start_page($html, 'edit_item');
            require('edit_item.php');
            end_page($html);
        }

        //variable $dbItemName should be set now, so get the current
        //output buffer, change the page title, clear the buffer and
        //push out the new page contents...
        $buffercontents = ob_get_contents();
        $buffercontents = str_replace('**~PAGE_HEADING~**', $dbItemName, $buffercontents);
        ob_clean();
        echo($buffercontents);
    }

    //list of items in current category
    if ($PAGEVIS['ItemList']) {
        start_page($html, 'itemlist');
        require('itemlist.php');
        end_page($html);
    }

    //show the search results or form
    if ($PAGEVIS['Search']) {
        start_page($html, 'search');
        require('search.php');
        end_page($html);
    }

    //show the complete basket
    if ($PAGEVIS['FullBasket']) {
        start_page($html, 'basket');
        require('basket.php');
        end_page($html);
    }

    //show the default home page
    if ($PAGEVIS['HomePage']) {
        start_page($html, 'main_welcome', ENIGMA_TEXT);
        $html->assign('WELCOME_TEXT', mostext(0));
        end_page($html);
    }

    //text
    if ($PAGEVIS['Text']) {
        start_page($html, $PAGEOPT['textfile'], ENIGMA_TEXT);
        end_page($html);
        spLogToHistory('text', 0, $PAGEOPT['textfile'], "Viewed text \"$textfile\"");
    }

    //articles
    if ($PAGEVIS['Article']) {
        require('articles/' . $PAGEOPT['articlename'] . '.php');
        spLogToHistory('article', 0, $PAGEOPT['articlename'], "Viewed article \"$articlefile\"");

        //variable $pagetitle should be set now, so get the current
        //output buffer, change the page title, clear the buffer and
        //push out the new page contents...
        $buffercontents = ob_get_contents();
        $buffercontents = str_replace('**~PAGE_HEADING~**', $pagetitle, $buffercontents);
        ob_clean();
        echo($buffercontents);
    }


    //bottom structure of page
    start_page($html, 'bottom');
    if ($PAGEVIS['MiniBasket'])
        require('basket.php');
    if ($PAGEVIS['KLFilterBox'])
        $html->parse('main.klfilter');
    //if($PAGEVIS['FeaturedItems'])	require('featured.php');
    //if($PAGEVIS['Specials'])		require('special.php');
    //if($PAGEVIS['Christmas'])		require('christmas.php');
    if ($PAGEVIS['RightPanels'])
        require('rightpanels.php');

    if (isset($legendpostagecharge)) {
        $PAGEVIS['Legend'] = true;
        foreach ($legendpostagecharge as $key => $value) {
            $html->assign('POSTAGECHARGE_IMG', $key);
            $html->assign('POSTAGECHARGE_ID', $key);
            $html->assign('POSTAGECHARGE_NAME', $value);
            $html->parse('main.legend.postagecharge');
        }
    }
    if ($PAGEVIS['Legend'])
        $html->parse('main.legend');
    end_page($html);

    //generic html footer
    start_page($html, 'footer');
    end_page($html);


    //special case for completed orders, if we're at the order confirmation
    //screen, save it to the database for support reasons
    if (($PAGEOPT['targetpage'] == 'basket') && (isset($_SESSION['userorderid']))) {
        spInsertOrderConfirmation($_SESSION['userorderid'], ob_get_contents());
    }


    //end popup page if
}


//put in the javascript to set form focus
$buffercontents = ob_get_contents();
if ((!empty($setFocusForm)) && (!empty($setFocusForm))) {
    $focuscmd = " onLoad=\"SetFocus('$setFocusForm','$setFocusField');\"";
} else {
    $focuscmd = '';
}
$buffercontents = str_replace('**~FOCUS_FORM~**', $focuscmd, $buffercontents);
ob_clean();
echo($buffercontents);


//clean out the keywords placeholder
$buffercontents = ob_get_contents();
$keywords = '';
if (!empty($OPT->extra_keywords)) {
    $keyarray = array_merge($OPT->extra_keywords, explode(',', $OPT->default_keywords));
} else {
    $keyarray = explode(',', $OPT->default_keywords);
}

sort($keyarray);
$lastword = '';
foreach ($keyarray as $keyword) {
    if ($keyword != $lastword) {
        $keywords .= $keyword . ',';
    }
    $lastword = $keyword;
}

$buffercontents = str_replace('**~SITE_KEYWORDS~**', $keywords, $buffercontents);
ob_clean();
echo($buffercontents);


//buffering output?
if ($pagebuffering) {
    ob_end_flush();
}

//	session_write_close();
// 	echo("<table>");
// 	echo("<tr>");
// 	echo("<td>".fancy_vardump($PAGEVIS)."</td>");
// 	echo("<td>".fancy_vardump($_SESSION)."</td>");
// 	echo("</tr>");
// 	echo("<table>");
//	echo("Vis is " . $PAGEOPT['deletedvis']."<br>");
?>
