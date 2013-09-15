<?php
 
 class _Dir
 {
	public $override = false;
	
	public function create($path, $perm = 0777, $opt = true)
	{
		if(!is_dir($path)){
			if(!mkdir($path, $perm, $opt)){
				trigger_error('System is currently unable to create directory '. $path .', please check your base directory permisions and try again', E_USER_WARNING);
			}else return true;
		}else if($override){
			if(!chmod($path, $perm)){
				trigger_error('System is currently unable to change permission of directory '. $path, E_USER_WARNING);
			}else return true;
		}
	}
	
	public function chmode($path, $perm)
	{
		if(is_dir($path)){
			if(!chmod($path, $perm)){
				trigger_error('System is currently unable to change permission of directory '. $path, E_USER_WARNING);
			}else return true;
		}else{
			trigger_error('Directory '. $path .' is not exist.', E_USER_WARNING);
		}
	}
	
 }