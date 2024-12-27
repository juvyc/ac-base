<?php
 /**
 * @Businesses_Mod
 * Type: Object
 * Desc: Common functionality that accessible to any builder
 */
 
 class Businesses_Mod extends \Base_System
 {
	//database table name
	public $_table = "businesses";
	//database table field primary key
	public $_primary_key = "id";
	//database table fields
	public $_fields = array(
		'id',
		'business_name',
		'location',
		'primary_contact_phone',
		'primary_contact_email',
		'is_deleted',
		'is_inactive',
		'date_established',
		'parent_id',
	);
	
	public $join_left = array(
		[
			'businesses' => [
				'alias' => 'parent', 
				'fields' => [
					'`{alias}`.business_name AS parent_business_name',
					'`{alias}`.location AS parent_location'
				], 
				
				'relations' => [
					"`{base_table}`.parent_id = `{alias}`.id"
				]
			]
		]
	);
	
	
	//Whether to show or hide the pagination
	public $ui_tbl_pagination = true;
	
	//If the data is from the query
	public $ui_tbl_while_db = true;
	
	//Show table UI head or not
	public $ui_tbl_head = true;
	
	//Show table UI foot or not
	public $ui_tbl_foot = false;
	
	//Limit of records to show up
	public $ui_tbl_limit = 2;
	
	//List of fields to show on the table lists
	public function ui_tbl_view() : array
	{
		return [
			'id' => '#',
			'business_name' => 'Business Name',
			'parent_id' => [
				'lbl' => 'Parent',
				'sf' => function($d, $fn){
					return ($d[$fn] == 0) ? 'None' : $d['parent_name'];
				},
			],
			'location' => 'Address',
			'primary_contact_phone' => 'Phone #',
			'primary_contact_email' => 'Email',
			'date_established' => 'Date Established',
			"is_inactive" => [
				"lbl" 	=> 'Is Active?',
				"sf" 	=> function($d, $fn){
					return $d[$fn] == 1 ? 'No' : 'Yes';
				}
			],
			
			//extra column for actions
			'action' => [
				'lbl' => '...',
				'sf' => function($d, $fn){
					return '';
				}
			]
		];
	}
	
	//Setup UI table this is connected to the ui_table generator of model controller
	public function ui_tbl_setup($data, $total_rows=0)
	{	
		$ui_tbl = $this->Ini()->Helper('ui_tbl')->load()->call();
		return $ui_tbl->build($this, $data, $total_rows);
	}
 }