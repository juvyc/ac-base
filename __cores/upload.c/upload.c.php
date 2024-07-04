<?php #//php_start\\;
 
 class _Upload
 {
	public $results = array();
	
	public $file = null;
	
	public $extension = null;
	
	public $override = false;
	
	public function __construct(){
		
	}
	
	public function forge($fileName)
	{	
		if(isset($_FILES[$fileName]) && $_FILES[$fileName]['name'] != ""){
			
			$this->file = $_FILES[$fileName];
			
			$this->extension = pathinfo($this->file['name'], PATHINFO_EXTENSION);
			
		}
	}
	
	public function save($dir, $nfname = false)
	{
		if($this->file){
			if(is_dir($dir) && is_writable($dir)){
				$fname = $this->file['name'];
				if($nfname) $fname = $nfname;
				
				$is_go = true;
				
				if(is_file($dir . '/' . $fname)){
					if(!$this->override){
						$is_go = false;
					}
				}
				
				if($is_go){
					if(move_uploaded_file($this->file['tmp_name'], $dir . '/' . $fname)){
						$this->results['status'] = 'DONE';
						$this->results['message'] = 'File is successfully uploaded';
					}else{
						$this->results['status'] = 'FAILED';
						$this->results['message'] = 'System is currently unable to upload the file, please try again!';
						trigger_error($this->results['message'], E_USER_WARNING);
					}
				}else{
					$this->results['status'] = 'FAILED';
					$this->results['message'] = 'File '. $dir . '/' . $fname .' is already exist, to override the existing one please set the value of [override_existing_file] to [true]';
					trigger_error($this->results['message'], E_USER_WARNING);
				}
			}else{
				$this->results['status'] = 'FAILED';
				$this->results['message'] = 'Directory '. $dir .' is not exist or is not a writable directory.';
				trigger_error($this->results['message'], E_USER_WARNING);
			}
			
		}
	}
	
 }