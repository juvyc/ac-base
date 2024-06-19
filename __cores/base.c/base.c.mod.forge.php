<?php
 
 /**
 * @Base_Model_Forge (class), processor of 
	inserting information through model
 */ 
 class Base_Model_Forge
 {
	public $fields = array();
	
	private $__mcn;
	
	/**
	* @__construct (method), automatically start executing @Base_Model_Forge class when called
	*/
	public function __construct($mcn)
	{
		if($mcn != "") $this->__mcn = new $mcn();
	}
	/**
	* @__get (method), allow field to be accesible oublicly
	*/
	public function __get($fn)
	{
		//Return each field when paging 
		return $this->fields[$fn];
	}
	/**
	* @__set (method), set data into field publictly
	*/
	public function __set($fn, $value)
	{
		//assign parameter with value to publictly
		$this->fields[$fn] = $value;
	}
	/**
	* @save (method), finally save information
	*/
	public function save()
	{
		//check fields before saving
		if(count($this->fields)){
			//prepare database methods
			$db = $this->__mcn->Ini()->DB()->exec();
			
			if(method_exists($this->__mcn, 'validate')){
				$check_validation = $this->__mcn->validate('insert', $this->fields);
				if(!$check_validation['success']){
					$this->fields[$this->__mcn->_primary_key] = $check_validation; // return the id as array if there's an error
					return;
				}
				
				if(!empty($check_validation['fields_values'])){
					$this->fields = array_merge($this->fields, $check_validation['fields_values']);
				}
			}
			
			//Do inserting data
			$qui = $db->insert($this->__mcn->_table)
						->data($this->fields)
						->run();
			
			//check if saving process is successfull
			if($qui->insert_id()){
				//Assign value to primary key when success
				$this->fields[$this->__mcn->_primary_key] = $qui->insert_id();
			}else{
				//Else make it 0
				$this->fields[$this->__mcn->_primary_key] = false;
			}
		}
	}
 }