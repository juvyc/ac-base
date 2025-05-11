<?php #//php_start\\;	
	
	/**
	Thi class manage the autoloads functionalities 
	that will called before the controller globally
	*/
	class _Autoload
	{	
		public $helpers = array(); //Temporary handler of helpers classes to be called
		
		protected $globals; //As $ACB_GLOBALS variable temporary handler
		
		public function __construct()
		{
			global $ACB_GLOBALS;
			
			//Asssining $ACB_GLOBALS variable to the class property variable
			$this->globals = $ACB_GLOBALS;
			
			//Getting the lists of helpers classes assigned in the configure file
			$this->helpers = include($this->globals['path']['apps_path'] . '/config/autoload.c.php');
			
			//Calling the function that reads and execute the autoload classes
			$this->loader();
		}
		
		/**
		Function that reads and execute the autoload classes
		*/
		public function loader()
		{
			if(count($this->helpers)){
				$tmpParam = array();
				
				//Loop all helpes from the autoload configuration file
				foreach($this->helpers as $fns){
					
					$path = key($fns);
					$funcs = $fns[$path];
					
					//Render the file
					include_once($this->globals['path']['apps_path'] . '/helper/' . $path . '_hpr.php');
					
					//Split the path
					$get_classname = explode('/', $path);
					
					//Assign the class name in a variable and extracting the last segment from the splitted path
					$realCN = $get_classname[count($get_classname) - 1] . '_hpr';
					
					//Check if method names need to call are multiple or array
					if(is_array($funcs)){
						if(count($fns)){
							//Loop through all the methods that needs to be called
							foreach($funcs as $fn){
								//Finally execute the class method
								call_user_func_array(array(new $realCN(), $fn), array());
							}
						}
					}else{
						//Finally execute the class method
						call_user_func_array(array(new $realCN(), $funcs), array());
					}
				}
			}
		}
		
	}

#//php_end\\;
