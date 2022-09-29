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
			
			$this->common_func = $this->Ini()->Mod('common');
			
		}
		
		public function action_index(){
			$form = $this->Ini()->Helper('forms')->load()->call();
			$mod_data = $this->Ini()->Mod('common')->load('data');
			$mod_metadata = $this->Ini()->Mod('common')->load('metadata');
			
			//print_r($mod_metadata->_results);
			
			$arrgs = array(
				array(
					'type' => 'text',
					'name' => 'first_name',
					'label' => 'First Name',
				),
				
				array(
					'type' => 'text',
					'name' => 'last_name',
					'label' => 'Last Name',
				),
				
				array(
					'type' => 'text',
					'name' => 'phone',
					'label' => 'Phone Number',
				),
				
				array(
					'type' => 'text',
					'name' => 'email',
					'label' => 'Email Address',
				),
				
				array(
					'type' => 'submit',
					'value' => 'Save',
					'class' => 'btn btn-primary',
				)
			);
			
			
			$this->data['_data_results'] = $mod_data->getResultsByType('lead', 
				array(
					'first_name',
					'last_name',
					'phone',
					'email',
				)
			);
			
			$_form_data = [];
			
			if($cid = $this->Ini()->Action()->GET()->param('cid')){
				if(count($this->Ini()->Action()->POST()->params)){
					$mod_metadata->updateBatchByParentData($cid, $this->Ini()->Action()->POST()->params);
				}
				
				$_form_data = $mod_metadata->getByParentData($cid);
			}else{
				if(count($this->Ini()->Action()->POST()->params)){
					$mod_data->insertNewData('lead', $this->Ini()->Action()->POST()->params);
				}
			}
			
			$this->data['_form_fields'] = $form->fields_builder($arrgs, array(
				'group_values' => $_form_data
			));
			
			return $this
					->Ini()
						->View()
							->set_data($this->data)
								->use_prepared('content', 'default/testform');
			
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