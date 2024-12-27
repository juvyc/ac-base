<?php
 
 /**
 * @Base_Model_Data (class), processor of 
	select, update and delete information through model
 */ 
 class Base_Model_Data
 {
	public $fields = array();
	public $relations_fields = array();
	private $__where = array(), $__or_where = [], $group_by, $order_by, $limit;
	private $whereOrig = [];
	private $__tmp_where = [];
	private $__stmt = null;
	private $_novalidation = false;
	private $__exec_relations = false;
	private $__joins = [];
	private $__mcn; // model class name
	private $__exclude_relations = [];
	private $__only_these_relations = [];
	
	public function where()
	{
		$numargs = func_num_args();
		$arg_list = func_get_args();
		if($numargs) $this->whereOrig[] = $arg_list;
		
		return $this;
	}
	
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
	
	//Execute query with relations
	public function withRelations()
	{
		$numargs = func_num_args();
		if($numargs > 0){
			$arg_list = func_get_args();
			foreach($arg_list[0] as $_relation_option){
				$extract_not = explode('!', $_relation_option);
				if(count($extract_not) == 2){
					$this->__exclude_relations[] = str_replace('!', '', $_relation_option);
				}else{
					$this->__only_these_relations[] = $_relation_option;
				}
			}
		}
		
		$this->__exec_relations = true;
		return $this;
	}
	
	//joins query builder
	private function __join_builder($_propname)
	{
		if(!$this->__exec_relations) return;
		
		if(property_exists($this->__mcn, $_propname)){
			foreach($this->__mcn->$_propname as $prop){
				$tbln = key($prop);
				
				if(in_array($tbln, $this->__exclude_relations)) continue;
				
				if(count($this->__only_these_relations) && !in_array($tbln, $this->__only_these_relations)) continue;
				
				$innjoin = $prop[key($prop)];
				
				$alias = (!empty($innjoin['alias']) ? $innjoin['alias'] : $tbln);
				
				$join_props = [
					'table_name' => $tbln,
					'alias' => $alias,
				];
				
				foreach($innjoin['fields'] as $fld){
					$fld = str_replace('{base_table}', $this->__mcn->_table, $fld);
					$fld = str_replace('{alias}', $alias, $fld);
					$this->relations_fields[] = $fld;
				}
				
				$relations = [];
				foreach($innjoin['relations'] as $relcon){
					$relcon = str_replace('{base_table}', $this->__mcn->_table, $relcon);
					$relcon = str_replace('{alias}', $alias, $relcon);
					$relations[] = $relcon;
				}
				
				$join_props['rel_con'] = ' ON ' . implode(' ', $relations);
				
				$this->__joins[] = [
					"props" => $join_props,
					"type" => $_propname
 				];
				
				if(!empty($innjoin['conditions'])){
					foreach($innjoin['conditions'] as $_jwhere){
						if(!is_array($_jwhere)){
							$_jwhere = str_replace('{base_table}', $this->__mcn->_table, $_jwhere);
							$_jwhere = str_replace('{alias}', $alias, $_jwhere);
						}else{
							$_tmp_wl = [];
							foreach($_jwhere as $_wl){
								$_wl = str_replace('{base_table}', $this->__mcn->_table, $_wl);
								$_wl = str_replace('{alias}', $alias, $_wl);
								$_tmp_wl[] = $_wl;
							}
							
							$_jwhere = $_tmp_wl;
						}
						
						$this->__tmp_where[] = $_jwhere;
					}
				}
			}
		}
	}
	
	/**
	* @_select (method), execute select
	*/
	public function _select($fields_overrider = [])
	{
		$this->relations_fields = [];
		$this->__joins = [];
		$this->__tmp_where = [];
		
		$db = $this->__mcn->Ini()->DB()->exec();
		
		$_fields_refined = [];
		foreach($this->__mcn->_fields as $fn){
			$_fields_refined[] = '`' . $this->__mcn->_table . '`.`' . $fn . '`';
		}
		
		//build joins here
		$this->__join_builder('join_inner');
		$this->__join_builder('join_left');
		$this->__join_builder('join_right');
		$this->__join_builder('join_full');
		
		$_merged_fields = array_merge($_fields_refined, $this->relations_fields);
		
		$_list_overrider_flds = [];
		if(!empty($fields_overrider)){
			foreach($fields_overrider as $_tmp_override_fld){
				$_tmp_override_fld = str_replace('{base_table}', $this->__mcn->_table, $_tmp_override_fld);
				$_list_overrider_flds[] = $_tmp_override_fld;
			}
		}
		
		$this->__stmt = $db->select((!empty($_list_overrider_flds)) ? $_list_overrider_flds : $_merged_fields)->from($this->__mcn->_table);
		
		foreach($this->__joins as $_join){
			if($_join['type'] == 'join_inner'){
				$this->__stmt = $this->__stmt->inner_join($_join['props']['table_name'], $_join['props']['alias'])->option($_join['props']['rel_con']);
			}else if($_join['type'] == 'join_left'){
				$this->__stmt = $this->__stmt->left_join($_join['props']['table_name'], $_join['props']['alias'])->option($_join['props']['rel_con']);
			}else if($_join['type'] == 'join_right'){
				$this->__stmt = $this->__stmt->right_join($_join['props']['table_name'], $_join['props']['alias'])->option($_join['props']['rel_con']);
			}else if($_join['type'] == 'join_full'){
				$this->__stmt = $this->__stmt->full_join($_join['props']['table_name'], $_join['props']['alias'])->option($_join['props']['rel_con']);
			}
		}
		
		if(count($this->whereOrig)){
			
			$tsnf = json_encode($this->whereOrig);
			$tsnf = str_replace('{base_table}', $this->__mcn->_table, $tsnf);
			$this->whereOrig = json_decode($tsnf, true);
			
			foreach($this->whereOrig as $args){
				$this->__stmt = $this->__stmt->where(...$args);
			}
		}
		
		if(count($this->__where)){
			$tsnf = json_encode($this->__where);
			$tsnf = str_replace('{base_table}', $this->__mcn->_table, $tsnf);
			$this->__where = json_decode($tsnf, true);
			$this->__stmt = $this->__stmt->where($this->__where);
		}
		
		if(count($this->__or_where)){
			$tsnf = json_encode($this->__or_where);
			$tsnf = str_replace('{base_table}', $this->__mcn->_table, $tsnf);
			$this->__or_where = json_decode($tsnf, true);
			$this->__stmt = $this->__stmt->or_where($this->__or_where);
		}
		
		if(!empty($this->__tmp_where)){
			$tsnf = json_encode($this->__tmp_where);
			$tsnf = str_replace('{base_table}', $this->__mcn->_table, $tsnf);
			$this->__tmp_where = json_decode($tsnf, true);
			
			foreach($this->__tmp_where as $_relwhere){
				$this->__stmt = $this->__stmt->where($_relwhere);
			}
		}
		
		return $this->__stmt;
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
			
			if(!$this->_novalidation && method_exists($this->__mcn, 'validate')){
				$check_validation = $this->__mcn->validate('update', $this->fields);
				if(!$check_validation['success']){
					return $check_validation;
				}
				
				if(!empty($check_validation['fields_values'])){
					$this->fields = array_merge($this->fields, $check_validation['fields_values']);
				}
			}
			
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
	
	public function orBy($where = array())
	{
		$this->__or_where = $where;
		return $this;
	}
	
	public function offChecker()
	{
		$this->_novalidation = true;
		return $this;
	}
	
	public function group($group_by)
	{
		$this->order_by = $group_by;
		return $this;
	}
	
	public function order($order_by)
	{
		$this->order_by = $order_by;
		return $this;
	}
	
	public function limit($limit)
	{
		$this->limit = $limit;
		return $this;
	}
	
	public function fetch_one()
	{
		$stmt = $this->_select();
		$getOne = $stmt->limit(1)->run()->fetch_object();
		if($getOne)
			return $getOne;
	}
	
	//If two parameters assigned it should use the fields overrider on select statement
	public function fetch()
	{
		$numargs = func_num_args();
		$arg_list = func_get_args();
		if($numargs > 0){
			if($numargs > 1){
				$stmt = $this->_select($arg_list[0]);
				return $arg_list[1]($stmt);
			}else{
				$stmt = $this->_select();
				return $arg_list[0]($stmt);
			}
		}else{
			
			$stmt = $this->_select();
			
			
			if($this->group_by){
				$stmt = $stmt->group_by($this->group_by);
			}
			
			if($this->order_by){
				$stmt = $stmt->order_by($this->order_by);
			}
			
			if($this->limit){
				$stmt = $stmt->limit($this->limit);
			}
			
			$getAll = $stmt->run();
			$rsdata = array();
			while($row = $getAll->fetch_object())
			{	
				$rsdata[] = $row;
			}
			
			return $rsdata;
		}
	}
	
	//UI Table Output
	public function ui_tbl($data, $total_rows=0)
	{
		return $this->__mcn->ui_tbl_setup($data, $total_rows);
	}
 }