<?php #//php_start\\;

	class _rm_posts
	{
		var $params = array();
		
		public function __construct()
		{
			foreach($_POST as $param => $value){
				$this->_assignee($param, $value);
			}
		}
		
		public function _assignee($param, $eval){
			$this->params[strtolower($param)] = $eval;
		}
		
		public function __get($param)
		{
			if(isset($this->params[$param])) return $this->params[$param];
		}
	}
	
#//php_end\\;?>