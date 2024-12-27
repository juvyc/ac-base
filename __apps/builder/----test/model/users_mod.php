<?php
 /**
 * @Users_Mod
 * Type: Object
 * Desc: It handle all database related processes exclusive to user controller
 */
 
 class Users_Mod extends \Base_System
 {
	public $_table = "users";
	public $_primary_key = "id";
	public $_fields = array(
		'id',
		'username',
		'password',
		'user_token',
		'full_name',
		'email',
		'role',
		'status',
		'date_added',
		'date_last_logged_in',
	);
	
	public function validate($type, $fields_values)
	{
		$_err_message = [];
		if(empty($this->_fields['username'])){
			$_err_message[] = 'Username should not be empty!';
		}else{
			$checkin = $this->Ini()->Mod('users')->get_data()->by(array('username' => $this->_fields['username']))->fetch_one();
			if(!empty($checkin->username)){
				$_err_message[] = 'Username '. $checkin->username .' is already exist!';
			}
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
	/**/
	
	public $join_left = array(
		[
			'profiles' => [
				'alias' => '_profile', 
				'fields' => [
					'`{alias}`.first_name',
					'`{alias}`.last_name'
				], 
				
				'relations' => [
					"`{base_table}`.profile_id = `{alias}`.id"
				]
			]
		]
	);
	//*/
 }