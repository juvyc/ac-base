<?php

class Employee_Clr extends \Hrms_Clr
{
	public function load_before()
	{
		parent::load_before();
	}
	
	public function action_index()
	{
		
		$_deduction_tpl = $this
							->Ini()
							->View()
							->set_data($this->params)
							->get_template('hrms/employee/deduction');
		
		$this->params['_deduction_tpl'] = base64_encode($_deduction_tpl);
		
		$this->conn_init->close_conn();
		
		if($this->Ini()->Action()->is_ajax()){
			
			$_resp['_main_content'] = $this
				->Ini()
				->View()
				->set_data($this->params)
				->get_template('hrms/employee/employee_list');
			
			

			return json_encode($_resp);
		}
		
		return $this
				->Ini()
				->View()
				->set_data($this->params)
				->use_prepared('content', 'hrms/employee/employee_list');
		
	}
	//All get request like querying data fron database should place under this function
	public function action_get($ref=null, $id=0)
	{
		if($ref == 'list'){
			$page = $this->Ini()->Action()->GET()->param('page'); // get the requested page
			$limit = $this->Ini()->Action()->GET()->param('rows'); // get how many rows we want to have into the grid
			$sidx = $this->Ini()->Action()->GET()->param('sidx'); // get index row - i.e. user click to sort
			$sord = $this->Ini()->Action()->GET()->param('sord'); // get the direction
			if(!$sidx) $sidx =1;
			
			$type = $this->Ini()->Action()->GET()->param('emp_type');
			
			
			$_s = $this->Ini()->Action()->GET()->param('s'); 
			
			if(!$type) $type=false;
			
			$_add_where = "";
			
			$stmt = $this->conn
					->select(array('COUNT(*) AS count'))
					->from('employees', 'emp')
					->inner_join('users', 'user')
						->on('user.id', 'emp.added_by')
				;
				
			
			$_s = $stmt->escape_string($_s);
			
			if($_s){
				$_add_where = "
					(
						CONCAT(emp.first_name, ' ', emp.last_name, ' ', emp.middle_initial) LIKE '%{$_s}%'
						OR emp.address LIKE '%{$_s}%'
						OR emp.email LIKE '%{$_s}%'
						OR emp.phone LIKE '%{$_s}%'
						OR emp.position LIKE '%{$_s}%'
					)
				";
				$stmt = $stmt->where($_add_where);
			}
			
			$stmt = $stmt->where('emp.is_deleted', 0);
			
			
			if($type){
				$stmt = $stmt->where('emp.emp_type', $type);
			}
			
			
			$stmt = $stmt->run();
			
			$row = $stmt->fetch_object();
			
			$count = $row->count;
			
			if( $count > 0 ) {
				$total_pages = ceil($count/$limit);
				
				if ($page > $total_pages) $page=$total_pages;
				$start = $limit * $page - $limit; 
			} else {
				$total_pages = 0;
				$start = 0;
			}
			
			$this->params['total_pages'] = $total_pages;
			
			$stmt = $this->conn
					->select(array(
						'emp.*',
						'user.username'
					))
					->from('employees', 'emp')
					->inner_join('users', 'user')
						->on('user.id', 'emp.added_by')
				;
				
			
			if($_s){
				$_add_where = "
					(
						CONCAT(emp.first_name, ' ', emp.last_name, ' ', emp.middle_initial) LIKE '%{$_s}%'
						OR emp.address LIKE '%{$_s}%'
						OR emp.email LIKE '%{$_s}%'
						OR emp.phone LIKE '%{$_s}%'
						OR emp.position LIKE '%{$_s}%'
					)
				";
				$stmt = $stmt->where($_add_where);
			}
			
			$stmt = $stmt->where('emp.is_deleted', 0);
			
			
			if($type){
				$stmt = $stmt->where('emp.emp_type', $type);
			}
			
					
			$stmt = $stmt->order_by("$sidx $sord")
					->limit($start, $limit);
			
			//echo $stmt->_debug();exit;
			
			$stmt = $stmt->run();
			
			$this->params['list_items'] = $stmt;
			
			$_resp =  $this
						->Ini()
						->View()
						->set_data($this->params)
						->get_template('hrms/employee/employee_list_item');
			
			$this->conn_init->close_conn();
			
			return $_resp;
		}else if($ref == 'edit' && $id > 0){
			$getEmpData = $this->conn->select()
					->from('employees')
					->where('id', $id)
					->run()->fetch_object();
				;
				
			if(empty($getEmpData)) return;
			
			$_deduction_tpl = $this
							->Ini()
							->View()
							->set_data($this->params)
							->get_template('hrms/employee/deduction');
		
			$this->params['_deduction_tpl'] = base64_encode($_deduction_tpl);
			
			$this->params['emp_data'] = $getEmpData;
			
			$_resp =  $this
						->Ini()
						->View()
						->set_data($this->params)
						->get_template('hrms/employee/employee_form');
			
			$this->conn_init->close_conn();
			
			return $_resp;
		}
		
		return;
	}
	
	//All form post action should go here
	public function action_do($ref=null)
	{
		//Only allow ajax requests
		if(!$this->Ini()->Action()->is_ajax()) throw new Exception('Invalid Request!');
		
		if($ref == 'new'){
			//Initialize POST data from form into a single variatble
			$post = $this->Ini()->Action()->POST();
			//Run insert function with data from form
			
			
			$deductions = [];
			
			if(!empty($post->params['deduction']['label'])){
				foreach($post->params['deduction']['label'] as $_k => $lbl){
					if(!$lbl) continue;
					
					$deductions[] = [
						'label' => $lbl,
						'integ_type' => $post->params['deduction']['integ_type'][$_k],
						'val' => $post->params['deduction']['val'][$_k],
					];
				}
			}
			
			$data = [
				'first_name' => $post->param('first_name'),
				'last_name' => $post->param('last_name'),
				'middle_initial' => $post->param('middle_name'),
				'suffix' => $post->param('suffix_name'),
				'address' => $post->param('complete_address'),
				'email' => $post->param('email'),
				'phone' => $post->param('phone'),
				'sex' => $post->param('sex'),
				'marital_status' => $post->param('marital_status'),
				'position' => $post->param('emp_position'),
				'emp_type' => $post->param('emp_type'),
				'rate' => $post->param('rate'),
				'num_hours_per_day' => $post->param('num_hours_per_day'),
				'ot_rate' =>  $post->param('ot_rate'),
				'added_by' => $this->current_user->id,
				'date_time_added' => $this->global_mod->dateTime(),
				'tax_rate' => $post->param('tax_rate'),
				'birthdate' => $post->param('birthdate'),
				'date_hired' => $post->param('date_hired'),
			];
			
			if(count($deductions)){
				$data['common_charges'] = json_encode($deductions);
			}
			
			if($post->param('time_in')){
				$data['c_time_in'] = $post->param('time_in');
			}
			
			$insert_id = $this->conn->insert('employees')->data($data)->run()->insert_id();
			
			//close database connection
			$this->conn_init->close_conn();
			//Return response id
			return $insert_id;
		}else if($ref == 'edit'){
			//Initialize POST data from form into a single variatble
			$post = $this->Ini()->Action()->POST();
			//Run insert function with data from form
			
			$_emp_id = $post->param('emp_id');
			if(!$_emp_id) return;
			
			$deductions = [];
			
			if(!empty($post->params['deduction']['label'])){
				foreach($post->params['deduction']['label'] as $_k => $lbl){
					if(!$lbl) continue;
					
					$deductions[] = [
						'label' => $lbl,
						'integ_type' => $post->params['deduction']['integ_type'][$_k],
						'val' => $post->params['deduction']['val'][$_k],
					];
				}
			}
			
			$data = [
				'first_name' => $post->param('first_name'),
				'last_name' => $post->param('last_name'),
				'middle_initial' => $post->param('middle_name'),
				'suffix' => $post->param('suffix_name'),
				'address' => $post->param('complete_address'),
				'email' => $post->param('email'),
				'phone' => $post->param('phone'),
				'sex' => $post->param('sex'),
				'marital_status' => $post->param('marital_status'),
				'position' => $post->param('emp_position'),
				'emp_type' => $post->param('emp_type'),
				'rate' => $post->param('rate'),
				'num_hours_per_day' => $post->param('num_hours_per_day'),
				'ot_rate' =>  $post->param('ot_rate'),
				'tax_rate' => $post->param('tax_rate'),
				'birthdate' => $post->param('birthdate'),
				'date_hired' => $post->param('date_hired'),
			];
			
			if(count($deductions)){
				$data['common_charges'] = json_encode($deductions);
			}
			
			if($post->param('time_in')){
				$data['c_time_in'] = $post->param('time_in');
			}
			
			$this->conn->update('employees')->set($data)->where('id', $_emp_id)->run();
			
			//close database connection
			$this->conn_init->close_conn();
			//Return response id
			return $_emp_id;
		}
	}
}