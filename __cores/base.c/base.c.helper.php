<?php #//php_start\\;
	
	class _Helper
	{
		var $helpers = array();
		var $ACB_GLOBALS;
		var $name;
		public function __construct($name='')
		{
			global $ACB_GLOBALS;
			$this->globals = $ACB_GLOBALS;			
			$this->name = $name;
		}
		
		public function call()
		{
			$classname = $this->name . '_hpr';
			return new $classname();
		}
		
		public function load()
		{
			if(is_array($this->name) && count($this->name)){
				foreach($this->name as $helper){
					include_once($this->globals['path']['apps_path'] . '/helper/' . $helper . '_hpr.php');
				}
			}else{
				include_once($this->globals['path']['apps_path'] . '/helper/' . $this->name . '_hpr.php');
			}
			
			$get_last_segment = explode('/', $this->name);
			$hprname = $get_last_segment[count($get_last_segment) - 1];
			return new _Helper($hprname);
		}
		
	}

#//php_end\\;?>