<?php #//php_start\\;
	
	class _Builder
	{
		var $globals;
		var $route;
		var $bldr_file;
		var $Uri;
		var $static_segments = array();
		var $fix_fn;
		var $ishome = false;
		
		public function _Builder(){
			global $GLOBALS;
			$this->globals = $GLOBALS;
			$this->Uri = new _Uri();
			$this->CONFIG = new _CONFIG();
		}
		
		public function _checker($reroute = false, $static = false)
		{
			/**
			* Checking if the current location is in the builder
			*/
			
			$this->route = ($static) ? $static : $this->_clean_segment($this->route);
			
			$file_router = ($reroute) ? $reroute : $this->route;
			
			if(is_file($this->globals['path']['apps_path'] . '/builder/' . $this->route . '/controller/' . $file_router . '_clr.php')){
				$this->bldr_file = $this->globals['path']['apps_path'] . '/builder/' . $this->route . '/controller/' . $file_router . '_clr.php';
				return true;
			}
		}
		
		public function _caller($reclass = false)
		{
			/**
			* This is the object caller from the selected builder
 			*/
			$route = ($reclass) ? $reclass . '_Clr' : $this->route . '_Clr';
			if (class_exists($route)) {
				return new $route();
			}
			
		}
		
		public function _clean_segment($val)
		{
			$segmt = str_replace('-', '_', $val);
			return $segmt;
		}
		
	}
	
#//php_end\\;?>