<?php #//php_start\\;

	class Default_Clr extends Base_System
	{
		var $_data = array(); //Data handler
		
		public function load_before()
		{
			
			$this->forms = $this->Ini()->Helper('forms')->load()->call();
			
			$this->conn = $this->Ini()->DB();
			$this->_dbq = $this->conn->exec();
		}
		
		public function action_index($a = null, $b = null){
			
			$_args = array(
				
				array(
					'type' => 'text',
					'name' => 'customer_name',
					'label' => 'Customer Name',
				),
					
				array(
					'type' => 'paragraph',
					'name' => 'address',
					'label' => 'Address',
				),
					
				array(
					'type' => 'text',
					'name' => 'phone',
					'label' => 'Phone Number',
				),
					
				array(
					'type' => 'submit',
					'value' => 'Save',
					'class' => 'btn btn-primary',
				),
			);
			
			
			$cid = $this->Ini()->Action()->GET()->param('cid');

			if(!empty($this->Ini()->Action()->POST()->params)){
				$this->forms->_save('customers', $this->Ini()->Action()->POST()->params, ($cid ? ['id' => $cid ] : []));
			}
			
			$_data_result = [];
			$_customer_model = $this->Ini()->Mod('default');
			
			if($cid){
				$dataModel = $_customer_model->get_data('customers')->by(array('id' => $cid))->fetch_one();
                $_data_result = (array) $dataModel;
			}
			
			$this->_data['_form_fields'] = $this->forms->fields_builder($_args, array('group_values' => $_data_result));
			
			$this->_data['customers_lists'] = $_customer_model->get_data('customers')->fetch();
			
			return $this
					->Ini()
						->View()
							->set_data($this->_data)
								->use_prepared('content', 'default/testform');
		}
		
	}
	
#//php_end\\;?>