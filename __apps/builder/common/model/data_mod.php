<?php
 /**
 * @User_Mod
 * Type: Object
 * Desc: It handle all database related processes exclusive to user controller
 */
 
 class Data_Mod extends Base_System
 {
	public $_table = "data";
	public $_primary_key = "id";
	public $_fields = array(
		'id',
		'type',
		'datetime_created',
		'datetime_updated',
		'created_by',
		'status',
	);
	
	
	public function getResultsByType($_type = '', $_fields=array(), $_where=array(), $limit = '20')
	{
		$dbconn = $this->Ini()->DB();
		$_dbq = $dbconn->exec();
		
		$_slflds = ['d.*'];
		$_joins = [];
		foreach($_fields as $_k => $_fn){
			$alias = 'md_' . $_k;
			$_slflds[] = $alias . '.meta_value AS ' . $_fn;
			
			$_joins[] = [$alias, $_fn];
		}
		
		
		$qstmt = $_dbq
					->select($_slflds)
					->from($this->_table, 'd');
		
		
		foreach($_joins as $_jd){
			$qstmt = $qstmt->left_join('meta_data', $_jd[0])
							->on("{$_jd[0]}.data_id", 'd.id')
							->on("and", "{$_jd[0]}.meta_key", "=", "'{$_jd[1]}'");
		}
		
		if($_type) $qstmt = $qstmt->where('d.type', $_type);
		
		if(count($_where)){
			$qstmt = $qstmt->where($_where);
		}
		
		$qstmt = $qstmt->run();
		
		$dbconn->close_conn();
		
		return $qstmt;
	}
	
	public function insertNewData($_type = '', $_fields=array(), $_status = 'active')
	{
		
		$mod_global = $this->Ini()->Mod('common')->load('global');
		
		//$user_info = $mod_global->_getCurrentUserInfo();
		
		$dbconn = $this->Ini()->DB();
		$_dbq = $dbconn->exec();
		
		$data_id = $_dbq
					->insert('data')
						->data(array(
							'type' => $_type,
							'datetime_created' => $mod_global->dateTime(),
							'created_by' => 1,//$user_info->id,
							'status' => $_status
						))
					->run()
		->insert_id();
		
		if($data_id > 0){
			foreach($_fields as $_fldName => $_fldValue){
				$mdata_id = $_dbq
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
		
		$dbconn->close_conn();
		
		return $data_id;
	}
 }