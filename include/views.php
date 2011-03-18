<?php
//set some database related constants, so that we don't have to keep doing db
//lookups for a few fixed records.
	define('EN_LANDSCAPE', 0);
	define('EN_PORTRAIT', 1);
	define('EN_SQUARE', 2);

//put some of these in arrays to facilitate easier manipulation
	$ENIGMA_ORIENT=array(
		EN_LANDSCAPE=>'Landscape',
		EN_PORTRAIT=>'Portrait',
		EN_SQUARE=>'Square'
	);

//I've split the views into sepparate files for clarity
	require('view/views.php');
	require('view/item.php');
?>