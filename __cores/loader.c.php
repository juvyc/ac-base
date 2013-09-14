<?php #//php_start\\;	


	/**
	* This is the cores loader file, it loads all important files of this framework
	* to be able to function this framework in every systems where it is integrated.
	*/

	
	/**
	* Loading the error.c.php which all errors controller functionality 
	* is stored.
	*/
	require_once 'error.c/error.c.php';
	
	require_once 'uri.c/uri.c.php';
	
	require_once 'builder.c/builder.c.php';
	
	require_once 'autoload.c/autoload.c.php';
	
	require_once 'system.c/system.c.php';
	
	require_once 'core.c/core.c.php';
	

#//php_end\\;?>