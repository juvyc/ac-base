<?php
	
	return array(
		/**
		* Put the common js and css files you want to load in all pages
		*
		* Note: to access this in your theme, it start first with '$assets_' so for the common js files
		*		you can acess as '$assets_js' the same in css
		*/
		'common_assets' => array(
			'css' => array(
				'__assets/css/bootstrap.min.css',
				'__assets/css/main.css',
			),
			'js' => array(
				'__assets/js/bootstrap.min.js',
			),
		),
		
		'common_tpl' => array(
			'header', 'layout', 'footer',
		),

		'static_data' => array(
			'base_url' => $ACB_GLOBALS['path']['base_root'],
			'title' => 'Awonsa Content Management System',
		),
	);