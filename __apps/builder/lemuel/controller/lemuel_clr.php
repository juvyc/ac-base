<?php
 /**
 * @User_Clr
 * Type: Object
 * Desc: User page
 */
 
 class Lemuel_Clr extends Base_System
 {
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
			//$this->_data['LoggedInUserData'] = $getCurrentLoggedInUserData;
		}else{
			$this->Ini()->redirect('login');
		}	
	}
	 
	 public function action_index()
	 {
		 return $this->Ini()->View()->get_template('lemuel');
	 }
	 
	 
	 
	 
 }