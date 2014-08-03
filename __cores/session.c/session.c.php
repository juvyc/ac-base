<?php #//php_start\\;
	
	class Base_Session
	{
	
		 var $SessionName=null;
    
		/* Function name: Constructor
		Params:
			@SessionName - The session name
		*/
		public function __constructor($SessionName)
		{
			if($SessionName){
				$this->SessionName=$SessionName;
			}
		}
		/* Function name: Set
		Params:
			@Setting - The key to set
			@Value - The value to set
		*/
		public function set($Setting = "",$Value = "")
		{
			if(is_array($Setting)){
				foreach($Setting as $listName => $lVal){
					$_SESSION[$listName] = $lVal;
				}
			}else{
				if($Setting != "" && $Value != ""){
					$_SESSION[$Setting]=$Value;
				}
			}
		}
		/* Function name: Get
		Params:
			@Setting - The key to get
			@Default - Value to return if the requested key is empty.
		*/
		public function get($Setting = '')
		{
			if(isset($_SESSION[$Setting])){
				return $_SESSION[$Setting];
			}
		}
		
		/**
			Destroy all sessions
		*/		
		public function destroy($listS = ""){
			if(empty($listS)){
				session_destroy();
			}else{
				$this->_unset($listS);
			}
		}
		
		/**
			Session unsetter
		*/
		public function _unset($listS){
			$listofNotToRemove = array();
			if(is_array($listS)){
				foreach($listS as $sesItem){
					if(isset($_SESSION[$sesItem])){
						$listofNotToRemove[$sesItem] = $_SESSION[$sesItem];
					}
				}
				
			}else{
				if($listS != null && isset($_SESSION[$listS])){
					$listofNotToRemove[$listS] = $_SESSION[$listS];
				}
			}
			
			//$_SESSION = Array();
			
			foreach($listofNotToRemove as $listOET => $vOETVal){
				if(isset($_SESSION[$listOET]))	$_SESSION[$listOET] = null;
			}
		}
	}
	
#//php_end\\;?>