<?php #//php_start\\;

	class Default_Clr extends Base_System
	{
		var $_data = array(); //Data handler
		
		public function load_before()
		{
			
			$this->forms = $this->Ini()->Helper('forms')->load()->call();
		}
		
		public function action_index($a = null, $b = null){
			
			$_args = array(
				
				array(
					'type' => 'paragraph',
					'name' => 'test_field',
					'label' => 'Test Field',
					'gW' => 4,
					'attribs' => 'alphanumspace maxlength="15"',
					'fldconstyle' => 'flex: 0 0 150px;',
				),
				
				array(
					'type' => 'email',
					'name' => 'test_field',
					'label' => 'Test Field',
					'gW' => 4,
					'attribs' => 'alphanumspace maxlength="15"',
					'fldconstyle' => 'flex: 0 0 150px;',
				),
				
				
				array(
					'type' => 'group',
					'fields' => array(
						array(
							'type' => 'file',
							'name' => 'test_field',
							'label' => 'Test Field',
							'gW' => 3,
							'attribs' => 'alphanumspace maxlength="15"',
							'fldconstyle' => 'flex: 0 0 150px;',
						),
						
						array(
							'type' => 'file',
							'name' => 'test_field',
							'label' => 'Test Field',
							'gW' => 3,
							'attribs' => 'alphanumspace maxlength="15"',
							'fldconstyle' => 'flex: 0 0 150px;',
						),
						
						array(
							'type' => 'file',
							'name' => 'test_field',
							'label' => 'Test Field',
							'gW' => 3,
							'attribs' => 'alphanumspace maxlength="15"',
							'fldconstyle' => 'flex: 0 0 150px;',
						),
						
						array(
							'type' => 'file',
							'name' => 'test_field',
							'label' => 'Test Field',
							'gW' => 3,
							'attribs' => 'alphanumspace maxlength="15"',
							'fldconstyle' => 'flex: 0 0 150px;',
						),
					)
				),
			
				
				
					array(
						'type' => 'group',
						'fields' => array(
							array(
								'type' => 'text',
								'name' => 'prev_id_no',
								'gW' => 4,
								'label' => 'Prev. ID #',
								'attribs' => 'alphanumspace maxlength="15"',
								'fldconstyle' => 'flex: 0 0 150px;'
							),
						
							array(
								'type' => 'text',
								'name' => 'first_name',
								'gW' => 4,
								'label' => 'First Name',
								'required' => 1,
								'attribs' => 'alphanumspace maxlength="45"',
							),
							
							array(
								'type' => 'text',
								'gW' => 4,
								'name' => 'middle_name',
								'label' => 'Middle Name',
								'attribs' => 'alphanumspace maxlength="45"',
							),
							
							array(
								'type' => 'text',
								'name' => 'last_name',
								'label' => 'Last Name',
								'required' => 1,
								'attribs' => 'alphanumspace maxlength="45"',
							),
							
							array(
								'type' => 'text',
								'name' => 'name_ext',
								'label' => 'Name Ext.',
								'attribs' => 'alphanum maxlength="3"',
								'fldconstyle' => 'flex: 0 0 150px;'
							),
						),
					),
					
					array(
						'type' => 'group',
						'fields' => array(
							
							array(
								'type' => 'file',
								'name' => 'photo',
								'label' => 'Photo',
								'gW' => 3,
							),
						
							array(
								'type' => 'text',
								'name' => 'designation',
								'label' => 'Designation',
								'required' => 1,
								'gW' => 3,
								'attribs' => 'alphanumspace maxlength="100"',
							),
						
							array(
								'type' => 'date',
								'name' => 'birthday',
								'label' => 'Birthday',
								'required' => 1,
								'gW' => 3,
								'attribs' => 'date',
								'fldconstyle' => 'flex: 0 0 150px;',
							),
							
							array(
								'type' => 'select',
								'name' => 'gender',
								'label' => 'Gender',
								'options' => array('Male', 'Female'),
								'required' => 1,
								'gW' => 3,
							),
							
						),
					),
					
					
					array(
						'type' => 'group',
						'fields' => array(
							array(
								'type' => 'text',
								'name' => 'phone_number',
								'label' => 'Phone',
								'required' => 1,
								'gW' => 6,
								'attribs' => 'alphanumspace maxlength="12"',
							),
							
							array(
								'type' => 'text',
								'name' => 'email',
								'label' => 'Email',
								'required' => 1,
								'gW' => 6,
								'attribs' => 'alphanumspace maxlength="50"',
							),
							
						),
					),
					
					
					array(
						'type' => 'group',
						'fields' => array(
							array(
								'type' => 'text',
								'name' => 'street',
								'label' => 'Street',
								'required' => 1,
								'gW' => 3,
								'attribs' => 'alphanumspace maxlength="50"',
							),
							
							array(
								'type' => 'text',
								'name' => 'village',
								'label' => 'Village',
								'required' => 1,
								'gW' => 3,
								'attribs' => 'alphanumspace maxlength="50"',
							),
							
							array(
								'type' => 'text',
								'name' => 'city',
								'label' => 'Town/City',
								'required' => 1,
								'gW' => 3,
								'attribs' => 'alphanumspace maxlength="50"',
								'fldconstyle' => 'flex: 0 0 150px;',
							),
							
							array(
								'type' => 'text',
								'name' => 'state',
								'label' => 'State/Province',
								'required' => 1,
								'gW' => 3,
								'attribs' => 'alphanumspace maxlength="50"',
							),
							
							array(
								'type' => 'text',
								'name' => 'zip_code',
								'label' => 'Zip Code',
								'required' => 1,
								'gW' => 3,
								'attribs' => 'alphanumspace maxlength="7"',
								'fldconstyle' => 'flex: 0 0 80px;',
							),
							
						),
					),
					
					array(
						'type' => 'group',
						'begin' => '<h4><i>Contact Person</i></h4>',
						'fields' => array(
							array(
								'type' => 'text',
								'name' => 'cp_full_name',
								'label' => 'Full Name',
								'required' => 1,
								'gW' => 3,
								'attribs' => 'alphanumspace maxlength="150"',
							),
							
							array(
								'type' => 'text',
								'name' => 'cp_phone',
								'label' => 'Phone',
								'required' => 1,
								'gW' => 3,
								'attribs' => 'alphanumspace maxlength="15"',
								'fldconstyle' => 'flex: 0 0 250px;',
							),
							
							array(
								'type' => 'text',
								'name' => 'cp_email',
								'label' => 'Email',
								'gW' => 3,
								'attribs' => 'alphanumspace maxlength="50"',
							),
						),
					),
					
					array(
						'type' => 'group',
						'fields' => array(
							array(
								'type' => 'text',
								'name' => 'cp_complete_address',
								'label' => 'Complete Address',
								'required' => 1,
								'attribs' => 'alphanumspace',
							),
						),
					),
					
					
					array(
						'type' => 'submit',
						'value' => 'Save',
						'class' => 'btn btn-black',
					),
				);
			

			if(!empty($this->Ini()->Action()->POST()->params)){
				$this->forms->_save('students', $this->Ini()->Action()->POST()->params);
			}
			
			
			
			$this->_data['_form_fields'] = $this->forms->fields_builder($_args, array('group_values' => array('city' => 'Bislig', 'birthday' => '12-12-2000')));
			
			
			
			return $this
					->Ini()
						->View()
							->set_data($this->_data)
								->use_prepared('content', 'default/testform');
		}
		
	}
	
#//php_end\\;?>