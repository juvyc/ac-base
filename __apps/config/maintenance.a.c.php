<?php #//php_start\\;
	
	
	/**
	* Errors settings, you can overwride 
	* this if you know how it works
	*/
	error_reporting(E_ALL);
	ini_set('log_errors','1');
	ini_set('display_errors','1');
	ini_set('error_log', $GLOBALS['path']['files_path'] . '/logs/errors.log');
	
	
	/**
	* Setting for system error maintenance varaiables
	* Filling up the ADMIN_EMAIL_ADDR system will automatically
	* send you if any errors occur on time of loading
	* but it's depend upon your settings above.
	*/
	
	$_error_parameters = array(
		'ADMIN_EMAIL_ADDR' => '',
		'GENERIC_ERR_PAGE' => base_url . '_error_.php',
	);

#//php_end\\;?>