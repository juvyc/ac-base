<?php #//php_start\\;
	
	/**
	* @Base_View (class), communication of controller to 
		template folder and data sender to template
	* 
	*/

	class Base_View{
		protected $globals;//handle all global variables
		protected $app_view; //temp handle of view file
		public function __construct($app_view="")
		{
			//Call global parameters
			global $GLOBALS;
			$this->globals = $GLOBALS;
			//securing app_view
			$this->app_view = $app_view;
		}
		
		/**
		* @load (method), loader of the view class from builder (optional), 
			this only neccessary if you want to shorten your codes in your controller
			and put other like views data into view class
		*/
		public function load()
		{
			if($this->app_view != ""){
				//Load the view class file
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
		
		/**
		* @set_prepared_data (method), assigner of prepared data
		*/
		public function set_prepared_data($params, $value='')
		{
			if(isset($_SESSION['_prepared_data_']) && is_array($_SESSION['_prepared_data_']) && count($_SESSION['_prepared_data_'])){
				//Check if array (this will ignore the @$value)
				if(is_array($params) && count($params)){
					foreach($params as $itemKey => $itemValue){
						//Add new requested prepared data into the existing list
						$_SESSION['_prepared_data_'][$itemKey] = $itemValue;
					}
				}else{
					//Add new requested prepared data into the existing list
					$_SESSION['_prepared_data_'][$params] = $value;
				}
			}else{
				//Set prepared data if not exist
				$_SESSION['_prepared_data_'] = array();
				//check if multiple data is in need to add
				if(is_array($params) && count($params)){
					foreach($params as $itemKey => $itemValue){
						//Assign prepared data
						$_SESSION['_prepared_data_'][$itemKey] = $itemValue;
					}
				}else{
					//Assign prepared data
					$_SESSION['_prepared_data_'][$params] = $value;
				}
			}
		}
		
		/**
		* @get_prepared_data (method), use to retrive prepared existing data
		*/
		public function get_prepared_data($key = '')
		{
			if(isset($_SESSION['_prepared_data_'][$key])){
				return $_SESSION['_prepared_data_'][$key];
			}
		}
		
		/**
		* @clear_prepared_data (method), removal of the specific session @_prepared_data_ (session name)
		*/
		public function clear_prepared_data($opt = array())
		{
			//Check if only specific data to remove
			if(count($opt)){
				//Check if multiple prepared data to remove
				if(is_array($opt)){
					foreach($opt as $pds){
						if(isset($_SESSION['_prepared_data_'][$pds])){
							//Clear prepare data if exist
							$_SESSION['_prepared_data_'][$pds] = null;
						}
					}
				}else{
					if(isset($_SESSION['_prepared_data_'][$opt])){
						//Clear prepare data if exist
						$_SESSION['_prepared_data_'][$opt] = null;
					}
				}
			}else
				$_SESSION['_prepared_data_'] = array(); //clear all prepared data
		}
		
		/**
		* @set_template (method), template loader
		*/
		public function set_template($tplFile ="")
		{
			/**
			* No checking is required since setting of template is must and not should be empty
				Better not to call this method when template is not neccessary in your controller
			*/
			require_once(dirname(__FILE__) . '/base.c.template.php');
			//Calling the class @Base_Template
			$template = new Base_Template();
			//Calling @set_template method that output template
			$get_template = $template->set_template($tplFile);
			$this->clear_prepared_data();
			return $get_template;
		}
		
		/**
		* @get_template (method), template passable to parameter
		*/
		public function get_template($tplFile ="")
		{
			/**
			* No checking is required since setting of template is must and not should be empty
				Better not to call this method when template is not neccessary in your controller
			*/
			require_once(dirname(__FILE__) . '/base.c.template.php');
			//Calling the class @Base_Template
			$template = new Base_Template();
			//Calling @get_template method that output template
			$get_template = $template->get_template($tplFile);
			return $get_template;
		}
		
		/**
		* @use_prepared (method), builder of prepared template assign @ theme.a.c.php
		*/
		public function use_prepared($param_prepared, $prepare_tpl){
			require_once(dirname(__FILE__) . '/base.c.template.php');
			$template = new Base_Template();
			$get_template = $template->use_prepared($param_prepared, $prepare_tpl);
			$this->clear_prepared_data();
			return $get_template;
		}
	}

#//php_end\\;?>