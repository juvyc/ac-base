<?php
 
 class _File
 {
	
	public $override = false;
	
	public function write($fileloc, $contents = "")
	{
		if(is_file($fileloc)){
			if($this->override){
				$content = $contents;
				$content = $contents;
				if($fp = fopen($fileloc,"wb")){
					fwrite($fp,$content);
					fclose($fp);
					return true;
				}else{
					trigger_error('System is unable to create file ' . $fileloc . ' please check the directory permission before proceeding.', E_USER_WARNING);
				}
			}else{
				trigger_error('Unable to re-create file ' . $fileloc . ', this file is already exist. To force re-creating this file set the override = true;', E_USER_WARNING);
			}
		}else{
				$content = $contents;
				if($fp = fopen($fileloc,"wb")){
					fwrite($fp,$content);
					fclose($fp);
					return true;
				}else{
					trigger_error('System is unable to create file ' . $fileloc . ' please check the directory permission before proceeding.', E_USER_WARNING);
				}
		}
	}
	
	public function get_contents($filename)
	{
		if(is_file($filename))
			return file_get_contents($filename);
	}
	
	public function rename($ofn, $nfn)
	{
		if(rename($ofn, $nfn)){
			return true;
		}else{
			trigger_error('System is unable to rename file ' . $ofn . ' to ' . $nfn . ', please check the file permission before proceeding.', E_USER_WARNING);
		}
	}
	
	public function remove($fn)
	{
		if(!unlink($fn)){
			trigger_error('System is unable to remove file ' . $fn . ' please check the file permission before proceeding.', E_USER_WARNING);
		}else{
			return true;
		}
	}
	
	public function copy($file, $newfile)
	{
		if (!copy($file, $newfile)) {
			trigger_error('System is unable to copy file ' . $file . ' to ' . $newfile ', please check the file permission before proceeding.', E_USER_WARNING);
		}else{
			return true;
		}
	}
 }