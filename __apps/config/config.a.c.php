<?php #//php_start\\;
	
	
	class _CONFIG
	{
		/**
		* Enable database here if you want to use
		* built in database classes.
		* To enable just set the value to True or like below:
		*
		* $enable_database = true;
		*
		* else just remain it as it is right now or as default
		*/
		public $enable_database = true;
		
		/**
		* If you have to execute the maitenance just changed it
		* to "off" or if you wish to execute maintenance while the
		* system is live, just remain it to 'on'.
		*
		* Note: once you set the system status to off you
		* will not be able to access any pages from your 
		* public_html or root folder. You need to create a 
		* sub folder and copy your development index.php 
		* into it. Ex. http://your_domain_name.com/_development
		*/
		public $system = array(
			/**
			* @status_on - to put the current status of the system 
			* -- NOTE: You need to uncomment the following parameters if you set the `status_on` to 'false'
			*
			* TRUE = meaning the system is active
			* FALSE = meaning the system is offline or inactive
			*/
			'status_on' => false,
			
			/**
			* @response_type - is for the information to appear on the screen if you set the system status to false
			*/ 
			//'response_type' => 'file',
			
			/**
			* Uncomment below if you set the `response_type` to 'file', this means
			* you selected your customize information to appear on the screen
			* when your system is offline
			*/
			//'file_location' => '/_status.php',
			
			/**
			* Uncomment below if you set the `response_type` to 'text', this means
			* you selected your customize information to appear on the screen
			* when your system is offline
			*
			* Otherwise: the system will only show blank screen
			*/
			//'message' => 'Our system is currently in maintenance, will get it back soon. Thank you!',
		);
	}

#//php_end\\;?>


