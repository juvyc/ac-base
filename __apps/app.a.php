<?php #//php_start\\;	
	
	/**
	* Starting session
	*/
	session_start();
	
	define('base_url', $ACB_GLOBALS['path']['base_root']);
	
	define('base_dir', $ACB_GLOBALS['path']['base_dir']);
	
	require_once $ACB_GLOBALS['path']['apps_path'] . '/config/config.a.c.php';
	require_once $ACB_GLOBALS['path']['apps_path'] . '/config/maintenance.a.c.php';
	
	/**
	* Call the cores loader
	*/
	require_once $ACB_GLOBALS['path']['cores_path'] . '/loader.c.php';
	require_once $ACB_GLOBALS['path']['cores_path'] . '/base.c/base.c.php';
	
	$ACB_GLOBALS['system'] = new _CORE();
	print($ACB_GLOBALS['system']->exec_app());
	
#//php_end\\;?>