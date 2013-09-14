<?php #//php_start\\;
	
	class requestmethod_hpr
	{
		public function redirect(){
			header('location: http://awonsa.com');
		}
		public function server()
		{
			if(!class_exists('_rm_server')){
				require_once(dirname(__FILE__) . '/_rm_server.php');
			}
			
			return new _rm_server();
		}
		
		public function post()
		{
			if(!class_exists('_rm_posts')){
				require_once(dirname(__FILE__) . '/_rm_posts.php');
			}
			
			return new _rm_posts();
		}
		
		public function get()
		{
			if(!class_exists('_rm_get')){
				require_once(dirname(__FILE__) . '/_rm_get.php');
			}
			
			return new _rm_get();
		}
		
		public function files()
		{
			if(!class_exists('_rm_files')){
				require_once(dirname(__FILE__) . '/_rm_files.php');
			}
			
			return new _rm_files();
		}
	}
	
#//php_end\\;?>