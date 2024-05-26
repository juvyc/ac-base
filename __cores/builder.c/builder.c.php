<?php #//php_start\\;
	
	class _Builder
	{
		public $ACB_GLOBALS;
		public $route;
		public $bldr_file;
		public $Uri;
		public $static_segments = array();
		public $fix_fn;
		public $ishome = false;
		
		protected $globals;
		protected $CONFIG;
		
		public function __construct(){
			global $ACB_GLOBALS;
			$this->globals = $ACB_GLOBALS;
			$this->Uri = new _Uri();
			$this->CONFIG = new _CONFIG();
		}
		 
		public function _checker($reroute = false, $static = false, $_debug=false)
		{
			/**
			* Checking if the current location is in the builder
			*/
			
			$this->route = ($static) ? $static : $this->_clean_segment($this->route);
			
			//echo $this->route;exit;
			
			$file_router = ($reroute) ? $reroute : $this->route;
			
			if($_debug){
				echo $this->globals['path']['apps_path'] . '/builder/' . $this->route . '/controller/' . $file_router . '_clr.php';
			}
			
			if(is_file($this->globals['path']['apps_path'] . '/builder/' . $this->route . '/controller/' . $file_router . '_clr.php')){
				$this->bldr_file = $this->globals['path']['apps_path'] . '/builder/' . $this->route . '/controller/' . $file_router . '_clr.php';
				return true;
			}
		}
		
		public function _caller($reclass = false, $_debug=false)
		{
			/**
			* This is the object caller from the selected builder
 			*/
			
			$route = str_replace('_', '', $reclass);
			$route = str_replace('-', '_', $route);
			
			//$route = ucwords($route);
			//$this->route = ucwords($this->route);
			
			$route = ($route) ? $route . '_Clr' : $this->route . '_Clr';
			if($_debug){
				echo $route;
			}
			
			//return 1;
			
			//echo $route;exit;
			
			if (class_exists($route)) {
				return new $route();
			}
		}
		
		public function _clean_segment($val)
		{
			if(!$val) return;
			$segmt = str_replace('_', '', $val);
			$segmt = str_replace('-', '_', $val);
			return $segmt;
		}
		
	}
	
#//php_end\\;?>