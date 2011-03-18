<?php
	/* ----------------------------------------------------------------
		    Filename:

		Requirements:

		       Notes:
	------------------------------------------------------------------- */

	require_https();


	// -----------------------------------
	// Handle Form
	// -----------------------------------
	if(!empty($_POST)) {
		$subname          = $_POST['txtName'];
		$subcaption       = $_POST['txtCaption'];
		$subdescription   = $_POST['txtDescription'];
		$tempfile         = $_FILES['fileNew']['tmp_name'];
		$originalfilename = $_FILES['fileNew']['name'];

		//get file extention
		$fileextention = strstr($originalfilename, '.');
		if($fileextention===false) {
			$fileextention='';
		}

		//get file properties
		$imgprops = getimagesize($tempfile);

		//add picture to database
		$newpictureid = spInsertPicture($subname, $originalfilename, $subdescription, $imgprops);

		//move temporary file to new location
		$newfile  = $_SERVER['DOCUMENT_ROOT'] . $OPT->productimageroot;
		$newfile .= str_pad($newpictureid, $OPT->pictureidlength, '0', STR_PAD_LEFT) . $fileextention;
		if(@move_uploaded_file($tempfile, $newfile)) {
			echo ("picture ok<br>");
			reload_page();

		} else {
			//permanently delete the record
			spDeletePicture($newpictureid, true);
			echo ("picture not moved!<br>");
		}
	}


	// -----------------------------------
	// Display
	// -----------------------------------

	switch ($picturetask) {
		case 'add':
			$html->parse('main.add');
			$setFocusForm='frmAdd';
			$setFocusField='fileNew';
		break;

		default:
			$html->parse('main.icons');
		break;
	}


	$rs=view_pictures();
	while($r=db_fetch_array($rs)) {
		$dbPictureName=$r['PictureName'];
		$dbFileName=$r['FileName'];

		$html->assign('PICTURE_NAME', $dbPictureName);
		$html->assign('FILENAME', $dbFileName);
		$html->parse('main.row');
	}



?>