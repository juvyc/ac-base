<?php
 /**
 * @security_hpr
 * Type: Object
 * Desc: Pickable methods to any controller
 */
 
 class security_hpr extends Base_System
 {
	 /**
	 * @isUserLogin
	 * Type: method
	 * Desc: Logged in user checker and validator
	 */
	public function isUserLogin()
	{
		
		$view = $this->Ini()->View();
		
		$session = $this->Ini()->session();
		
		//Check if requuired session variables for user logged in is exist
		if($session->get('loggedInUserId') && $session->get('loggedInUserUsername') && $session->get('loggedInUserPassword')){
			//Start db connection
			$db = $this->Ini()->DB()->exec();
			
			//Check if exist on the database table
			$stmt = $db
						->select(array('*'))
						->from('users')
						->where(array(
							'id' => ':ID',
							'username' => ':USERNAME',
							'password' => ':PASSWORD',
							'active' => '1',
						))
						->replace('ID', $session->get('loggedInUserId'))
						->replace('USERNAME', $session->get('loggedInUserUsername'))
						->replace('PASSWORD', $session->get('loggedInUserPassword'))
						->limit(1)
					->run();
			
			if($stmt->num_rows() > 0){
				$result = $stmt->fetch_object();
				$view->set_prepared_data('_current_user_data', $result);
				return $result;
			}
			
		}
	}
	
	
	/**
	 * @setLoginDetails
	 * Type: method
	 * Desc: To set sessions that required on login details
	 */
	public function setLoginDetails($loggedInUserId, $loggedInUserUsername, $loggedInUserPassword)
	{
		$session = $this->Ini()->session();
		
		//@loggedInUserId sesstion name that hold a user id 
		$session->set('loggedInUserId', $loggedInUserId);
		//@loggedInUserUsername sesstion name that hold a username
		$session->set('loggedInUserUsername', $loggedInUserUsername);
		//@loggedInUserPassword sesstion name that hold a password
		$session->set('loggedInUserPassword', $loggedInUserPassword);
	}
	/**
	 * @setLogout
	 * Type: method
	 * Desc: To logout user
	 */
	 public function setLogout()
	 {
		$session = $this->Ini()->session();
		$session->destroy();
	 }
	
 }