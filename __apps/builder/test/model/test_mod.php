<?php #//php_start\\;

	class Test_Mod extends Test_Clr
	{
		public function home_mod(){
			/**
			* Assign the view
			*/
			return $this
					->Ini()
						->View('test')
							->load()
								->home_view();
		}
		
	}
	
#//php_end\\;?>