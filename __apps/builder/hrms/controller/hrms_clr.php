<?php

class Hrms_Clr extends \Base_System
{
 
	protected $params = array();

	protected $conn_init;
	protected $conn;
	protected $global_mod, $upsert_log;
	protected $current_user;
	protected $settings;
	protected $forms;
 
	//Initializer of this class controller
	public function load_before()
	{
		$this->params = array();
		
		$this->conn_init = $this->Ini()->DB();
		$this->conn = $this->conn_init->exec();
		
		$this->global_mod =  $this->Ini()->Mod('common')->load('global');
		$this->upsert_log =  $this->Ini()->Mod('common')->load('upsertlog');
		
		$this->global_mod->conn = $this->conn;
		
		$this->current_user = $this->global_mod->_getCurrentUserInfo();
		
		$this->settings =  $this->global_mod->settings();
		
		$this->params['settings'] = $this->settings;
		
		$this->params['current_user'] = $this->current_user;
		
		$this->forms = $this->Ini()->Helper('forms')->load()->call();
		
		//Run sync to pass the current user data to forms class
		$this->forms->sync();
	}

	public function action_index()
	{
		$curr_year = $this->global_mod->dateTime('Y');
		$this->params['curr_year'] = $curr_year;
		
		
		$employees_numbers = $this->conn
			->select([
				'COUNT(*) AS employees_numbers'
			])
				->from('employees', 'emp')
					->where('emp.is_deleted', 0)
					->run()
				->fetch_object();
		
		$this->params['employees_numbers'] = $employees_numbers->employees_numbers ?? 0;
		
		$time_logs_counter = $this->conn
			->select([
				'COUNT(*) AS time_logs_counter'
			])
				->from('employees_time_logs', 'tl')
					->where('tl.is_deleted', 0)
					->run()
				->fetch_object();
		
		$this->params['time_logs_counter'] = $time_logs_counter->time_logs_counter ?? 0;
		
		$payroll_created_counter = $this->conn
			->select([
				'COUNT(*) AS payroll_created_counter',
				'SUM(ep.total_net) AS payroll_net_total',
				'SUM(ep.total_tax) AS payroll_tax_total',
			])
				->from('employees_payroll', 'ep')
					->where('ep.is_deleted', 0)
					->run()
				->fetch_object();
		
		$this->params['payroll_created_counter'] = $payroll_created_counter->payroll_created_counter ?? 0;
		$this->params['payroll_net_total'] = $payroll_created_counter->payroll_net_total ?? 0;
		$this->params['payroll_tax_total'] = $payroll_created_counter->payroll_tax_total ?? 0;
		
		
		$paid_regular_hours_total = $this->conn
			->select([
				'SUM(tl.total_log) AS paid_regular_hours_total'
			])
				->from('employees_time_logs', 'tl')
					->where('tl.is_deleted', 0)
					->where('tl.payroll_id', '>', 1)
					->run()
				->fetch_object();
		
		$this->params['paid_regular_hours_total'] = $paid_regular_hours_total->paid_regular_hours_total ?? 0;
		
		
		$unpaid_regular_hours_total = $this->conn
			->select([
				'SUM(tl.total_log) AS unpaid_regular_hours_total'
			])
				->from('employees_time_logs', 'tl')
					->where('tl.is_deleted', 0)
					->where('tl.payroll_id', 0)
					->run()
				->fetch_object();
		
		$this->params['unpaid_regular_hours_total'] = $unpaid_regular_hours_total->unpaid_regular_hours_total ?? 0;
		
		$paid_ot_hours_total = $this->conn
			->select([
				'SUM(tl.total_ot_log) AS paid_ot_hours_total'
			])
				->from('employees_time_logs', 'tl')
					->where('tl.is_deleted', 0)
					->where('tl.ot_approved', '>', 0)
					->where('tl.payroll_id', '>', 0)
					->run()
				->fetch_object();
		
		$this->params['paid_ot_hours_total'] = $paid_ot_hours_total->paid_ot_hours_total ?? 0;
		
		$unpaid_ot_hours_total = $this->conn
			->select([
				'SUM(tl.total_ot_log) AS unpaid_ot_hours_total'
			])
				->from('employees_time_logs', 'tl')
					->where('tl.is_deleted', 0)
					->where('tl.ot_approved', '>', 0)
					->where('tl.payroll_id', 0)
					->run()
				->fetch_object();
		
		$this->params['unpaid_ot_hours_total'] = $unpaid_ot_hours_total->unpaid_ot_hours_total ?? 0;
		
		$_q_paid_earnings_charges = $this->conn->select([
						"SUM(IF(type = 'E', ec.amount, 0)) AS total_paid_earnings",
						"SUM(IF(type = 'C', ec.amount, 0)) AS total_paid_charges",
					])
					->from('employees_earns_charges', 'ec')
					->where('ec.is_deleted', 0)
					->where('ec.payroll_id', '>', 0)
					->run()->fetch_object();
					
		$this->params['paid_earnings_charges'] = $_q_paid_earnings_charges;
		
		$_unpaid_earnings_charges = $this->conn->select([
						"SUM(IF(type = 'E', ec.amount, 0)) AS total_unpaid_earnings",
						"SUM(IF(type = 'C', ec.amount, 0)) AS total_unpaid_charges",
					])
					->from('employees_earns_charges', 'ec')
					->where('ec.is_deleted', 0)
					->where('ec.payroll_id', 0)
					->run()->fetch_object();
		
		$this->params['unpaid_earnings_charges'] = $_unpaid_earnings_charges;
		
		$_qstmt = $this->conn->select([
						'DATE_FORMAT(ep.date_processed, "%Y-%M") AS group_id',
						'SUM(total_net) AS total_net'
					])
					->from('employees_payroll', 'ep')
					->where('ep.is_deleted', 0)
					->where("DATE_FORMAT(ep.date_processed, '%Y') = '{$curr_year}'")
					->group_by('DATE_FORMAT(ep.date_processed, "%Y-%M")')
					->run();
		$monthly_payroll = [];
		if($_qstmt->num_rows()){
			while($_r = $_qstmt->fetch_object()){
				$monthly_payroll[$_r->group_id] = $_r->total_net;
			}
		}
		
		
		$payroll_monthly_data = [];
		for ($m=1; $m<=12; $m++){
		 $month_n = date('F', mktime(0,0,0,$m, 1, date('Y')));
		 $payroll_monthly_data[] = [
			'month_name' => $month_n,
			'value' => $monthly_payroll[$curr_year . '-' . $month_n] ?? 0,
		 ];
		}
		
		$this->params['payroll_monthly_data'] = $payroll_monthly_data;
		
		$this->conn_init->close_conn();
		
		if($this->Ini()->Action()->is_ajax()){
			
			$_resp['_main_content'] = $this
									->Ini()
									->View()
									->set_data($this->params)
									->get_template('hrms/dashboard');
			
			return json_encode($_resp);
		}
		
		return $this
				->Ini()
				->View()
				->set_data($this->params)
				->use_prepared('content', 'hrms/dashboard');
	}
	
}