<?php
//Earning & Charges controller
class Bs_Clr extends \Hrms_Clr
{
	public function load_before()
	{
		parent::load_before();
	}
	
	public function action_index()
	{
		
		$_inputs_data = [];
		
		$this->params['upsert_inputs'] = $this->forms->fields_builder($this->businessInfoFields(), [
			'group_values' => $_inputs_data
		]);
		
		$_resp['_main_content'] = $this
			->Ini()
			->View()
			->set_data($this->params)
			->get_template('hrms/bs/bs_list');
		
		$this->conn_init->close_conn();
		
		return json_encode($_resp);
	}
	
	private function businessInfoFields()
	{
		
		$query = $this->conn->select()
			->from('businesses', 'busi')
			->where('busi.is_deleted', 0)
			->order_by('busi.business_name')
			->run();
		
		$listsByLevels = [];
		while($row = $query->fetch_object()){
			$listsByLevels[$row->parent_id][] = [
				
			];
		}
		
		$_flds = [];
		
		$_flds[] = [
			'type' => 'hidden',
			'name' => 'ref_id',
			'value' => 0,
			'save' => false,
		];
		
		$_flds[] = array(
			'type' => 'group',
			'fields' => array(
				array(
					'type' => 'select',
					'gW' => 12,
					'lblClass' => 'col-sm-12 col-form-label',
					'fconClass' => 'col-sm-12',
					'name' => 'parent_id',
					'label' => "Parent",
					'required' => true,
					'opt_start' => [
						'text' => 'None',
						'value' => 0,
					],
					'options' => [],
				)
			)
		);
		
		
		$_flds[] = array(
			'type' => 'group',
			'fields' => array(
				array(
					'type' => 'text',
					'gW' => 12,
					'lblClass' => 'col-sm-12 col-form-label',
					'fconClass' => 'col-sm-12',
					'name' => 'business_name',
					'label' => "Business Name",
					'required' => true,
				)
			)
		);
		
		$_flds[] = array(
			'type' => 'group',
			'fields' => array(
				array(
					'type' => 'text',
					'gW' => 12,
					'lblClass' => 'col-sm-12 col-form-label',
					'fconClass' => 'col-sm-12',
					'name' => 'location',
					'label' => "Complete Address",
					'required' => true,
				)
			)
		);
		
		$_flds[] = array(
			'type' => 'group',
			'fields' => array(
				array(
					'type' => 'text',
					'gW' => 12,
					'lblClass' => 'col-sm-12 col-form-label',
					'fconClass' => 'col-sm-12',
					'name' => 'primary_contact_phone',
					'label' => "Contact Phone",
					'required' => true,
				)
			)
		);
		
		$_flds[] = array(
			'type' => 'group',
			'fields' => array(
				array(
					'type' => 'text',
					'gW' => 12,
					'lblClass' => 'col-sm-12 col-form-label',
					'fconClass' => 'col-sm-12',
					'name' => 'primary_contact_email',
					'label' => "Contact Email",
					'required' => true,
				)
			)
		);
		
		$_flds[] = array(
			'type' => 'group',
			'fields' => array(
				array(
					'type' => 'date',
					'gW' => 12,
					'lblClass' => 'col-sm-12 col-form-label',
					'fconClass' => 'col-sm-12',
					'name' => 'date_established',
					'label' => "Date Established",
					'required' => true,
				)
			)
		);
		
		return $_flds;
	}
	
	//All get request like querying data fron database should place under this function
	public function action_get($ref=null)
	{
		if(!$this->Ini()->Action()->is_ajax()) throw new Exception('Invalid Request!');
		
		if($ref == 'list'){
			
			$page = $this->Ini()->Action()->GET()->param('p'); // get the requested page
			//$limit = $this->Ini()->Action()->GET()->param('rows'); // get how many rows we want to have into the grid
			//$sidx = $this->Ini()->Action()->GET()->param('sidx'); // get index row - i.e. user click to sort
			//$sord = $this->Ini()->Action()->GET()->param('sord'); // get the direction
			//if(!$sidx) $sidx =1;
			
			$hrmsModel = $this->Ini()->Mod('hrms');
			
			$busiModel = $hrmsModel
				->get_data('businesses')
				->withRelations()
				->by([
					'{base_table}.is_deleted' => 0
				]);
			
			//Count all rows in database
			$busiCounter = $busiModel->_select(['COUNT(`{base_table}`.id) AS num_rows'])
				->run()->fetch_object();
				
			//Get the existing total number of records
			$num_rows = $busiCounter->num_rows;
			
			//Query limit should be the same with the table UI limit
			$limit = 2;
			
			//off set
			$start = ($num_rows > 0) ? ($limit * $page - $limit) : 0; 
			
			//Get data from db
			$busiData = $busiModel->_select()
				->limit($start, $limit)
				->run();
			
			$resp = $busiModel->ui_tbl($busiData, $num_rows);
			
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
		
		if($ref == 'record'){ //record action means update or insert
			//Initialize POST data from form into a single variatble
			$post = $this->Ini()->Action()->POST();
			$ref_id = $post->params['ref_id'] ?? 0;
			
			//Get the fields set on the form
			$formFields = $this->businessInfoFields();
			
			/*
			$this->forms
					->ready_fields($formFields, $post->params);
			
			print_r($this->forms->ready_fields_list);
			
			exit;
			*/
			
			if($ref_id > 0){
				$is_updated = $this->forms
					->ready_fields($formFields, $post->params)
					->final_save('businesses', ['id' => $ref_id]);
				
				return $ref_id;
			}else{
				//Run insert function with data from form
				$resp_id = $this->forms
					->ready_fields($formFields, $post->params)
					->final_save('businesses');
			}
			
			//close database connection
			$this->conn_init->close_conn();
			//Return response id
			return $resp_id;
		}else if($ref == 'delete'){ //delete action will set the is_deleted field to 1
			$post = $this->Ini()->Action()->POST();
			$ref_id = $post->params['ref_id'];
			
			$this->forms
				->ready_fields([], ['is_deleted' => 1])
				->final_save('businesses', ['id' => $ref_id]);
			
			$this->conn_init->close_conn();
			
			return $ref_id;
		}
	}
}