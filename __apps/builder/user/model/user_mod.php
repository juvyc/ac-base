<?php
 /**
 * @User_Mod
 * Type: Object
 * Desc: It handle all database related processes exclusive to user controller
 */
 
 class User_Mod extends Base_System
 {
	public $_table = "users";
	public $_primary_key = "id";
	public $_fields = array(
		'id',
		'username',
		'password',
		'active',
		'first_name',
		'last_name',
		'role',
		'params',
		'date_added',
	);
	
	 /**
	 * @encryptor
	 * Type: Method
	 * Desc: To encrypt something
	 */
	public function encryptor($string)
	{
		return $this->Ini()->MD5($string);
	}
	
	 /**
	 * @registerUser
	 * Type: Method
	 * Desc: To register user
	 */
	public function registerUser()
	{
		//@rs, use as method self response handle in array format
		$rs = array();
		//Starting initializign the database
		$db = $this->Ini()->DB()->exec();
		//Starting initializign the post method
		$post = $this->Ini()->Action()->POST();
		//Checking if the method post has arleast one post field submitted
		if(count($post->params)){
			//Error message handler
			$error = "";
			//Checking if email is not exist on the post method
			if(!$post->param('fldEmail')){
				$error = "Please enter your email address.";
			}else if(!filter_var($post->param('fldEmail'), FILTER_VALIDATE_EMAIL)){
				//if email is not in email format
				$error = "Please enter a valid email address.";
			}else{
				//Check email if exist
				$stmnt = $db
							->select(array('email'))
							->from('users')
							->where(array(
								'email' => ':email'
							))
							->replace('email', $post->param('fldEmail'))
							->limit(1)
							->run();
				if($stmnt->num_rows()){
					$error = 'Email is already exist, if you think this email is yours and you forgot your password, try to recover it <a href="'. base_url .'user/recover">here</a>.';
				}
			}
			//checkign if username is not exist on post method request
			if(!$post->param('fldUsername')){
				$error = "Please enter your prefered username.";
			}else if(preg_match('/[^a-zA-Z0-9]+/', $post->param('fldUsername'), $matches)){
				//If username contents any special characters error will display
				$error = "Username only accept alphanumeric characters";
			}else{
				//Check if username is already exist
				$stmnt = $db
							->select(array('email'))
							->from('users')
							->where(array(
								'username' => ':username'
							))
							->replace('username', $post->param('fldUsername'))
							->limit(1)
							->run();
				if($stmnt->num_rows()){
					$error = 'Username is already exist, if you think this username is yours and you forgot your password, try to recover it <a href="'. base_url .'user/recover">here</a>.';
				}
			}
			//Checking if password doesn't exist on post method request
			if(!$post->param('fldPassword')){
				$error = "Please enter your prefered username.";
			}else if ($post->param('fldPassword') != $post->param('fldCPassword')){
				//IF password not match with the its confirmation
				$error = "Confirmation password doesn't match with your prefered passwords.";
			}
			
			//Finally checing if error is not empty
			if($error != ""){
				$rs['status'] = 'warning';
				$rs['message'] = $error;
			}else{
				
				$password = $this->encryptor($post->param('fldPassword'));
				$username = $post->param('fldUsername');
				$email = $post->param('fldEmail');
				
				//Do insert to database
				$query = $db
							->insert('users')
							->data(array(
								'username' => ":username",
								'password' => ":password",
								'email' => ":email",
								'date_added' => ":date_added",
							))
							->replace('username', $username)
							->replace('password', $password)
							->replace('email', $email)
							->replace('date_added', strtotime('now'))
						->run();
				//Check if successfully inserted		
				if($query->insert_id()){
					$rs['status'] = 'success';
					$rs['data'] = array(
						'loggedInUserId' => $query->insert_id(),
						'loggedInUserUsername' => $username,
						'loggedInUserPassword' => $password,
					);
				}else{
					$rs['status'] = 'danger';
					$rs['message'] = 'System is currently unable to save user.';
				}
			}
		}
		
		return $rs;
	}
	/**
	 * @getUser
	 * Type: Method
	 * Desc: To get specific user
	 */
	public function getUser($where = array())
	{
		if(count($where)){
			//Starting initializign the database
			$db = $this->Ini()->DB()->exec();
			$stmnt = $db
						->select(array('*'))
						->from('users')
						->where($where)
						->limit(1)
						->run();
			if($stmnt->num_rows()){
				return $stmnt->fetch_object();
			}
		}
	}
	/**
	 * @doLogin
	 * Type: Method
	 * Desc: To login user
	 */
	public function doLogin()
	{
		//@rs, use as method self response handle in array format
		$rs = array();
		
		//Starting initializign the post method
		$post = $this->Ini()->Action()->POST();
		
		//Checking if the method post has arleast one post field submitted
		if(count($post->params)){
		
			//Error message handler
			$error = "";
			//check if email is not exist on post method request
			if(!$post->param('fldUsername')){
				$error = "Email is required";
			}
			//check if password is not exist on post method request
			if(!$post->param('fldPassword')){
				$error = "Password is required";
			}
			//check if error is not null
			if($error != ""){
				$rs['status'] = 'warning';
				$rs['message'] = $error;
			}else{
				//encrypt password with the common encryptor
				$password = $this->encryptor($post->param('fldPassword'));
				//building a where statement
				$where = array(
					'username' => $post->param('fldUsername'),
					'password' => $password
				);
				
				//get user data
				$getUser = $this->getUser($where);
				
				//check if user exist
				if($getUser){
					$rs['status'] = 'success';
					$rs['data'] = array(
						'loggedInUserId' => $getUser->id,
						'loggedInUserUsername' => $getUser->username,
						'loggedInUserPassword' => $getUser->password,
					);
				}else{
					//If user is not exist, display error message.
					$rs['status'] = 'danger';
					$rs['message'] = "Invalid username and password.";
				}
				
			}
		}
		
		return $rs;
	}
 }