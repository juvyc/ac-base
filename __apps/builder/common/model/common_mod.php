<?php
 /**
 * @Common_Mod
 * Type: Object
 * Desc: Common functionality that accessible to any builder
 */
 
 class Common_Mod
 {
	 /**
	 * @adminAssets
	 * Type: Method
	 * Desc: Optional function that handle lists assets specific for admin section
	 */
	public function adminAssets($type)
	{
		$lists = array();
		
		switch(strtolower($type)){
			case 'css' :
				$lists[] = '__assets/css/datepicker.css';
				$lists[] = '__assets/css/jquery.fancybox.css';
				$lists[] = '__assets/css/admin.css';
			break;
			
			case 'js' : 
				$lists[] = '__assets/js/moment.js';
				$lists[] = '__assets/js/bootstrap-datepicker.js';
				$lists[] = '__assets/ckeditor/ckeditor.js';
				$lists[] = '__assets/js/jquery.fancybox.js';
				$lists[] = '__assets/js/jquery.form.js';
				$lists[] = '__assets/js/controller.js';
			break;
		}
		
		return $lists;
	}
	
	public function frontAssets($type)
	{
		$lists = array();
		
		switch(strtolower($type)){
			case 'css' :
				$lists[] = '__assets/css/datepicker.css';
				$lists[] = '__assets/css/jquery.fancybox.css';
				$lists[] = '__assets/css/front.css';
			break;
			
			case 'js' : 
				$lists[] = '__assets/js/moment.js';
				$lists[] = '__assets/js/bootstrap-datepicker.js';
				$lists[] = '__assets/js/jquery.fancybox.js';
				$lists[] = '__assets/js/jquery.form.js';
				$lists[] = '__assets/ckeditor/ckeditor.js';
				$lists[] = '__assets/js/controller.js';
			break;
		}
		
		return $lists;
	}
	
	//Changes status label but not the key
	public function statuses($key = false, $type = 'production')
	{
		if($type == 'production'){
			//Status for production
			$statuses = array(
				1 => 'Waiting',
				2 => 'In-Progress',
				3 => 'To Serve',
			);
			
			return (isset($statuses[$key])) ? $statuses[$key] : false;
		}else if($type == 'casher'){
			$key = $key - 1;
			//Status for casher
			$statuses = array(
				0 => 'Unpaid',
				1 => 'Paid',
				2 => 'Credit',
			);
			
			(isset($statuses[$key])) ? $statuses[$key] : false;
		}
	}
	
	
	public function slugify($text)
	{
		$text = htmlentities($text);
		$text = html_entity_decode($text);
		$text = strip_tags($text);
		
		$text = preg_replace("/&#?[a-z0-9]+;/i","",$text);
		
		$text = htmlspecialchars($text);
		
		$text = str_replace("&#39;", '', $text);
		$text = str_replace('&#34;', '', $text);
		
		
		$text = preg_replace('/[^A-Za-z0-9 ]/', ' ', $text);

		// trim
		$text = trim($text, '-');

		// lowercase
		$text = strtolower($text);


		if (empty($text))
		{
		return false;
		}
		
		$limit = 50;
		
		if(strlen($text) > $limit){
		$text = substr($text, 0, $limit);
		if(substr($text, $limit - 1, $limit) != ""){
			$explodedtospace = explode(' ',$text);
			if(count($explodedtospace))
				unset($explodedtospace[count($explodedtospace) - 1]);
			$text = implode(' ', $explodedtospace);
		}
		}
		
		$text = str_replace(' ', ' ', $text);
		$text = str_replace(' ', '-', $text);
		$text = str_replace('--', '-', $text);
		
		if(substr($text, strlen($text) - 1, strlen($text)) == '-')
		$text = substr($text, 0, strlen($text) - 1);
		
		if(substr($text, 0, 1) == '-')
		$text = substr($text, 1, strlen($text));
		
		$text = str_replace('--', '-', $text);
		
		return $text;
	}
 }