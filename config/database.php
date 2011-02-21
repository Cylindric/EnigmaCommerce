<?php

class DATABASE_CONFIG {

	var $default = array(
		'driver' => 'mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'enigma3',
		'password' => 'enigma3',
		'database' => 'enigma3',
		'prefix' => '',
	);

	var $test = array(
		'driver' => 'mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'enigma3',
		'password' => 'enigma3',
		'database' => 'enigma3test',
		'prefix' => '',
	);

	var $upgrade = array(
		'driver' => 'mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'enigma2',
		'password' => 'enigma2',
		'database' => 'enigma',
		'prefix' => 'store_',
	);

}