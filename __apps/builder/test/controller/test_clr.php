<?php #//php_start\\;

	class Test_Clr extends Base_System
	{
		var $data = array(); //Data handler
		
		public function load_before()
		{
			/**
			* Assign the location of the public directories including the base root
			*/
			
			$this->data['full_name'] = 'Juvy Cagape';
			$this->data['email'] = 'cagapejuvy@gmail.com';
			
		}
		
		public function action_index($a = '', $b){
			/**
			* Call the model
			*/
			
			
			return $this
					->Ini()
						->Mod('test')
							->load()
								->home_mod();
		}
		
		public function _affdata()
		{
			$this->data['base_root'] = $this->Ini()->Uri()->base();
			
			$this->data['theme_root'] = $this->data['base_root'] . '__themes/default/';
			
			$requestmethod = $this->Ini()->Helper('requestmethod')->call();
			
			$this->data['server'] = $requestmethod->server();
			
			$this->data['get'] = $requestmethod->get();
			
			$this->data['post'] = $requestmethod->post();
			
			$this->data['files'] = $requestmethod->files();
			
		}
		
		public function action_form()
		{
			echo $this->Ini()->Action()->POST()->param('email');
			
			if($this->Ini()->Action()->POST()->param('email')){
				$this->Ini()->redirect($this->Ini()->SERVER()->param('REQUEST_URI'));
				echo $this->Ini()->Action()->POST()->param('email');
			}
			
			return $this
					->Ini()
						->View()
							->set_data($this->data)
								->set_template('default/testform');
		}
		
		public function action_sample()
		{
			return 1;
		}
		
		public function load_after()
		{
			
		}
		
		public function fload_404()
		{
			header("HTTP/1.0 500 Page not found!");	
			return '000';
		}
		
	}
	
#//php_end\\;?>