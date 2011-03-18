<?php

	// INSTALL
	$sqlfile = $_SERVER['DOCUMENT_ROOT'].'/doc/mysql/create-tables.sql';

	//echo($_SERVER['DOCUMENT_ROOT'].'doc/mysql/create-tables.sql');
	$fd = fopen($sqlfile,'rb');
	$qry = fread($fd, filesize($sqlfile));
	fclose($fd);
?>