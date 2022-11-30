<?php #//php_start\\;
	
	class Base_Model{
	
		private $ACB_GLOBALS;
		private $app_mod;
		
		public function __construct($app_mod = "")
		{
			global $ACB_GLOBALS;
			$this->globals = $ACB_GLOBALS;
			$this->app_mod = $app_mod;
		}
		
		
		/**
		* Apps module loader
		*/
		
		public function load($on = '')
		{
			/**
			* @on parameter, this will use to target 
				specific file to call under a certain model
			*/ 
			if($on != "" && $this->app_mod !=""){
				require_once($this->globals['path']['apps_path'] . '/builder/' . $this->app_mod . '/model/' . $on .'_mod.php');
				$get_a_class = $on . '_mod';
				return new $get_a_class();
			}else if($this->app_mod !=""){
				require_once($this->globals['path']['apps_path'] . '/builder/' . $this->app_mod . '/model/' . $this->app_mod .'_mod.php');
				$get_a_class = $this->app_mod . '_mod';
				return new $get_a_class();
			}
		}
		
		public function forge($on = '')
		{	
			/**
			* @on parameter, this will use to target 
				specific file to call under a certain model
			*/ 
			if($on != "" && $this->app_mod !=""){
				include_once($this->globals['path']['apps_path'] . '/builder/' . $this->app_mod . '/model/' . $on .'_mod.php');
				$get_a_class = $on . '_mod';
			}else if($this->app_mod !=""){
				include_once($this->globals['path']['apps_path'] . '/builder/' . $this->app_mod . '/model/' . $this->app_mod .'_mod.php');
				$get_a_class = $this->app_mod . '_mod';
			}
			
			//Check if @Base_Model_Forge is already running and if not yet then call the class file
			if(!class_exists('Base_Model_Forge'))
				require_once('base.c.mod.forge.php');
			
			return new Base_Model_Forge($get_a_class);
		}
		
		public function get_data($on = '')
		{	
			/**
			* @on parameter, this will use to target 
				specific file to call under a certain model
			*/ 
			if($on != "" && $this->app_mod !=""){
				include_once($this->globals['path']['apps_path'] . '/builder/' . $this->app_mod . '/model/' . $on .'_mod.php');
				$get_a_class = $on . '_mod';
			}else if($this->app_mod !=""){
				include_once($this->globals['path']['apps_path'] . '/builder/' . $this->app_mod . '/model/' . $this->app_mod .'_mod.php');
				$get_a_class = $this->app_mod . '_mod';
			}
			
			//Check if @Base_Model_Forge is already running and if not yet then call the class file
			if(!class_exists('Base_Model_Data'))
				require_once('base.c.mod.data.php');
			
			return new Base_Model_Data($get_a_class);
		}
	}

#//php_end\\;?>