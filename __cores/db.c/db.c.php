<?php #//php_start\\;	
	
	
	
	require_once('db.c.queries.php');
	
	class _DB
	{
		protected $db_name;
		
		protected $db_username;
		
		protected $db_password;
		
		protected $db_host;
		
		var $db_prefix;
		
		protected $db_conn_handler;
		
		var $globals = array();
		
		public function __construct()
		{
			global $GLOBALS;
			
			$this->globals = $GLOBALS;
			
			$get_array_data_in_file = include $this->globals['path']['apps_path'] . '/config/connection.a.c.php';
			
			$this->db_name = $get_array_data_in_file['db_name'];
			
			$this->db_username = $get_array_data_in_file['db_username'];
			
			$this->db_password = $get_array_data_in_file['db_password'];
			
			$this->db_host = $get_array_data_in_file['db_host'];
			
			$this->db_prefix = $get_array_data_in_file['db_prefix'];
			
			$this->connect();
			
		}
		
		/**
		* Execute database connection
		*/
		public function connect()
		{
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
		
		/**
		* Closing database connection
		*
		*/
		public function close_conn()
		{
			mysql_close($this->db_conn_handler);
		}
		
		/**
		* Events execution like delete, select, update queries
		*/
		public function exec()
		{
			return new _QUERY($this->db_conn_handler);
		}
		
	}
	
#//php_end\\;?>