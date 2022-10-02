<?php #//php_start\\;

	class Test2_Clr extends Base_System
	{
		var $data = array(); //Data handler
		
		public function load_before()
		{
			/**
			* Assign the location of the public directories including the base root
			*/
			
			$this->dbconn = $this->Ini()->DB();
			$this->_dbq = $this->dbconn->exec();
			
			$this->data['full_name'] = 'Juvy Cagape';
			$this->data['email'] = 'cagapejuvy@gmail.com';
			
			$this->common_func = $this->Ini()->Mod('common');
			
			$this->post = $this->Ini()->Action()->POST();
			
		}
		
		public function action_index(){
			$form = $this->Ini()->Helper('forms')->load()->call();
			$mod_data = $this->Ini()->Mod('common')->load('data');
			$mod_metadata = $this->Ini()->Mod('common')->load('metadata');
			
			//print_r($mod_metadata->_results);
			
			$arrgs = array(
				
				array(
					'type' => 'text',
					'name' => 'company_info[company_name]',
					'label' => 'Company Name',
				),
				
				array(
					'type' => 'text',
					'name' => 'company_info[company_industry]',
					'label' => 'Industry',
				),
				
				array(
					'type' => 'text',
					'name' => 'company_info[company_website]',
					'label' => 'Website',
				),
				
				array(
					'type' => 'text',
					'name' => 'company_info[company_address_street]',
					'label' => 'Address',
				),
				
				array(
					'type' => 'text',
					'name' => 'company_info[company_address_city]',
					'label' => 'City',
				),
				
				array(
					'type' => 'text',
					'name' => 'company_info[company_address_state]',
					'label' => 'State',
				),
				
				array(
					'type' => 'text',
					'name' => 'company_info[company_address_postalcode]',
					'label' => 'Zip Code',
				),
				
				array(
					'type' => 'text',
					'name' => 'company_info[company_address_country]',
					'label' => 'Country',
				),
				
				array(
					'type' => 'text',
					'name' => 'lead_info[lead_title]',
					'label' => 'Contact Position',
				),
				
				array(
					'type' => 'text',
					'name' => 'lead_info[lead_fname]',
					'label' => 'Contact First Name',
				),
				
				array(
					'type' => 'text',
					'name' => 'lead_info[lead_lname]',
					'label' => 'Contact Last Name',
				),
				
				array(
					'type' => 'text',
					'name' => 'lead_info[lead_phone]',
					'label' => 'Contact Phone',
				),
				
				array(
					'type' => 'text',
					'name' => 'lead_info[lead_emailadd]',
					'label' => 'Contact Email',
				),
				
				array(
					'type' => 'submit',
					'value' => 'Save',
					'class' => 'btn btn-primary',
				)
			);
			
			if(count($_POST)){
				
				//print_r($this->post->params['lead_info']);
				//exit;
				
				 $modCompanies = $this->Ini()->Mod('common')->forge('companies');
				 
				 //$modCompanies->fields = $this->post->params['company_info'];
				 
				/**/
				foreach($this->post->params['company_info'] as $_fn => $_fv){
					$modCompanies->$_fn = $_fv;
				}
				//*/
				
				
				$modCompanies->save();
				
				
				$_company_id = ($modCompanies) ? $modCompanies->company_id : 0;
				
				
				
				if($_company_id > 0){
					$modLeads = $this->Ini()->Mod('common')->forge('leads');
					 
					foreach($this->post->params['lead_info'] as $_fn => $_fv){
						$modLeads->$_fn = $_fv;
					}
					
					//print_r($modLeads->fields);
					//exit;
					
					$modLeads->save();
					
					$_lead_id = ($modLeads) ? $modLeads->lead_id : 0;
					
					if($_lead_id > 0){
						$modLinker = $this->Ini()->Mod('common')->forge('linker');
						$modLinker->type = 'leads';
						$modLinker->base_id = $_company_id;
						$modLinker->ref_id = $_lead_id;
						$modLinker->save();
						
						if($modLinker->id > 0){
							$this->Ini()->redirect(base_url . 'test2');
						}
					}
					 
				}
			}
			
			
			$this->data['_form_fields'] = $form->fields_builder($arrgs);
			
			
			$get_leads_data = $this->_dbq->select(array('*'))
							->from('linker', 'lnk')
							
								->inner_join('leads', 'ld')
									->on('ld.lead_id', 'lnk.ref_id')
								
								->inner_join('companies', 'cmp')
									->on('cmp.company_id', 'lnk.base_id')
							->where('lnk.type', 'leads')
							
						->run();
								
			
			$this->data['_data_results'] = $get_leads_data;
			
			$this->dbconn->close_conn();
			return $this
					->Ini()
						->View()
							->set_data($this->data)
								->use_prepared('content', 'default/testform2');
			
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