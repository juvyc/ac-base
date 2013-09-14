<?php #//php_start\\;
	
	/**
	* AC-Base Frameworks always starts at your index.php as recognizer of our
	* core. Using of this framework you must always follow the configuration
	* process to make it working properly and avoid any intruders (except server issue).
	*
	* Below is the complete configuration in order to activate this framework in your
	* system.
	*/
	
	/**
	* Loading the core (always remember that folders structure should 
	* the same, maining you put the core wherever directories but folders 
	* structures inside that base directory should the same)
	*/
	
	$GLOBALS = Array();
	
	/**
	* Set your base directory where your website started
	*
	* Ex. $GLOBALS['path']['base_dir'] = '/public_html';
	* or better to assign value to realpath for dynamic detection like below
	* $GLOBALS['path']['base_dir'] = realpath(__DIR__);
	*
	* NOTE: You need to change realpath(__DIR__) to static if you are using below of 5.4 PHP version.
	* The static base path is like '/home/var/public_html'
	*/
	
	$GLOBALS['path']['base_dir'] = realpath(__DIR__);
	
	/**
	* @base_root - this is the base location where you put your website
	* NOTE: Donot add the full url of you site just directory name like this
	* Ex. /my/website/
	*
	*
	* But if your location is the main root just put like
	* Ex. '/'
	*/
	
	$REQUEST_URI = explode('?', $_SERVER['REQUEST_URI']);
	$REQUEST_URI = explode('/', $REQUEST_URI[0]);
	$base_dir = explode('/', str_replace("\\", "/",$GLOBALS['path']['base_dir']));
	$base_root = array();
	$tracker = false;
	foreach($REQUEST_URI as $buri){
		if($buri == $base_dir[count($base_dir) - 1]){
			$base_root[] = $buri . '/';
			$tracker = true;
			break;
		}else{
			$base_root[] = $buri;
			continue;
		}
	}
	
	if($tracker){
		$GLOBALS['path']['base_root'] = implode('/',$base_root);
	}else{
		$GLOBALS['path']['base_root'] = '/';
	}
	
	/**
	* Forlder Aliasis -- is to allow developers to create thier own folder names for
	* apps, files, cores and themes. To do this just replace the value of the variables
	* below.
	*/
	
	/**
	* configuration, customs classes and other private files storage
	*/
	$GLOBALS['path']['apps_path'] = $GLOBALS['path']['base_dir'] . '/__apps';
	
	/**
	* Files Uploads storage
	*/
	$GLOBALS['path']['files_path'] = $GLOBALS['path']['base_dir'] . '/__files';
	
	/**
	* Storage of all functionality of ac-base framework
	*/
	$GLOBALS['path']['cores_path'] = $GLOBALS['path']['base_dir'] . '/__cores';
	
	/**
	* Storage of the layouts or files that generates the user interface
	*/
	$GLOBALS['path']['themes_path'] = $GLOBALS['path']['base_dir'] . '/__themes';
	
	
	/**
	* Call the app.a.php under the __apps folder where the framework core
	* are integrated
	*/
	
	require_once($GLOBALS['path']['apps_path'] . '/app.a.php');
		

#//php_end\\;?>