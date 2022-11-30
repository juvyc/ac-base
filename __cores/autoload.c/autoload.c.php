<?php #//php_start\\;	

	class _Autoload
	{
		var $helpers = array();
		var $ACB_GLOBALS;
		public function __construct()
		{
			global $ACB_GLOBALS;
			$this->globals = $ACB_GLOBALS;
			$this->helpers = include($this->globals['path']['apps_path'] . '/config/autoload.c.php');
			$this->loader();
		}
		
		public function loader()
		{
			if(count($this->helpers)){
				$tmpParam = array();
				foreach($this->helpers as $path => $fns){
					include_once($this->globals['path']['apps_path'] . '/helper/' . $path . '_hpr.php');
					$get_classname = explode('/', $path);
					$realCN = $get_classname[count($get_classname) - 1] . '_hpr';
					
					if(is_array($fns)){
						if(count(count($fns))){
							foreach($fns as $fn){
								call_user_func_array(array(new $realCN(), $fn), array());
							}
						}
					}else{
						call_user_func_array(array(new $realCN(), $fns), array());
					}
				}
			}
		}
		
	}

#//php_end\\;?>