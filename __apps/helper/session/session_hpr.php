<?php #//php_start\\;
	
	class session_hpr
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
		public function _SET($Setting = "",$Value = "")
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
		public function _GET($Setting = '')
		{
			if(isset($_SESSION[$Setting])){
				return $_SESSION[$Setting];
			}
		}
		
		/**
			Destroy all sessions
		*/		
		public function _DESTROY($listS = ""){
			$this->_UNSET($listS);
		}
		
		/**
			Session unsetter
		*/
		public function _UNSET($listS){
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
			
			$_SESSION = Array();
			
			foreach($listofNotToRemove as $listOET => $vOETVal){
				$_SESSION[$listOET]	= $vOETVal;
			}
		}
	}
	
#//php_end\\;?>