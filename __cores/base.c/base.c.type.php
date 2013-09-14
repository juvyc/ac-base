<?php #//php_start\\;
	
	/**
	* This the model controller and manager
	* 
	*/

	class Base_Type
	{	
		/**
		* for modules manager
		*/
		public function Mod($app_mod = "")
		{
			require_once(dirname(__FILE__) . '/base.c.mod.php');
			return new Base_Model($app_mod);
			
		}
		
		/**
		* for view manager
		*/
		public function View($app_view='')
		{
			require_once(dirname(__FILE__) . '/base.c.view.php');
			return new Base_View($app_view);
		}
		
		/**
		* assign the database
		*/
		public function DB()
		{
			return new _DB();
		}
		
		/**
		* for request url, like segment
		*/
		public function Uri()
		{
			return new _Uri();
		}
		
		/**
		* For form action
		*/
		public function Action()
		{
			require_once(dirname(__FILE__) . '/base.c.action.php');
			return new Action_Base();
		}
		
		/**
		* For server
		*/
		public function SERVER()
		{
			require_once(dirname(__FILE__) . '/base.c.action.php');
			return new Action_Base('SERVER');
		}
		
		/**
		* For helper
		*/
		public function Helper($name = '')
		{
			require_once(dirname(__FILE__) . '/base.c.helper.php');	
			return new _Helper($name);			
		}
		
		/**
		* MD5 encryption
		*/
		public function MD5($string)
		{
			return md5($string);
		}
		
		/**
		* base64 encryption
		*/
		public function base64_encode($string)
		{
			return base64_encode($string);
		}
		
		/**
		* base64 decryption
		*/
		public function base64_decode($string)
		{
			return base64_decode($string);
		}
		
		/**
		* Session controller
		*/
		public function session()
		{
			require_once(dirname(dirname(__FILE__)) . '/session.c/session.c.php');
			return new Base_Session();
		}
		
		/**
		* @redirect
		*/
		
		public function redirect($url)
		{
			header("location:" . $url);
		}
	}

#//php_end\\;?>