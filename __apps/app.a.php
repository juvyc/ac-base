<?php #//php_start\\;	
	
	/**
	* Starting session
	*/
	session_start();
	
	define('base_url', $GLOBALS['path']['base_root']);
	
	define('base_dir', $GLOBALS['path']['base_dir']);
	
	require_once $GLOBALS['path']['apps_path'] . '/config/config.a.c.php';
	require_once $GLOBALS['path']['apps_path'] . '/config/maintenance.a.c.php';
	require_once $GLOBALS['path']['apps_path'] . '/config/connection.a.c.php';
	
	/**
	* Call the cores loader
	*/
	require_once $GLOBALS['path']['cores_path'] . '/loader.c.php';
	require_once $GLOBALS['path']['cores_path'] . '/base.c/base.c.php';
	
	$GLOBALS['system'] = new _CORE();
	
	echo $GLOBALS['system']->exec_app();
	
#//php_end\\;?>