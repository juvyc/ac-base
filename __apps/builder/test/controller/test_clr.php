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
			//$mod_data = $this->Ini()->Mod('common')->load('data')->call();
			//$mod_metadata = $this->Ini()->Mod('common')->load('metadata')->call();
			
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
			
			//INSERTING FORM FIELDS TO DATABASE
			if(count($this->Ini()->Action()->POST()->params)){
				$data_id = $this->Ini()->DB()->exec()
							->insert('data')
								->data(array(
									'type' => 'lead',
									'datetime_created' =>  date('Y-m-d H:i:s'),
									'created_by' => 1,
									'status' => 'active'
								))
							->run()
				->insert_id();
				
				//echo $data_id;
				
				
				if($data_id > 0){
					
					//print_r($this->Ini()->Action()->POST()->params);
					
					foreach($this->Ini()->Action()->POST()->params as $_fldName => $_fldValue){
						$mdata_id = $this->Ini()->DB()->exec()
									->insert('meta_data')
										->data(array(
											'data_id' => $data_id,
											'meta_key' => $_fldName,
											'meta_value' => $_fldValue,
										))
									->run()
						->insert_id();
					}
				}
			}
			
			
			$db = $this->Ini()->DB()->exec();
			
			$stmt = $db
					->select(array(
						'd.*', 
						'md1.meta_value AS first_name',
						'md2.meta_value AS last_name',
						'md3.meta_value AS email',
					))
					->from('data', 'd')
					
						->inner_join('meta_data', 'md1')
							->on('md1.data_id', 'd.id')
								->on("AND md1.meta_key = 'first_name'")
								
						->inner_join('meta_data', 'md2')
							->on('md2.data_id', 'd.id')
								->on("AND md2.meta_key = 'last_name'")
								
						->inner_join('meta_data', 'md3')
							->on('md3.data_id', 'd.id')
								->on("AND md3.meta_key = 'email'")
							
					->where(array(
						'd.type' => 'lead'
					))
					->run();
			
			$this->data['_data_results'] = $stmt;
			$this->data['_form_fields'] = $form->fields_builder($arrgs);
			
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