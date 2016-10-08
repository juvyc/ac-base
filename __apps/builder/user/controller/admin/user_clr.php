<?php
 /**
 * @User_Admin_Clr
 * Type: Object
 * Desc: User management system
 */
 
 class User_Admin_Clr extends Base_System
 {
	 /**
	 * @_data
	 * Type: variable
	 * Desc: Storage of data that'll pass to template
	 */
	public $_data = array();
	
	/**
	 * @load_before
	 * Type: method
	 * Desc: Method that will load first before other
	 */
	public function load_before()
	{
		$this->db = $this->Ini()->DB()->exec();
		$this->Uri = $this->Ini()->Uri();
		$this->Action = $this->Ini()->Action();
		
		//Assign view to controller parameter @view
		$this->view = $this->Ini()->View();
		
		//Assign the secutiry helper to controller parameter @security
		$this->security = $this->Ini()->Helper('security')->load()->call();
		
		//Assign the common helper to controller parameter @common
		$this->common = $this->Ini()->Helper('common')->load()->call();
		
		//Assign the common model to controller parameter @commonModel
		$this->commonModel = $this->Ini()->Mod('common')->load();
		
		//Assign the view model to controller parameter @viewModel
		$this->viewModel = $this->Ini()->Mod('user')->load('view');
		
		//Now start checking if user who's trying to access this controller is logged in
		if($getCurrentLoggedInUserData = $this->security->isUserLogin()){
			$this->_data['LoggedInUserData'] = $getCurrentLoggedInUserData;
		}else{
			//Give a message
			$this->common->setMessage('danger', 'You are not currently logged in, please login to continue');
			//Redirect page
			$this->Ini()->redirect(base_url . 'user/login');
		}
		
		$this->_data['title'] = 'User Management';
		
		$this->_data['custom_css'] = $this->commonModel->adminAssets('css');
		$this->_data['custom_js'] = $this->commonModel->adminAssets('js');
		
		//Set active nav
		$this->_data['navActive'] = $this->Ini()->Uri()->get_segment(2);
	}
	
	public function action_index()
	{
		$this->viewModel->constructor();
		
		$this->_data['list_view']['data'] = $this->viewModel->getLists();
		$this->_data['list_view']['rows'] = $this->viewModel->getListsRows();
		
		$this->_data['content'] = $this->view->set_data($this->_data)->set_template('admin/user/lists');
		return $this->view->set_data($this->_data)->set_template('admin/template');
	}
	
	public function action_new()
	{
		$post = $this->Action->POST();
		if(count($post->params)){
			$rs = array();
			$error = "";
			if($post->param('cfldPassword') != $post->param('fldPassword')){
				$error = "Password confirmation not match.";
			}
			
			if(preg_match('/[^a-zA-Z0-9]+/', $post->param('fldUsername'), $matches)){
				//If username contents any special characters error will display
				$error = "Username only accept alphanumeric characters";
			}else{
				//Check if username is already exist
				$stmnt = $this->db
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
			
			if($error != ""){
				$rs['status'] = "failed";
				$rs['message'] = $error;
			}else{
				$usermodel = $this->Ini()->Mod('user')->forge();
				$usermodel->username = $post->param('fldUsername');
				$usermodel->password = $this->Ini()->MD5($post->param('fldPassword'));
				$usermodel->active = $post->param('fldStatus');
				$usermodel->first_name = $post->param('fldFirstName');
				$usermodel->last_name = $post->param('fldLastName');
				$usermodel->role = $post->param('fldrole');
				$usermodel->date_added = strtotime('now');
				
				$usermodel->save();
				
				if($usermodel->id){
					$rs['status'] = "success";
					$this->common->setMessage('success', 'User has been successfully added.');	
				}else{
					$rs['status'] = "failed";
					$rs['message'] = 'System is unable to save user';
				}
			}
			
			return json_encode($rs);
		}
		return $this->view->set_data($this->_data)->get_template('admin/user/ajaxnew');
	}
	
	public function action_edit()
	{
		$post = $this->Action->POST();
		
		if($cid = (int) $this->Uri->get_segment(4)){
			
			$quid = $this->Ini()->Mod('user')->get_data()->by(array(
				'id' => $cid
			));
			
			$this->_data['user_data'] = $quid->fetch_one();
			
			if(count($this->_data['user_data'])){
				if(count($post->params)){
					$rs = array();
					$error = "";
					if($post->param('fldPassword')){
						if($post->param('cfldPassword') != $post->param('fldPassword')){
							$error = "Password confirmation not match.";
						}
					}
					
					if($error != ""){
						$rs['status'] = "failed";
						$rs['message'] = $error;
					}else{
						$usermodel = $quid;
						if($post->param('fldPassword')){
							$usermodel->password = $this->Ini()->MD5($post->param('fldPassword'));
						}
						$usermodel->active = $post->param('fldStatus');
						$usermodel->first_name = $post->param('fldFirstName');
						$usermodel->last_name = $post->param('fldLastName');
						$usermodel->role = $post->param('fldrole');
						
						$usermodel->save();
						
						$rs['status'] = "success";
						$this->common->setMessage('success', 'User has been successfully updated.');
					}
					
					die(json_encode($rs));
				}
				
				return $this->view->set_data($this->_data)->get_template('admin/user/ajaxedit');
			}
		
		}
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
		
		return $this->Ini()->View()->set_data($this->_data)->use_prepared('content', 'user/login');
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
	
	public function action_ajaxaction()
	{
		$post = $this->Action->POST();
		
		if(count($post->param('item'))){
			$cc = 0;
			foreach($post->param('item') as $userid){
				if($userid > 1){
					$userdata = $this->Ini()->Mod('user')->get_data()->by(array(
						'id' => $userid
					))->delete();
					
					if($userdata){
						$cc++;
					}
				}
			}
			
			if($cc){
				$this->common->setMessage('success', $cc . ' user(s) has been successfully deleted.');
			}else{
				$this->common->setMessage('failed', $cc . ' user is deleted.');
			}
		}
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