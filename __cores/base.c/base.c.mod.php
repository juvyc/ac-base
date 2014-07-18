<?php #//php_start\\;
	
	/**
	* This the model controller and manager
	* 
	*/
	
	
	class Base_Model{
		var $globals;
		var $app_mod;
		public function __construct($app_mod = "")
		{
			global $GLOBALS;
			$this->globals = $GLOBALS;
			$this->app_mod = $app_mod;
		}
		
		
		/**
		* Apps module loader
		*/
		
		public function load($on = '')
		{
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
			
			if($on != "" && $this->app_mod !=""){
				require_once($this->globals['path']['apps_path'] . '/builder/' . $this->app_mod . '/model/' . $on .'_mod.php');
				$get_a_class = $on . '_mod';
			}else if($this->app_mod !=""){
				require_once($this->globals['path']['apps_path'] . '/builder/' . $this->app_mod . '/model/' . $this->app_mod .'_mod.php');
				$get_a_class = $this->app_mod . '_mod';
			}
			
			if(!class_exists('Base_Model_Forge'))
				require_once('base.c.mod.forge.php');
			
			return new Base_Model_Forge($get_a_class);
		}
	}

#//php_end\\;?>