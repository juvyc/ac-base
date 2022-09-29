<?php
 class Global_Mod extends \Base_System
 {
	public $conn = null;
	
	public function _getCurrentUserInfo()
	{
		$session = $this->Ini()->session();
		$this->system = $this->Ini()->Helper('system')->load()->call();
		$this->security = $this->Ini()->Helper('security')->load()->call();
		
		if($lc = $this->system->getfname()){
			if($this->security->isValid($lc)){
				if($userInfo = $this->security->isUserLogin()){
					$userInfo->branch = $session->get('loggedInUserBranch');
					$userInfo->type = $userInfo->role;
					return $userInfo;
				}else{
					$session->set('__redirect_after_login', $_SERVER['REQUEST_URI']);
					$this->Ini()->redirect(base_url . 'user/login/');
				}
			}else{
				$this->Ini()->redirect(base_url . 'user/' . $this->readb64('bGljZW5zZQ==') . '/');
			}
		}else{
			$this->Ini()->redirect(base_url . 'user/' . $this->readb64('bGljZW5zZQ==') . '/');
		}
	}
	
	public function settings()
	{
		
		$this->conn_init = $this->Ini()->DB();
		$db = $this->conn_init->exec();
			
		$stmt = $db
				->select(array('*'))
				->from('setting')
				->run();
		
		$rs = array();
		$rs['__class'] = $this;
		while($row = $stmt->fetch_object()){
			$rs[$row->setting_name] = $row->setting_value;
		}
		
		$this->conn_init->close_conn();
		
		return $rs;
	}
	
	
	public function doUpload($uploaderName)
	{
		if(empty($_FILES[$uploaderName]["tmp_name"])) return [];
		
		$lists = array();
		$error=array();
		$extension=array("jpeg","jpg","png","gif","pdf");
		foreach($_FILES[$uploaderName]["tmp_name"] as $key=>$tmp_name) {
			$file_name=$_FILES[$uploaderName]["name"][$key];
			$file_tmp=$_FILES[$uploaderName]["tmp_name"][$key];
			$ext=pathinfo($file_name,PATHINFO_EXTENSION);
			
			$uploadFolder = base_dir . '/__files/uploads/';
			
			$album_name = date('Y-m-d');
			
			if(!is_dir($uploadFolder . $album_name)){
				mkdir($uploadFolder . $album_name, 0777, true);
			}
			
			$fullPath = $uploadFolder . $album_name;
			
			//return $fullPath;

			if(in_array($ext,$extension)) {
				if(!file_exists($fullPath."/".$file_name)) {
					if(move_uploaded_file($file_tmp, $fullPath."/".$file_name)){
						array_push($lists, $album_name . '/' . $file_name);
					}else{
						array_push($error,"$file_name, ");
					}
				}
				else {
					$filename=basename($file_name,$ext);
					$newFileName=$filename.time().".".$ext;
					if(move_uploaded_file($file_tmp,$fullPath."/".$newFileName)){
						array_push($lists, $album_name . '/' . $newFileName);
					}else{
						array_push($error,"$file_name, ");
					}
				}
			}
			else {
				array_push($error,"$file_name, ");
			}
		}
		
		return array(
			'uploaded_files' => $lists,
			'uploaded_erros' => $error,
		);
	}
	
	public function readb64($_t = null)
	{
		return base64_decode($_t);
	}
	
	public function dbDateTime($_opt='')
	{
		$getTime = $this->Ini()->DB()->exec()
				->select(array(
					'NOW() AS now',
					'MONTH(NOW()) AS month',
					'DAY(NOW()) AS day',
					'YEAR(NOW()) AS year'
				))->run()->fetch_array();
		
		if($_opt){
			return $getTime[$_opt];
		}else return $getTime['now'];
	}
	
	public function dateTime($fmt='', $_cdate='', $_ftz='')
	{
		$settings = $this->settings();
		
		if($_cdate){
			if($_ftz){
				$date = new DateTime($_cdate, new DateTimeZone($_ftz));
			}else{
				$date = new DateTime($_cdate);
			}
		}else{
			$date = new DateTime();
		}

		$date->setTimezone(new DateTimeZone($settings['time_zone']));
		
		if(!$fmt) $fmt = 'Y-m-d H:i:s';
		return $date->format($fmt);
	}
	
 }