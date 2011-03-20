<?php

/* ----------------------------------------------------------------
  Filename: itemlist.php

  Requirements: $_SESSION['maincatid']
  $_SESSION['subcatid']

  New Sessions: $_SESSION['itemlistpage']
  $_SESSION['itemlistmax']

  Notes: This page retrieves a list of items from the
  database and outputs a list
  ------------------------------------------------------------------- */
define('LIST_TYPE_COMPACT', 0);
define('LIST_TYPE_THUMBS', 1);
define('LIST_TYPE_COMPLETE', 2);

$html->assign("MAINCAT_ID", $_SESSION['maincatid']);
$html->assign("SUBCAT_ID", $_SESSION['subcatid']);


$pg = 1;
$max = 0;

//get the current page
if (!empty($_SESSION['itemlistpage'])) {
    $pg = $_SESSION['itemlistpage'];
}
//echo("showing page $pg<br>");
//only calculate the max if we need to
if (!isset($_SESSION['itemlistmax'])) {
    $rsitemlist = view_items($_SESSION['subcatid']);
    $max = db_num_rows($rsitemlist);
}

$pages = ceil($max / $OPT->itempagesize);
$rsitemlist = view_items($_SESSION['subcatid'], '', $OPT->itempagesize, $pg);


//get the rows of items
$ItemsPerRow = 3;
$ItemNum = 0;
$RowNum = 1;
$ProductKeywords = array();

while ($r = db_fetch_array($rsitemlist)) {
    $dbItemID = $r['ItemID'];
    $dbItemName = $r['ItemName'];
    $dbLowPrice = format_currency($r['LowPrice'], '&nbsp;');
    $dbRecommended = $r['IsRecommended'];
    $dbDeleted = (boolean) ($r['ItemDeleteDate'] != '0000-00-00 00:00:00');
    $dbImageFileName = $r['Filename'];
    $dbListType = $r['ListType'];

    // Add each word of the name to the keywords
    $words = explode(' ', $dbItemName);
    foreach ($words as $key => $value) {
        $ProductKeywords[$value] = $value;
    }

    $html->assign("CLASS", '');
    $html->assign("ITEM_ID", $dbItemID);
    $html->assign("ITEM_NAME", $dbItemName);
    $html->assign("ITEM_PRICE", $dbLowPrice);

    $html->assign("PAGE_ANCHOR", authorised() ? '#1' : '');
    $html->assign("IMAGE", img_safe($OPT->productimageroot . $dbImageFileName, $dbItemName));

    if (authorised()) {
        $dbListType = LIST_TYPE_COMPACT;
    } else {
        $dbListType = LIST_TYPE_COMPLETE;
    }
    switch ($dbListType) {
        case(LIST_TYPE_COMPACT):
            if (($r['DetailCount'] == 0) || ($r['WebView'] == 0))
                $html->assign("CLASS", 'unavailable');
            if ($dbDeleted)
                $html->assign("CLASS", 'deleted');
            if ($dbIsRecommended != 0)
                $html->parse("main.compact.row.recommended");
            $html->parse("main.compact.row");
            break;

        case(LIST_TYPE_COMPLETE):
            $_SESSION['itemid'] = $dbItemID;
            $html_hierarchy = 'main.complete.';
            require('item.php');
            $html->parse("main.complete");
            break;

        default:

            $html->parse("main.expanded.row.cell");

            //an end of row, if this is the last cell in a row
            if ($ItemNum == ($ItemsPerRow - 1)) {
                $ItemNum = -1;
                $html->parse("main.expanded.row");
            }
            $ItemNum++;
            break;
    }
}

$OPT->extra_keywords = $ProductKeywords;

switch ($dbListType) {
    case(LIST_TYPE_COMPACT):
        if (authorised()) {
            $html->assign("CLASS", '');
            $html->assign("ITEM_ID", 0);
            $html->assign("ITEM_NAME", 'Add New Item');
            $html->assign("ITEM_PRICE", '&nbsp;');
            //$html->parse("main.compact.row");
            $html->parse("main.compact.newlink");
        }
        $html->parse("main.compact");

        break;
    case(LIST_TYPE_COMPLETE):
        break;
    default:
        //chances are that we need to put in some blank category cells and close the row
        if ($ItemNum != 0) {
            $ColumnsToSpan = (($ItemsPerRow - $ItemNum) * 2);
            $html->assign("COLSPAN", $ColumnsToSpan);
            $html->parse("main.expanded.row.span");
            $html->parse("main.expanded.row");
        }

        if (authorised()) {
            $html->assign("CLASS", '');
            $html->assign("ITEM_ID", 0);
            $html->assign("ITEM_NAME", 'Add New Item');
            $html->assign("ITEM_PRICE", '&nbsp;');
            $html->parse("main.expanded.newlink");
        }

        $html->parse("main.expanded");
        break;
}

//set the page x of y stuff
$startitem = ($pg - 1) * $OPT->itempagesize + 1;
$enditem = min($startitem - 1 + $OPT->itempagesize, $max);

$html->assign("START_ITEM", $startitem);
$html->assign("END_ITEM", $enditem);
$html->assign("TOTAL_ITEMS", $max);

//using next/previous instead of page 1.2.3.4
if ($pages > 1) {
    if ($pg > 1) {
        $html->assign("PREVIOUS_PAGE", $pg - 1);
        $html->parse("main.prevlink");
    }
    if ($pg < $pages) {
        $html->assign("NEXT_PAGE", $pg + 1);
        $html->parse("main.nextlink");
    }
}

//$html->parse("main");
?>
