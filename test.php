<?php
//phpinfo();die();

$to      = 'mark@hanfordonline.co.uk';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: Koi Logic Sales <sales@koilogic.co.uk>' . "\r\n" .
    'Reply-To: sales@koilogic.co.uk' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$result = mail($to, $subject, $message, $headers);
var_dump($result);

die();
require 'application.php';

/*
function html_to_text($htmlin) {
	$lookfor 		= array('<p>', '</p>', '<br>', '<br />');
	$replacewith	= array("\n",  "\n",   "\n",   "\n");

	$textversion = $htmlin;
	$textversion = str_replace($lookfor, $replacewith, $textversion);
	$textversion = html_entity_decode($textversion);
	
	return $textversion;
}

echo(html_to_text('<p>hello there<br>how is<br />it going?</p>'));
*/

	$_SESSION['DEBUG'] = false;
  $dt = '2008-07-25 12:10';
	$i = 0;
	
	echo("Time order placed: ".date('d-m-Y H:i l', strtotime($dt))."<br>");
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	echo(date('d-m-Y l', $_SESSION["BASKET"]->getNextDeliveryDate(strtotime($dt),$i++)).'<br>');
	
// 	echo('<hr>');
// 	test('2005-05-25 10:00');
// 	echo('<hr>');
// 	test('2005-05-26 10:00');
// 	echo('<hr>');
// 	test('2005-05-27 10:00');
// 	echo('<hr>');
// 	test('2005-05-28 10:00');
// 	echo('<hr>');
// 	test('2005-05-29 10:00');
// 	echo('<hr>');
// 	test('2005-05-30 10:00');
// 	echo('<hr>');
// 	test('2005-05-31 10:00');
// 	echo('<hr>');
// 	test('2005-05-1 10:00');
// 	echo('<hr>');
// 	test('2005-05-2 10:00');
// 	echo('<hr>');
// 	test('2005-05-3 10:00');
// 	echo('<hr>');
// 	test('2005-05-4 10:00');
// 	echo('<hr>');
// 	test('2005-05-5 10:00');
// 	echo('<hr>');
// 	test('2005-05-6 10:00');
// 	echo('<hr>');
// 	test('2005-05-7 10:00');
// 	echo('<hr>');


?>