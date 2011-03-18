<?php
	/* ----------------------------------------------------------------
		    Filename: edit_category.php

		Requirements:

		       Notes:
	------------------------------------------------------------------- */


	//default visibility of the edit category box
 	if(!isset($_SESSION['showEditCategoryDetails'])) {
		$_SESSION['showEditCategoryDetails']=false;
	}

	$showedit=$_SESSION['showEditCategoryDetails'];
	if($_SESSION['maincatid']==0) $showedit=true;
	if(
		   (!is_null($_SESSION['subcatid']))
		&& ($_SESSION['subcatid']==0)) $showedit=true;

	//form handling
	if(httppost_string('hidFormSubmitted')=='edit_category') {
		$subCategoryID   = httppost_int('hidCategoryID');
		$subParentID     = httppost_int('selParentID');
		$subCategoryName = httppost_string('txtCategoryName');
		$subDescription  = httppost_string('txtDescription');
		$subWebView      = httppost_string('chkWebView');

		if(isset($_POST['imgAdd_x'])) {
			spInsertCategory($subCategoryName, $subDescription, $subParentID, $subWebView);
		}
		if(isset($_POST['imgSave_x'])) {
			spUpdateCategory($subCategoryID, $subCategoryName, $subDescription, $subParentID, $subWebView);
		}
		if(isset($_POST['imgUnDelete_x'])) {
			spUnDeleteCategory($subCategoryID);
		}
		if(isset($_POST['imgDelete_x'])) {
			spDeleteCategory($subCategoryID);
		}
		reload_page();

	}



	//only do any of this stuff if the box is even visible
	if($showedit) {

		if($_SESSION['maincatid']==0) {
			//add a new main category
			$html->assign('CATEGORY_TITLE', 'Adding Category');
			$html->assign('CATEGORY_SELECTOR', draw_selectcategory('selParentID',0));
			$html->assign('WEB_VIEW', ' checked="checked"');
			$html->parse('main.show.cmdadd');

		} elseif ($_SESSION['subcatid']==-1) {
			//add a new sub category
			$html->assign('CATEGORY_TITLE', 'Adding Category');
			$html->assign('CATEGORY_SELECTOR', draw_selectcategory('selParentID',$_SESSION['maincatid']));
			$html->assign('WEB_VIEW', ' checked="checked"');
			$html->parse('main.show.cmdadd');

		} else {
			//edit the category


			if(!empty($_SESSION['subcatid'])) {
				$rs=view_categorydetails($_SESSION['subcatid']);
			} else {
				$rs=view_categorydetails($_SESSION['maincatid']);
			}
			if(db_num_rows($rs)>0) {

				$r=db_fetch_array($rs);
				$dbCategoryID  =$r['CategoryID'];
				$dbCategoryName=$r['CategoryName'];
				$dbDescription =$r['Description'];
				$dbWebView     =$r['WebView'];
				$dbParentID    =$r['ParentID'];
				$dbDeleted     =(boolean)($r['DeleteDate']!='0000-00-00 00:00:00');

				$html->assign('CATEGORY_TITLE', "Editing Category \"$dbCategoryName\"");
				$html->assign('CATEGORY_SELECTOR', draw_selectcategory('selParentID',$dbParentID));
				$html->assign('CATEGORY_ID', $dbCategoryID);
				$html->assign('CATEGORY_NAME', $dbCategoryName);
				$html->assign('DESCRIPTION', $dbDescription);
				$html->assign('WEB_VIEW', $dbWebView ? ' checked="checked"' : '');
				$html->parse('main.show.cmdsave');
				if($dbDeleted) $html->parse('main.show.cmdundelete');
				else $html->parse('main.show.cmddelete');

			}

		}

		$html->assign('MAINCAT_ID', isset($_SESSION['maincatid']) ? $_SESSION['maincatid'] : 0);
		$html->assign('SUBCAT_ID', isset($_SESSION['subcatid']) ? $_SESSION['subcatid'] : 0);
		$html->assign('DIVCLASS', '');
		if($dbDeleted) $html->assign('DIVCLASS', 'deleted');
		$html->parse('main.show.normal_edit_mode');
		$html->parse('main.show');

	} else {
		$html->assign('MAINCAT_ID', isset($_SESSION['maincatid']) ? $_SESSION['maincatid'] : 0);
		$html->assign('SUBCAT_ID', isset($_SESSION['subcatid']) ? $_SESSION['subcatid'] : 0);
		$html->parse('main.hide');
	}

?>
