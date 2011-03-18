<?php
	$debugging=0;

	$MainFGsel="#ffffff";
	$MainBGsel="#9999ff";

	$BGin = $MainBGsel;
	$FGin = $MainFGsel;

	if ($debugging) echo ("BGin: $BGin FGin: $FGin<br/>");

	$w=16;
	$h=15;
	$j=2;

	//create the image
	$im = imagecreate($w,$h);

	//background colours
	$rBG = hexdec(substr($BGin, 1,2));
	$gBG = hexdec(substr($BGin, 3,2));
	$bBG = hexdec(substr($BGin, 5,2));
	$bg = imagecolorallocate($im, $rBG,$gBG,$bBG);
	if ($debugging) echo ("bg: $rBG,$gBG,$bBG<br/>");

	//foreground colours
	$rFG = hexdec(substr($FGin, 1,2));
	$gFG = hexdec(substr($FGin, 3,2));
	$bFG = hexdec(substr($FGin, 5,2));
	$fg = imagecolorallocate($im, $rFG,$gFG,$bFG);
	if ($debugging) echo ("fg: $rFG,$gFG,$bFG<br/>");

	//anti-aliased colours
	$rAA = floor($rBG + abs($rBG-$rFG)*0.5);
	$gAA = floor($gBG + abs($gBG-$gFG)*0.5);
	$bAA = floor($bBG + abs($bBG-$bFG)*0.5);
	$aa = imagecolorallocate($im, $rAA,$gAA,$bAA);
	if ($debugging) echo ("aa: $rAA,$gAA,$bAA<br/>");

	//the aa triangle
	$points[0] = $j;	   $points[1] = $j;
	$points[2] = $w-$j;	$points[3] = $j;
	$points[4] = $w*0.5;	$points[5] = $h-$j;
	imagefilledpolygon($im, $points, 3, $aa);

	//the main triangle
	$j = $j*1.5;
	$points[0] = $j;	   $points[1] = $j*0.9;
	$points[2] = $w-$j;	$points[3] = $j*0.9;
	$points[4] = $w*0.5;	$points[5] = $h-$j;
	imagefilledpolygon($im, $points, 3, $fg);

	if (!$debugging) header("Content-type: image/png");
	imagepng($im);
	imagedestroy($im);
?>
