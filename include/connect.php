<?php

	if(authorised()) {
		db_connect($OPT->storedatabasehost, $OPT->storedatabasename, $OPT->storedatabaseuser, $OPT->storedatabasepass);
	} else {
		db_connect($OPT->storedatabasehost, $OPT->storedatabasename, $OPT->admindatabaseuser, $OPT->admindatabasepass);
	}

?>