<?php
	$debugging=false;

	require_once('application.php');

	//require_once('config/config.php');

	$productid = $_GET['id'];

	$imagepath = '/img/products/';//$OPT->productimageroot;
	//echo("stuff is $imagepath".getPictureFromStockCode($productid));

	if (!$debugging) header("Content-type: image/png");
	//if($debugging) echo("Image: $imagepath".getPictureFromStockCode($productid) );
	$im = imagecreatefrompng($imagepath.getPictureFromStockCode($productid));
	imagepng($im);
	imagedestroy($im);

	//imagepng($im);
	//imagedestroy($im);
?>
