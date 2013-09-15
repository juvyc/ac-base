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
			if(!class_exists('Base_Model')) require_once(dirname(__FILE__) . '/base.c.mod.php');
			return new Base_Model($app_mod);
			
		}
		
		/**
		* for view manager
		*/
		public function View($app_view='')
		{
			if(!class_exists('Base_View')) require_once(dirname(__FILE__) . '/base.c.view.php');
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
			if(!class_exists('Action_Base')) require_once(dirname(__FILE__) . '/base.c.action.php');
			return new Action_Base();
		}
		
		/**
		* For server
		*/
		public function SERVER()
		{
			if(!class_exists('Action_Base'))  require_once(dirname(__FILE__) . '/base.c.action.php');
			
			return new Action_Base('SERVER');
		}
		
		/**
		* For helper
		*/
		public function Helper($name = '')
		{
			if(!class_exists('_Helper')) require_once(dirname(__FILE__) . '/base.c.helper.php');	
			
			return new _Helper($name);			
		}
		
		/**
		* For upload
		*/
		
		public function Upload(){
		
			if(!class_exists('_Upload')) require_once(dirname(dirname(__FILE__)) . '/upload.c/upload.c.php');
			
			return new _Upload();
		}
		
		/**
		* For Directory
		*/
		
		public function Dir(){
		
			if(!class_exists('_Dir')) require_once(dirname(dirname(__FILE__)) . '/dir.c/dir.c.php');
			
			return new _Dir();
		}
		
		/**
		* For Photo Resize
		*/
		
		public function Photoresize(){
		
			if(!class_exists('_photoresize')) require_once(dirname(dirname(__FILE__)) . '/photoresize.c/photoresize.c.php');
			
			return new _Photoresize();
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
			if(!class_exists('Base_Session')) require_once(dirname(dirname(__FILE__)) . '/session.c/session.c.php');
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