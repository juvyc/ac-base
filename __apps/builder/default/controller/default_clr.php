<?php #//php_start\\;

	class Default_Clr extends Base_System
	{
		protected $_data = array(); //Data handler
		protected $forms;
		protected $conn;
		protected $_dbq;
		
		public function load_before()
		{
			$this->forms = $this->Ini()->Helper('forms')->load()->call();
			
			$this->conn = $this->Ini()->DB();
			$this->_dbq = $this->conn->exec();
		}
		
		public function action_index($a = null, $b = null){
			return 'test';
		}
		
	}
	
#//php_end\\;?>