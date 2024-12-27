<?php
//Earning & Charges controller
class Tl_Clr extends \Hrms_Clr
{
	public function load_before()
	{
		parent::load_before();
	}
	
	public function action_index()
	{
		
		$emp_q = $this->conn->select()->from('employees')
					->where('is_deleted', 0)
					->where('emp_type', '!=', 'Terminated')
					->order_by('first_name', 'ASC')
					->run();
		
		$_list = [];
		while($_emp = $emp_q->fetch_object()){
			$_list[] = $_emp;
		}
		
		$this->params['employees'] = $_list;
		
		$_resp['_main_content'] = $this
			->Ini()
			->View()
			->set_data($this->params)
			->get_template('hrms/time_logs/tl_list');
		
		$this->conn_init->close_conn();
		
		return json_encode($_resp);
	}
	//All get request like querying data fron database should place under this function
	public function action_get($ref=null)
	{
		if(!$this->Ini()->Action()->is_ajax()) throw new Exception('Invalid Request!');
		
		if($ref == 'list'){
			$page = $this->Ini()->Action()->GET()->param('page'); // get the requested page
			$limit = $this->Ini()->Action()->GET()->param('rows'); // get how many rows we want to have into the grid
			$sidx = $this->Ini()->Action()->GET()->param('sidx'); // get index row - i.e. user click to sort
			$sord = $this->Ini()->Action()->GET()->param('sord'); // get the direction
			if(!$sidx) $sidx =1;
			
			$date_start = $this->Ini()->Action()->GET()->param('date_start');
			$date_end = $this->Ini()->Action()->GET()->param('date_end');
			$option = $this->Ini()->Action()->GET()->param('option');
			
			
			$_s = $this->Ini()->Action()->GET()->param('s'); 
			
			
			$_add_where = "";
			
			$stmt = $this->conn
					->select(array('COUNT(*) AS count'))
					->from('employees_time_logs', 'tl')
					
					->inner_join('employees', 'emp')
						->on('emp.id', 'tl.emp_id')
						
					->left_join('employees_payroll', 'ep')
						->on('ep.id', 'tl.payroll_id')
					
					->left_join('users', 'login_by')
						->on('login_by.id', 'tl.log_in_by')
						
					->left_join('users', 'log_out_by')
						->on('log_out_by.id', 'tl.log_in_by')
					->left_join('users', 'ot_approved_by')
						->on('ot_approved_by.id', 'tl.ot_approved_by')
				;
				
			
			$_s = $stmt->escape_string($_s);
			
			if($_s){
				$_add_where = "
					(
						CONCAT(emp.first_name, ' ', emp.last_name, ' ', emp.middle_initial) LIKE '%{$_s}%'
					)
				";
				$stmt = $stmt->where($_add_where);
			}
			
			$stmt = $stmt->where('emp.is_deleted', 0);
			$stmt = $stmt->where('tl.is_deleted', 0);
			
			
			if($date_start && $date_end){
				$stmt = $stmt->where("CAST(time_in AS DATETIME) BETWEEN CAST('{$date_start}' AS DATETIME) AND CAST('{$date_end}' AS DATETIME)");
			}else if($date_start){
				$stmt = $stmt->where("CAST(time_in AS DATETIME) >= CAST('{$date_start}' AS DATETIME)");
			}else if($date_end){
				$stmt = $stmt->where("CAST(time_in AS DATETIME) <= CAST('{$date_end}' AS DATETIME)");
			}
			
			
			if($option){
				if($option == 'wp') $stmt = $stmt->where('tl.payroll_id', '>', 0);
				else if($option == 'wop') $stmt = $stmt->where('tl.payroll_id', 0);
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
						'tl.*',
						'emp.first_name',
						'emp.last_name',
						'emp.middle_initial',
						'emp.suffix',
						'login_by.username AS login_by_name',
						'log_out_by.username AS log_out_by_name',
						'ot_approved_by.username AS ot_approved_by_name',
						'ep.start_coverage_date',
						'ep.end_coverage_date',
					))
					->from('employees_time_logs', 'tl')
					->inner_join('employees', 'emp')
						->on('emp.id', 'tl.emp_id')
					
					->left_join('employees_payroll', 'ep')
						->on('ep.id', 'tl.payroll_id')
					
					->left_join('users', 'login_by')
						->on('login_by.id', 'tl.log_in_by')
					->left_join('users', 'log_out_by')
						->on('log_out_by.id', 'tl.log_out_by')
					->left_join('users', 'ot_approved_by')
						->on('ot_approved_by.id', 'tl.ot_approved_by')
				;
				
			
			$_s = $stmt->escape_string($_s);
			
			if($_s){
				$_add_where = "
					(
						CONCAT(emp.first_name, ' ', emp.last_name, ' ', emp.middle_initial) LIKE '%{$_s}%'
					)
				";
				$stmt = $stmt->where($_add_where);
			}
			
			$stmt = $stmt->where('emp.is_deleted', 0);
			$stmt = $stmt->where('tl.is_deleted', 0);
			
			if($date_start && $date_end){
				$stmt = $stmt->where("CAST(time_in AS DATETIME) BETWEEN CAST('{$date_start}' AS DATETIME) AND CAST('{$date_end}' AS DATETIME)");
			}else if($date_start){
				$stmt = $stmt->where("CAST(time_in AS DATETIME) >= CAST('{$date_start}' AS DATETIME)");
			}else if($date_end){
				$stmt = $stmt->where("CAST(time_in AS DATETIME) <= CAST('{$date_end}' AS DATETIME)");
			}
			
			
			
			if($option){
				if($option == 'wp') $stmt = $stmt->where('tl.payroll_id', '>', 0);
				else if($option == 'wop') $stmt = $stmt->where('tl.payroll_id', 0);
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
						->get_template('hrms/time_logs/tl_list_item');
			
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
			$tl_id = $post->params['tl_id'] ?? 0;
			
			$getEmp = $this->conn
					->select()
					->from('employees')
					->where('id', $post->params['emp_id'])
					->where('is_deleted', 0)
					->where('emp_type', '!=', 'Terminated')
					->run()->fetch_object();
			
			if(empty($getEmp)){
				throw new Exception('Employee not exist!');
			}
			
			$hrms_required_num_hrs_per_day = (!empty($getEmp->num_hours_per_day)) ? $getEmp->num_hours_per_day : $this->settings['hrms_required_num_hrs_per_day'];
			
			if(!$tl_id){
				$post->params['log_in_by'] = $this->current_user->id;
				$post->params['log_out_by'] = $this->current_user->id;
			}else{
				$getThisLog = $this->conn
					->select()
					->from('employees_time_logs')
					->where('id', $tl_id)
					->run()->fetch_object();
				
				if(empty($getThisLog->time_out)){
					$post->params['log_out_by'] = $this->current_user->id;
				}
			}
			
			$post->params['date_time_recorded'] = $this->global_mod->dateTime();
			
			$time_out = $post->params['time_out'];
					
			$in = $post->params['time_in'];
			$out = $time_out;
			
			$time_diff = (strtotime($out) - strtotime($in)) / 3600;
			
			$total_log = $time_diff;
			$total_ot_log = 0;
			if($time_diff > $hrms_required_num_hrs_per_day){
				$total_log = $hrms_required_num_hrs_per_day;
				$total_ot_log = $time_diff - $hrms_required_num_hrs_per_day;
			}
			
			$post->params['total_log'] = $total_log;
			$post->params['total_ot_log'] = $total_ot_log;
			
			
			unset($post->params['tl_id']);
			if($tl_id > 0){
				$this->conn->update('employees_time_logs')->set($post->params)->where('id', $tl_id)->run();
				$resp_id = $tl_id;
			}else{
				//Run insert function with data from form
				$resp_id = $this->conn->insert('employees_time_logs')->data($post->params)->run()->insert_id();
			}
			
			//close database connection
			$this->conn_init->close_conn();
			//Return response id
			return $resp_id;
		}else if($ref == 'approve'){
			$post = $this->Ini()->Action()->POST();
			$tl_id = $post->params['_ref_id'];
			$_val = $post->params['_val'];
			$this->conn->update('employees_time_logs')->set([
				'ot_approved' => $_val,
				'ot_approved_by' => ($_val) ? $this->current_user->id : 0,
				'ot_approved_on' => ($_val) ? $this->global_mod->dateTime() : null,
			])->where('id', $tl_id)->run();
			$this->conn_init->close_conn();
			return $tl_id;
		}else if($ref == 'delete'){
			$post = $this->Ini()->Action()->POST();
			$tl_id = $post->params['_ref_id'];
			$this->conn->update('employees_time_logs')->set([
				'is_deleted' => 1
			])->where('id', $tl_id)->run();
			$this->conn_init->close_conn();
			return $tl_id;
		}
	}
}