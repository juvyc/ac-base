<?php #//php_start\\;

	class Test_Clr extends \Base_System
	{
		private $conn;
		private $db;
		protected $users_model;
		
		public function load_before()
		{
			
			$this->conn = $this->Ini()->DB();
			$this->db = $this->conn->exec();
			
			/**
			* Assign the location of the public directories including the base root
			*/
			
			$this->users_model = $this->Ini()->Mod('test');
		}
		
		public function action_index()
		{
			$init_users_mod = $this->users_model->get_data('users')->withRelations(['!profiles']);
			
			/**/
			$get_num_users = $init_users_mod->fetch(['COUNT({base_table}.id) AS num_users'], function($qstmt){
				//return $qstmt->run()->fetch_object()->num_users;
				$qstmt = $qstmt->where([
					'id' => ['!=' => 1]
				]);
				return $qstmt->_debug();
			});
			
			echo $get_num_users;
			//*/
			exit;
			/**/
			$get_users = $init_users_mod->fetch(function($qstmt){
				return $qstmt->run()->fetch_object();
			});
			
			echo PHP_EOL;
			var_dump($get_users);
			
			//*/
			
			/**
			$get_users = $init_users_mod->fetch(function($qstmt){
				return $qstmt;
			});
			
			echo $get_users->_debug();
			
			$profile = $this->Ini()->Mod('test')->forge('profiles');
			$profile->first_name = 'Testing 2';
			$profile->last_name = 'Testing 2';
			$profile->middle_name = 'Testing 2';
			
			$profile->save();
			
			var_dump($profile->id);
			
			
			//Check and then update
			$profile = $this->users_model->get_data('profiles')->by([
				'id' => 1
			]);
			
			if(!empty($profile->fetch_one())){
				$profile->first_name = 'Ok doki';
				$update_stat = $profile->save();
				var_dump($update_stat);
			}
			//*/
			
			$this->conn->close_conn();
			
			
		}
	}
	
#//php_end\\;?>