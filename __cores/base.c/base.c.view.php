<?php #//php_start\\;
	
	/**
	* This the view controller and manager
	* 
	*/

	class Base_View{
		protected $globals;
		protected $app_view;
		public function __construct($app_view="")
		{
			global $GLOBALS;
			$this->globals = $GLOBALS;
			
			$this->app_view = $app_view;
			
		}
		
		public function load()
		{
			if($this->app_view != ""){
				require_once($this->globals['path']['apps_path'] . '/builder/' . $this->app_view . '/view/' . $this->app_view .'_view.php');
				$get_a_class = $this->app_view . '_view';
				return new $get_a_class();
			}
		}
		
		
		/**
		* Construct a variables with values as data that
		* can be filter on in the view
		*/
		public function set_data($param='', $value='')
		{
			require_once(dirname(__FILE__) . '/base.c.template.php');
			return new Base_Template($param, $value);
		}
		
		public function set_prepared_data($params, $value='')
		{
			if(isset($_SESSION['_prepared_data_']) && is_array($_SESSION['_prepared_data_']) && count($_SESSION['_prepared_data_'])){
				if(is_array($params) && count($params)){
					foreach($params as $itemKey => $itemValue){
						$_SESSION['_prepared_data_'][$itemKey] = $itemValue;
					}
				}else{
					$_SESSION['_prepared_data_'][$param] = $value;
				}
			}else{
				
				$_SESSION['_prepared_data_'] = array();
				
				if(is_array($params) && count($params)){
					foreach($params as $itemKey => $itemValue){
						$_SESSION['_prepared_data_'][$itemKey] = $itemValue;
					}
				}else{
					$_SESSION['_prepared_data_'][$param] = $value;
				}
			}
		}
		
		public function clear_prepared_data()
		{
			$_SESSION['_prepared_data_'] = array();
		}
		
		public function set_template($tplFile ="")
		{
			require_once(dirname(__FILE__) . '/base.c.template.php');
			$template = new Base_Template();
			return $template->set_template($tplFile);
		}
		
		public function use_prepared($param_prepared, $prepare_tpl){
			require_once(dirname(__FILE__) . '/base.c.template.php');
			$template = new Base_Template();
			return $template->use_prepared($param_prepared, $prepare_tpl);
		}
	}

#//php_end\\;?>