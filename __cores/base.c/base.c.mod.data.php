<?php
 
 /**
 * @Base_Model_Data (class), processor of 
	select, update and delete information through model
 */ 
 class Base_Model_Data
 {
	public $fields = array();
	private $__where = array();
	private $__stmt = null;
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
	* @_select (method), execute select
	*/
	public function _select()
	{
		$db = $this->__mcn->Ini()->DB()->exec();
		$this->__stmt = $db->select($this->__mcn->_fields)->from($this->__mcn->_table);
		if(count($this->__where)){
			$this->__stmt = $this->__stmt->where($this->__where);
		}
	}
	
	/**
	* @save (method), to save change of existing data
	*/
	public function save()
	{
		//check fields before saving updates
		if(count($this->fields)){
			//prepare database methods
			$db = $this->__mcn->Ini()->DB()->exec();
			//Do updating data
			$qui = $db->update($this->__mcn->_table)
						->set($this->fields)
						->where($this->__where)
						->run()
						->affected_rows();
			
			//return the affected rows
			return $qui;
		}
	}
	
	/**
	* @delete (method), to remove existing data
	*/
	public function delete()
	{
			//prepare database methods
			$db = $this->__mcn->Ini()->DB()->exec();
			//Do updating data
			$qui = $db->delete($this->__mcn->_table)
						->where($this->__where)
						->run()
						->affected_rows();
			
			//return the affected rows
			return $qui;
	}
	
	/**
	* @by (method), adding where statement
	*/ 
	public function by($where = array())
	{
		$this->__where = $where;
		return $this;
	}
	
	public function fetch_one()
	{
		$this->_select();
		$getOne = $this->__stmt->limit(1)->run()->fetch_object();
		if($getOne)
			return $getOne;
	}
	
	public function fetch()
	{
		$this->_select();
		$getAll = $this->__stmt->run();
		$rsdata = array();
		while($row = $getAll->fetch_object())
		{	
			$rsdata[] = $row;
		}
		
		return $rsdata;
	}
 }