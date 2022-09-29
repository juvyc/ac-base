<?php
 /**
 * @User_Mod
 * Type: Object
 * Desc: It handle all database related processes exclusive to user controller
 */
 
 class Metadata_Mod extends Base_System
 {
	public $_results = [];
	public $_table = "meta_data";
	public $_primary_key = "id";
	public $_fields = array(
		'id',
		'data_id',
		'meta_key',
		'meta_value'
	);
	
	public function getByParentData($_parent_id = null)
	{
		if($_parent_id){
			$dbconn = $this->Ini()->DB();
			$_dbq = $dbconn->exec();
			
			$getMDs = $_dbq->select()->from($this->_table, 'mt')->where('data_id', $_parent_id)->run();
			
			$_results = [];
			while($_fi = $getMDs->fetch_object()){
				$_results[$_fi->meta_key] = $_fi->meta_value;
			}
			
			$dbconn->close_conn();
			
			return $_results;
		}
	}
	
	/**
	* To update meta data by parent data id
	*/
	public function updateBatchByParentData($_parent_id = null, $_flds= array())
	{
		if($_parent_id){
			$dbconn = $this->Ini()->DB();
			$_dbq = $dbconn->exec();
			
			foreach($_flds as $_fn => $_fv){
				$_v = $_dbq->escape_string($_fv);
				$_stmt = "UPDATE {$this->_table} SET meta_value='{$_v}' WHERE meta_key='{$_fn}' AND data_id='{$_parent_id}';";
				
				$_dbq->query($_stmt)->run();
			}
			
			$dbconn->close_conn();
			
			return 1;
		}
	}
	
 }