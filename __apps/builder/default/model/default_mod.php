<?php #//php_start\\;

	class Default_Mod extends Default_Clr
	{
		public function home_mod(){
			/**
			* Assign the view
			*/
			return $this
					->Ini()
						->View('default')
							->load()
								->home_view();
		}
		
	}
	
#//php_end\\;?>