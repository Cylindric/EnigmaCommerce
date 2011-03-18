<?php

	require_once('application.php');

	if(!authorised()) die('Need to log in');

	echo ("<h1>Fix Pictures.php</h1>");


	$sql = "SELECT * FROM {$dbprefix}picture";
	$rs = db_query($sql);
	while($r = db_fetch_array($rs)) {

		$pid = $r['PictureID'];
		$path = $_SERVER['DOCUMENT_ROOT'].$OPT->productimageroot;
		$file = $r['FileName'];

		switch($_GET['task']) {

			//copy files from the location in FileName to a generated name
			case 1:
				clearstatcache();
				$tofile   = str_pad($r['PictureID'],6,'0', STR_PAD_LEFT) . '.png';
				echo ("copying $path$file to $path$tofile");
				if(copy($path . $file, $path . $tofile)) {
					echo("ok");
					$sql = "UPDATE {$dbprefix}picture SET FileName='$tofile' WHERE PictureID=$pid";
					db_query($sql);
				}
				echo("<br>");
			break;


			//update image dimensions
			case 2:
				clearstatcache();
				echo ("updating details for $path$file");
				$imgprops = getimagesize($path.$file);
				if($imgprops!==false) {
					$w = $imgprops[0];
					$h = $imgprops[1];

					$sql  = "UPDATE {$dbprefix}picture ";
					$sql .= "SET Width=$w, ";
					$sql .= "Height=$h ";
					$sql .= "WHERE PictureID=$pid ";
					db_query($sql);
				}
				echo ("<br>");
			break;


			//delete records with missing files
			case 3:
				clearstatcache();
				echo ("Looking for file $path$file");
				if(!file_exists($path.$file)) {
					spDeletePicture($pid);
					echo (" deleted");
				}
				echo ("<br>");
			break;

		}


	}

?>