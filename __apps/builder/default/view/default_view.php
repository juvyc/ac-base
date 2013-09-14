<?php #//php_start\\;

	class Default_View extends Default_Mod
	{
		public function home_view(){
			/**
			* Assign the template
			*/
			return $this
					->Ini()
						->View()
							->set_data($this->data)
								->set_template('default/homepage');
					
		}
		
	}
	
#//php_end\\;?>