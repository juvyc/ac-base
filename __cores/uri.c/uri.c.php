<?php #//php_start\\;
	
	class _Uri
	{
		/**
		* Get request URL base by segment
		*/
		public function get_segment($n = 0)
		{
			if($n > 0){
				if(count($this->___uriD())){
					$get_array_list = $this->___uriD();
					return isset($get_array_list[$n - 1]) && $get_array_list[$n - 1] != "" ? $get_array_list[$n - 1] : null;
				}
			}
		}
		
		/**
		* Get all segments
		*/
		public function get_segments()
		{
			return $this->___uriD();
		}
		
		/**
		* Get base URI
		*/
		public function base()
		{
			$s = empty($_SERVER["HTTPS"]) ? '' : (($_SERVER["HTTPS"] == "on") ? "s" : "");
			$protocol = $this->strLeft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
			$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
			return $protocol."://".$_SERVER['SERVER_NAME'].$port . base_url;   
		}
		
		/**
		* Get the positionning, this will get the possition
		* of a certain words or character from the sentence
		* Ex. You wish to get some word before that certain word/char
		* from the sentence like below:
		* $s1 = 'The Dog Run After Seeing Me';
		* $s2 = 'After Seeing Me';
		*
		* That above example will output below:
		* 
		* = The Dog Run
		*/
		public function strLeft($s1, $s2) {
			return substr($s1, 0, strpos($s1, $s2));
		}
		
		/**
		* This is the request URI extractor
		*/
		public function ___uriD(){
			if(base_url == "/"){
				$getOnlyClean = substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI']));
				$extracted_uri = array(
					0 => '/',
					1 => $getOnlyClean
				);
			}else{
				$extracted_uri = explode(base_url, $_SERVER['REQUEST_URI']);
			}
				
			//echo $extracted_uri[1];exit;
			$get_s_extracted = isset($extracted_uri[1]) ? explode('?', $extracted_uri[1]) : array();
			//$get_t_extracted = isset($get_s_extracted[0]) ? explode('/', $get_s_extracted[0]) : array();
			if(isset($get_s_extracted[0])){
				$tostrng = (substr($get_s_extracted[0], strlen($get_s_extracted[0]) - 1, strlen($get_s_extracted[0])) == "/") ? substr($get_s_extracted[0], 0, strlen($get_s_extracted[0]) - 1) : $get_s_extracted[0];
				$get_t_extracted = explode("/", $tostrng);
			}else{
				$get_t_extracted = array();
			}
			return $get_t_extracted;
		}
		
		public function get_the_rest_segments($start_segment = '')
		{
			$get_list_rests = array();
			if($start_segment){
				$start_segment = str_replace('_', '-', $start_segment);
				$start_rec = 0;
				foreach($this->get_segments() as $item){
					
					if($start_rec)
						$get_list_rests[] = $item;
					
					if($item == $start_segment && $start_rec < 1) $start_rec = 1;
				}
			}else{
				foreach($this->get_segments() as $item){
					$get_list_rests[] = $item;
				}
			}
			
			return $get_list_rests;
		}
	}
	
#//php_end\\;?>