<?php
 /**
 * @User_Clr
 * Type: Object
 * Desc: User page
 */
 
 class User_Clr extends Base_System
 {
	 /**
	 * @_data
	 * Type: variable
	 * Desc: Storage of data that'll pass to template
	 */
	public $_data = array();
	
	/**
	 * @security
	 * Type: variable
	 * Desc: Global holder of  security helper in this controller
	 */
	private $security =  null, $common =  null, $userModel = null;
	
	/**
	 * @load_before
	 * Type: method
	 * Desc: Method that will load first before other
	 */
	public function load_before()
	{
		//Assign the secutiry helper to controller parameter @security
		$this->security = $this->Ini()->Helper('security')->load()->call();
		
		//Assign the common helper to controller parameter @common
		$this->common = $this->Ini()->Helper('common')->load()->call();
		
		//Assign the user model to controller parameter @userModel
		$this->userModel = $this->Ini()->Mod('user')->load();
		
		//Assign the common model to controller parameter @commonModel
		$this->commonModel = $this->Ini()->Mod('common')->load();
		
		//Now start checking if user who's trying to access this controller is logged in
		if($getCurrentLoggedInUserData = $this->security->isUserLogin()){
			$this->_data['LoggedInUserData'] = $getCurrentLoggedInUserData;
		}
		
		$this->_data['custom_css'] = $this->commonModel->frontAssets('css');
		$this->_data['custom_js'] = $this->commonModel->frontAssets('js');
	}
	
	public function action_index()
	{
		//$this->_data['messages'] = $this->common->getMessages();
		return 'User page...';
	}
	
	public function action_login()
	{
		//ALways redirect if user is currently login
		if(isset($this->_data['LoggedInUserData'])) $this->Ini()->redirect(base_url);
		
		$getLogin = $this->userModel->doLogin();
		
		if(count($getLogin)){
			if($getLogin['status'] == 'success'){
				//Format: User ID, Username, Password
				$this->security->setLoginDetails($getLogin['data']['loggedInUserId'], 
													$getLogin['data']['loggedInUserUsername'], 
													$getLogin['data']['loggedInUserPassword']
												);
				$this->common->setMessage('success', 'Successfully login!.');
				
				//Redirect page
				$this->Ini()->redirect(base_url);
				
			}else{
				$this->common->setMessage($getLogin['status'], $getLogin['message']);
			}
		}
		$this->_data['content'] = $this->Ini()->View()->set_data($this->_data)->get_template('user/login');
		return $this->Ini()->View()->set_data($this->_data)->set_template('user/template');
		
		//return $this->Ini()->View()->set_data($this->_data)->use_prepared('content', 'user/login');
	}
	
	public function action_register()
	{
		//ALways redirect if user is currently login
		if(isset($this->_data['LoggedInUserData'])) $this->Ini()->redirect(base_url);
		
		$registerUser = $this->userModel->registerUser();
		
		if(count($registerUser)){
			if($registerUser['status'] == 'success'){
				//Format: User ID, Username, Password
				$this->security->setLoginDetails($registerUser['data']['loggedInUserId'], 
													$registerUser['data']['loggedInUserUsername'], 
													$registerUser['data']['loggedInUserPassword']
												);
				$this->common->setMessage('success', 'User has been successfully saved.');
				
				//Redirect page
				$this->Ini()->redirect(base_url);
				
			}else{
				$this->common->setMessage($registerUser['status'], $registerUser['message']);
			}
		}
		
		return $this->Ini()->View()->set_data($this->_data)->use_prepared('content', 'user/register');
	}
	
	public function action_logout()
	{
		//Logout
		$this->security->setLogout();
		//Redirect page
		$this->Ini()->redirect(base_url);
	}
	
	// public function load_404()
	// {
		// return 404;
	// }
	
	
	public function load_after()
	{
		
	}
 }