<?php

class Payroll_Clr extends \Hrms_Clr
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
			->get_template('hrms/payroll/payroll_list');
		
		
		$this->conn_init->close_conn();
		
		return json_encode($_resp);
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
			
			
			$date_start = $this->Ini()->Action()->GET()->param('date_start');
			$date_end = $this->Ini()->Action()->GET()->param('date_end');
			
			
			$_s = $this->Ini()->Action()->GET()->param('s'); 
			
			
			$_add_where = "";
			
			$stmt = $this->conn
					->select(array('COUNT(*) AS count'))
					
					->from('employees_payroll', 'ep')
					->inner_join('employees', 'emp')
						->on('emp.id', 'ep.emp_id')
					->inner_join('users', 'user')
						->on('user.id', 'ep.processed_by')
				;
				
			
			$_s = $stmt->escape_string($_s);
			
			if($_s){
				$_add_where = "
					(
						CONCAT(emp.first_name, ' ', emp.last_name, ' ', emp.middle_initial) LIKE '%{$_s}%'
						OR ep.position LIKE '%{$_s}%'
					)
				";
				$stmt = $stmt->where($_add_where);
			}
			
			$stmt = $stmt->where('ep.is_deleted', 0);
			
			if($date_start && $date_end){
				$stmt = $stmt->where("CAST(ep.start_coverage_date AS DATETIME) BETWEEN CAST('{$date_start}' AS DATETIME) AND CAST('{$date_end}' AS DATETIME)");
			}else if($date_start){
				$stmt = $stmt->where("CAST(ep.start_coverage_date AS DATETIME) >= CAST('{$date_start}' AS DATETIME)");
			}else if($date_end){
				$stmt = $stmt->where("CAST(ep.start_coverage_date AS DATETIME) <= CAST('{$date_end}' AS DATETIME)");
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
						'ep.*',
						'emp.first_name',
						'emp.middle_initial',
						'emp.last_name',
						'emp.suffix',
						'user.username'
					))
					->from('employees_payroll', 'ep')
					->inner_join('employees', 'emp')
						->on('emp.id', 'ep.emp_id')
					->inner_join('users', 'user')
						->on('user.id', 'ep.processed_by')
				;
				
			
			if($_s){
				$_add_where = "
					(
						CONCAT(emp.first_name, ' ', emp.last_name, ' ', emp.middle_initial) LIKE '%{$_s}%'
						OR ep.position LIKE '%{$_s}%'
					)
				";
				$stmt = $stmt->where($_add_where);
			}
			
			$stmt = $stmt->where('ep.is_deleted', 0);
			
			if($date_start && $date_end){
				$stmt = $stmt->where("CAST(ep.start_coverage_date AS DATETIME) BETWEEN CAST('{$date_start}' AS DATETIME) AND CAST('{$date_end}' AS DATETIME)");
			}else if($date_start){
				$stmt = $stmt->where("CAST(ep.start_coverage_date AS DATETIME) >= CAST('{$date_start}' AS DATETIME)");
			}else if($date_end){
				$stmt = $stmt->where("CAST(ep.start_coverage_date AS DATETIME) <= CAST('{$date_end}' AS DATETIME)");
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
						->get_template('hrms/payroll/payroll_list_item');
			
			$this->conn_init->close_conn();
			
			return $_resp;
		}else if($ref == 'payroll-calc'){
			
			$rsp_data = [];
			
			$form_get = $this->Ini()->Action()->GET();
			
			$stmt = $this->conn->select([
					'SUM(total_log) AS total_log'
				])
				->from('employees_time_logs', 'etl')
				->where('payroll_id', 0)
				->where("time_out IS NOT NULL")
				->where('is_deleted', 0)
				->where('emp_id', $form_get->param('emp_id'))
			;
			
			$stmt = $stmt->where("CAST(time_in AS DATETIME) BETWEEN CAST('{$form_get->param('start_coverage_date')}' AS DATETIME) AND CAST('{$form_get->param('end_coverage_date')}' AS DATETIME)");
			$stmt = $stmt->run()->fetch_object();
			
			
			$rsp_data['total_log'] = $stmt->total_log;
			
			
			$stmt = $this->conn->select([
					'SUM(total_ot_log) AS total_ot_log'
				])
				->from('employees_time_logs', 'etl')
				->where('payroll_id', 0)
				->where('is_deleted', 0)
				->where('ot_approved', '>', 0)
				->where('emp_id', $form_get->param('emp_id'))
			;
			
			$stmt = $stmt->where("CAST(time_in AS DATETIME) BETWEEN CAST('{$form_get->param('start_coverage_date')}' AS DATETIME) AND CAST('{$form_get->param('end_coverage_date')}' AS DATETIME)");
			$stmt = $stmt->run()->fetch_object();
			
			$rsp_data['total_ot_log'] = $stmt->total_ot_log;
			
			
			$stmt = $this->conn->select([
					'SUM(amount) AS total_other_charges'
				])
				->from('employees_earns_charges', 'ec')
				->where('payroll_id', 0)
				->where('type', 'C')
				->where('is_deleted', 0)
				->where('emp_id', $form_get->param('emp_id'))
			;
			
			//$stmt = $stmt->where("CAST(date_time_added AS DATETIME) BETWEEN CAST('{$form_get->param('start_coverage_date')}' AS DATETIME) AND CAST('{$form_get->param('end_coverage_date')}' AS DATETIME)");
			$stmt = $stmt->run()->fetch_object();
			
			$rsp_data['total_other_charges'] = $stmt->total_other_charges;
			
			$stmt = $this->conn->select([
					'SUM(amount) AS total_earnings'
				])
				->from('employees_earns_charges', 'ee')
				->where('payroll_id', 0)
				->where('type', 'E')
				->where('is_deleted', 0)
				->where('emp_id', $form_get->param('emp_id'))
			;
			
			//$stmt = $stmt->where("CAST(date_time_added AS DATETIME) BETWEEN CAST('{$form_get->param('start_coverage_date')}' AS DATETIME) AND CAST('{$form_get->param('end_coverage_date')}' AS DATETIME)");
			$stmt = $stmt->run()->fetch_object();
			
			$rsp_data['total_earnings'] = $stmt->total_earnings;
			
			$stmt = $this->conn->select([
					'SUM(amount) AS total_earnings_non_tax'
				])
				->from('employees_earns_charges', 'ee')
				->where('payroll_id', 0)
				->where('type', 'E')
				->where('is_deleted', 0)
				->where('is_taxable', 0)
				->where('emp_id', $form_get->param('emp_id'))
			;
			
			//$stmt = $stmt->where("CAST(date_time_added AS DATETIME) BETWEEN CAST('{$form_get->param('start_coverage_date')}' AS DATETIME) AND CAST('{$form_get->param('end_coverage_date')}' AS DATETIME)");
			$stmt = $stmt->run()->fetch_object();
			
			$rsp_data['total_earnings_non_tax'] = $stmt->total_earnings_non_tax;
			
			
			$stmt = $this->conn->select()
				->from('employees', 'emp')
				->where('id', $form_get->param('emp_id'))
				->run()->fetch_object();
			
			$rsp_data['employee_data'] = $stmt;
			
			if($this->Ini()->Action()->GET()->param('do') == 'save'){ //Do save if $_is_save_action is true
				$total_hrs  = ($rsp_data['total_log'] ?? 0) + ($rsp_data['total_ot_log'] ?? 0);
				$total_other_charges = $rsp_data['total_other_charges'] ?? 0;
				$total_earnings = $rsp_data['total_earnings'] ?? 0;
				$total_earnings_non_tax = $rsp_data['total_earnings_non_tax'] ?? 0;
				
				$subtotal = ($total_hrs * $rsp_data['employee_data']->rate) + $total_earnings;
				
				$total_gross = $subtotal;
				
				$subtotal = $subtotal - $total_other_charges;
				
				
				$total_deduction = $total_other_charges;
				
				if(!empty($rsp_data['employee_data']->common_charges)){
					$common_charges = json_decode($rsp_data['employee_data']->common_charges, true);
					if(!empty($common_charges)){
						foreach($common_charges as $lc){
							if($lc['integ_type'] == 'Fixed'){
								$subtotal = $subtotal - (float) $lc['val'];
								$total_deduction = $total_deduction + (float) $lc['val'];
							}else if($lc['integ_type'] == 'Percentage'){
								$line_charge = ($subtotal * ((float) $lc['val'] / 100));
								$subtotal = $subtotal - $line_charge;
								$total_deduction = $total_deduction + $line_charge;
							}
						}
					}
				}
				
				$_total_tax = 0;
				if($rsp_data['employee_data']->tax_rate){
					$_total_tax = (($subtotal - $total_earnings_non_tax) * ((float) $rsp_data['employee_data']->tax_rate / 100));
					$subtotal = $subtotal - $_total_tax;
				}
				
				$net_total = $subtotal;
				
				$data = [
					'emp_id' => $form_get->param('emp_id'),
					'total_hrs' => $total_hrs,
					'total_ot_hrs' => $rsp_data['total_ot_log'],
					'total_other_earnings' => $total_earnings,
					'total_gross' => $total_gross,
					'total_deduction' => $total_deduction,
					'total_no_tax' => $total_earnings_non_tax,
					'total_tax' => $_total_tax,
					'total_net' => $net_total,
					'position' => $rsp_data['employee_data']->position,
					'rate' => $rsp_data['employee_data']->rate,
					'ot_rate' => $rsp_data['employee_data']->ot_rate,
					'tax_rate' => $rsp_data['employee_data']->tax_rate,
					'start_coverage_date' => $form_get->param('start_coverage_date'),
					'end_coverage_date' => $form_get->param('end_coverage_date'),
					'processed_by' => $this->current_user->id,
					'date_processed' => $this->global_mod->dateTime(),
				];
				
				$payroll_id = $this->conn->insert('employees_payroll')->data($data)->run()->insert_id();
				
				if($payroll_id){
					$this->conn->update('employees_time_logs')
						->set([
							'payroll_id' => $payroll_id
						])
						->where('payroll_id', 0)
						->where('is_deleted', 0)
						->where("time_out IS NOT NULL")
						->where('emp_id', $form_get->param('emp_id'))
						->where("CAST(time_in AS DATETIME) BETWEEN CAST('{$form_get->param('start_coverage_date')}' AS DATETIME) AND CAST('{$form_get->param('end_coverage_date')}' AS DATETIME)")
					->run();
					
					$this->conn->update('employees_earns_charges')
						->set([
							'payroll_id' => $payroll_id
						])
						->where('payroll_id', 0)
						->where('is_deleted', 0)
						->where('emp_id', $form_get->param('emp_id'))
						->where("CAST(date_time_added AS DATETIME) BETWEEN CAST('{$form_get->param('start_coverage_date')}' AS DATETIME) AND CAST('{$form_get->param('end_coverage_date')}' AS DATETIME)")
					->run();
					
					if($net_total < 0){ // if the result is negative
						$this->conn->insert('employees_earns_charges')
							->data([
								'emp_id' => $form_get->param('emp_id'),
								'type' => 'C',
								'description' => 'Balance after this payroll #' . $payroll_id . ' (' . $form_get->param('start_coverage_date') . ' - ' . $form_get->param('end_coverage_date') . ')',
								'amount' => abs($net_total),
								'balance_ref_id' => $payroll_id,
								'added_by' => $this->current_user->id,
								'date_time_added' => $this->global_mod->dateTime(),
							])->run();
					}
					
					$rsp_data = 1;
					
				}
			}
			
			$this->conn_init->close_conn();
			
			return json_encode($rsp_data);
		}else if($ref == 'info'){
			$getPayrollInfo = $this->conn->select([
					'ep.*',
					'emp.first_name',
					'emp.last_name',
					'emp.middle_initial',
					'emp.suffix',
					'emp.common_charges',
				])
				->from('employees_payroll', 'ep')
					->inner_join('employees', 'emp')
						->on('emp.id', 'ep.emp_id')
				->where('ep.id', $id)
			->run()->fetch_object();
			
			if(!empty($getPayrollInfo)){
				$getExtraEarningsCharges = $this->conn->select()
					->from('employees_earns_charges', 'ec')
						->where('ec.payroll_id', $id)
					->run();
				$list_earnings = [];
				$list_charges = [];
				while($r = $getExtraEarningsCharges->fetch_object()){
					if($r->type == 'E'){
						$list_earnings[] = $r;
					}else{
						$list_charges[] = $r;
					}
				}
				
				$getPayrollInfo->list_charges = $list_charges;
				$getPayrollInfo->list_earnings = $list_earnings;
			}
			
			$resp = (!empty($getPayrollInfo)) ? json_encode($getPayrollInfo) : -1;
			
			$this->conn_init->close_conn();
			
			return $resp;
		}
		
		return;
	}
	
	//All form post action should go here
	public function action_do($ref=null)
	{
		//Only allow ajax requests
		if(!$this->Ini()->Action()->is_ajax()) throw new Exception('Invalid Request!');
		
		if($ref == 'delete'){
			$post = $this->Ini()->Action()->POST();
			$ep_id = $post->params['_ref_id'];
			$this->conn->update('employees_payroll')->set([
				'is_deleted' => 1
			])->where('id', $ep_id)->run();
			
			$this->conn->update('employees_earns_charges')->set([
				'payroll_id' => 0
			])->where('payroll_id', $ep_id)
			->run();
			
			$this->conn->update('employees_earns_charges')->set([
				'is_deleted' => 1
			])->where('balance_ref_id', $ep_id)
			->run();
			
			$this->conn->update('employees_time_logs')->set([
				'payroll_id' => 0
			])->where('payroll_id', $ep_id)->run();
			
			$this->conn_init->close_conn();
			
			return $ep_id;
		}
	}
}