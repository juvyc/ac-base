<?php
 /**
 * @Users_Mod
 * Type: Object
 * Desc: It handle all database related processes exclusive to user controller
 */
 
 class Profiles_Mod extends \Test_Clr
 {
	public $_table = "profiles";
	public $_primary_key = "id";
	public $_fields = array(
		'id',
		'first_name',
		'last_name',
		'middle_name',
	);
	
	
	public function validate($type, $fields_values)
	{
		$_err_message = [];
		if(empty($fields_values['first_name'])){
			$_err_message[] = 'Username should not be empty!';
		}
		
		return [
			'success' => empty($_err_message) ? true : false,
			'message' => implode(PHP_EOL, $_err_message),
		];
	}
	
	//for INNER JOIN MODEL settings
	
			
	/**
	'conditions' => [
		["{$this->_table}.profile_id", '!=', "profile.id"]
	]
	*/
	/**
	public $join_left = array(
		[
			'profiles' => [
				'alias' => 'profile', 
				'fields' => [
					'{alias}.first_name',
					'{alias}.last_name'
				], 
				
				'relations' => [
					"{base_table}.profile_id = {alias}.id"
				],
				
				'conditions' => [
					"{base_table}.profile_id=1 AND {base_table}.id !=1"
				]
			]
		],
		
		[
			'accounts' => [
				'alias' => 'account', 
				'fields' => [
					'{alias}.id AS account_id',
					'{alias}.last_name'
				], 
				
				'relations' => [
					"{base_table}.profile_id = {alias}.id"
				],
				
				'conditions' => [
					"{base_table}.profile_id=1 AND {base_table}.id !=1"
				]
			]
		]
	);
	*/
 }