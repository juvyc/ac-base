<?php #//php_start\\;

	class Default_Admin_Clr extends Base_System
	{
		var $data = array(); //Data handler
		
		public function load_before()
		{
			//echo $this->Uri->base();
		}
		
		public function action_index($a = '', $b){
			/**
			* Call the model
			*/
			return $this
					->Ini()
						->View()
							->set_template('default/test');
		}
		
		public function action_profile()
		{
			return 2;
		}
		
	}
	
#//php_end\\;?>