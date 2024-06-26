<?php #//php_start\\;	
	
	class _DB
	{
		protected $db_name;
		
		protected $db_username;
		
		protected $db_password;
		
		protected $db_host;
		
		public $db_prefix = null;
		
		protected $db_conn_handler;
		
		public $ACB_GLOBALS = array();
		
		public $conn_type = 'mysqli';
		
		protected $globals;
		
		public function __construct($dbclasstype = '', $dbtype = '')
		{
			global $ACB_GLOBALS;
			
			$this->globals = $ACB_GLOBALS;
			
			$get_array_data_in_file = include $this->globals['path']['apps_path'] . '/config/connection.a.c.php';
			
			$this->db_name = $get_array_data_in_file['db_name'];
			
			$this->db_username = $get_array_data_in_file['db_username'];
			
			$this->db_password = $get_array_data_in_file['db_password'];
			
			$this->db_host = $get_array_data_in_file['db_host'];
			
			$this->db_prefix = $get_array_data_in_file['db_prefix'];
			
			if(strtolower($dbclasstype) == 'pdo'){
				//type whether pgsql or mysql
				return $this->PDO($dbtype);
			}else{
				$this->connect();
				
				require_once('db.c.queries.controller.php');
				require_once('db.c.queries.php');
			}
			
		}
		
		/**
		* Execute database connection
		*/
		public function connect()
		{
			if(!empty($GLOBALS['__db_conn_handler'])){
				$this->db_conn_handler = $GLOBALS['__db_conn_handler'];
				$this->conn_type = $GLOBALS['__db_conn_type'];
			}else{
				if(function_exists('mysqli_connect')){
					if(!$this->db_conn_handler) $this->db_conn_handler = @mysqli_connect($this->db_host, $this->db_username, $this->db_password, $this->db_name);
					if(mysqli_connect_errno()){
						trigger_error("Connect failed: %s\n", mysqli_connect_error(), E_USER_WARNING);
					}
					
					$this->conn_type = 'mysqli';
					
				}else{
					/**
					* Do connect to the database server
					*/
					if(!$this->db_conn_handler = @mysql_connect($this->db_host, $this->db_username, $this->db_password, true)) {
						trigger_error("Unable to communicate to your database server!", E_USER_WARNING);
					}
					
					/**
					* Do select database name
					*/
					if(!mysql_select_db($this->db_name, $this->db_conn_handler)) {
						trigger_error("Unable to select database, please make sure that your database is really exist on your server!", E_USER_WARNING);				
					}
					
					/**
					* Setting up the database characters
					*/
					mysql_query("SET NAMES utf8", $this->db_conn_handler);
					mysql_query("SET CHARACTER SET utf8", $this->db_conn_handler);
				}
				
				$GLOBALS['__db_conn_handler'] = $this->db_conn_handler;
				$GLOBALS['__db_conn_type'] = $this->conn_type;
			}
		}
		
		/**
		* Closing database connection
		*
		*/
		public function close_conn()
		{
			if($this->conn_type == 'mysqli'){
				mysqli_close($this->db_conn_handler);
				if(!empty($GLOBALS['__db_conn_handler']) && is_resource($GLOBALS['__db_conn_handler'])) mysqli_close($GLOBALS['__db_conn_handler']);
			}else{
				mysql_close($this->db_conn_handler);
				if(!empty($GLOBALS['__db_conn_handler']) && is_resource($GLOBALS['__db_conn_handler'])) mysql_close($GLOBALS['__db_conn_handler']);
			}
		}
		
		/**
		* Events execution like delete, select, update queries
		*/
		public function exec()
		{
			return new _QUERY($this->db_conn_handler, $this->db_prefix, $this->conn_type);
		}
		
		private function PDO($dbtype = 'mysql')
		{
			try{
				return new PDO($dbtype.':host='. $this->db_host .';dbname='. $this->db_name, $this->db_username, $this->db_password);
			}catch(PDOException $e){
				trigger_error($e->getMessage(), E_USER_WARNING);
			}
		}
		
	}
	
#//php_end\\;?>