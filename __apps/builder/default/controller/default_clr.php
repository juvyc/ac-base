<?php #//php_start\\;

	class Default_Clr extends Base_System
	{
		var $data = array(); //Data handler
		
		public function load_before()
		{
			//echo $this->Uri->base();
		}
		
		public function action_index($a = '', $b = false){
			/**
			* Call the model
			*/
			return $this
					->Ini()
						->View()
							->set_template('default/test');
		}
		
	}
	
#//php_end\\;?>