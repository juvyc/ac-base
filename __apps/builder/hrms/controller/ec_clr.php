<?php
//Earning & Charges controller
class Ec_Clr extends \Hrms_Clr
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
			->get_template('hrms/earnings_charges/ec_list');
		
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
			
			$type = $this->Ini()->Action()->GET()->param('type');
			$date_start = $this->Ini()->Action()->GET()->param('date_start');
			$date_end = $this->Ini()->Action()->GET()->param('date_end');
			$option = $this->Ini()->Action()->GET()->param('option');
			
			
			$_s = $this->Ini()->Action()->GET()->param('s'); 
			
			if(!$type) $type=false;
			
			$_add_where = "";
			
			$stmt = $this->conn
					->select(array('COUNT(*) AS count'))
					->from('employees_earns_charges', 'ec')
					->inner_join('employees', 'emp')
						->on('emp.id', 'ec.emp_id')
					->inner_join('users', 'user')
						->on('user.id', 'ec.added_by')
					
					->left_join('employees_payroll', 'ep')
						->on('ep.id', 'ec.payroll_id')
				;
				
			
			$_s = $stmt->escape_string($_s);
			
			if($_s){
				$_add_where = "
					(
						CONCAT(emp.first_name, ' ', emp.last_name, ' ', emp.middle_initial) LIKE '%{$_s}%'
						OR ec.description LIKE '%{$_s}%'
					)
				";
				$stmt = $stmt->where($_add_where);
			}
			
			$stmt = $stmt->where('emp.is_deleted', 0);
			$stmt = $stmt->where('ec.is_deleted', 0);
			
			if($date_start && $date_end){
				$stmt = $stmt->where("CAST(ec.date_time_added AS DATETIME) BETWEEN CAST('{$date_start}' AS DATETIME) AND CAST('{$date_end}' AS DATETIME)");
			}else if($date_start){
				$stmt = $stmt->where("CAST(ec.date_time_added AS DATETIME) >= CAST('{$date_start}' AS DATETIME)");
			}else if($date_end){
				$stmt = $stmt->where("CAST(ec.date_time_added AS DATETIME) <= CAST('{$date_end}' AS DATETIME)");
			}
			
			
			if($option){
				if($option == 'wp') $stmt = $stmt->where('ec.payroll_id', '>', 0);
				else if($option == 'wop') $stmt = $stmt->where('ec.payroll_id', 0);
			}
			
			
			
			if($type){
				$stmt = $stmt->where('ec.type', $type);
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
						'ec.*',
						'emp.first_name',
						'emp.last_name',
						'emp.middle_initial',
						'emp.suffix',
						'user.username',
						'ep.start_coverage_date',
						'ep.end_coverage_date',
					))
					->from('employees_earns_charges', 'ec')
					->inner_join('employees', 'emp')
						->on('emp.id', 'ec.emp_id')
					->inner_join('users', 'user')
						->on('user.id', 'ec.added_by')
					
					->left_join('employees_payroll', 'ep')
						->on('ep.id', 'ec.payroll_id')
				;
				
			
			$_s = $stmt->escape_string($_s);
			
			if($_s){
				$_add_where = "
					(
						CONCAT(emp.first_name, ' ', emp.last_name, ' ', emp.middle_initial) LIKE '%{$_s}%'
						OR ec.description LIKE '%{$_s}%'
					)
				";
				$stmt = $stmt->where($_add_where);
			}
			
			$stmt = $stmt->where('emp.is_deleted', 0);
			$stmt = $stmt->where('ec.is_deleted', 0);
			
			if($date_start && $date_end){
				$stmt = $stmt->where("CAST(ec.date_time_added AS DATETIME) BETWEEN CAST('{$date_start}' AS DATETIME) AND CAST('{$date_end}' AS DATETIME)");
			}else if($date_start){
				$stmt = $stmt->where("CAST(ec.date_time_added AS DATETIME) >= CAST('{$date_start}' AS DATETIME)");
			}else if($date_end){
				$stmt = $stmt->where("CAST(ec.date_time_added AS DATETIME) <= CAST('{$date_end}' AS DATETIME)");
			}
			
			
			if($option){
				if($option == 'wp') $stmt = $stmt->where('ec.payroll_id', '>', 0);
				else if($option == 'wop') $stmt = $stmt->where('ec.payroll_id', 0);
			}
			
			
			
			if($type){
				$stmt = $stmt->where('ec.type', $type);
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
						->get_template('hrms/earnings_charges/ec_list_item');
			
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
			
			$post->params['added_by'] = $this->current_user->id;
			$post->params['date_time_added'] = $post->params['date_time_added'] ?? $this->global_mod->dateTime();
			
			//Run insert function with data from form
			$insert_id = $this->conn->insert('employees_earns_charges')->data($post->params)->run()->insert_id();
			
			//close database connection
			$this->conn_init->close_conn();
			//Return response id
			return $insert_id;
		}else if($ref == 'delete'){
			$post = $this->Ini()->Action()->POST();
			$ec_id = $post->params['_ref_id'];
			$this->conn->update('employees_earns_charges')->set([
				'is_deleted' => 1
			])->where('id', $ec_id)->run();
			$this->conn_init->close_conn();
			return $ec_id;
		}
	}
}