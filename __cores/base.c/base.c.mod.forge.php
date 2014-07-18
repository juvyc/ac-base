<?php

 class Base_Model_Forge
 {
	var $fields = array();
	
	var $__mcn;
	
	public function Base_Model_Forge($mcn)
	{
		if($mcn != "") $this->__mcn = new $mcn();
	}
	
	public function __set($fn, $value)
	{
		$this->fields[$fn] = $value;
	}
	
	public function update()
	{	
		return $this->__mcn->__table;
	}
	
	public function select($f, $where = array())
	{
		return 'fsdf';
	}
 }