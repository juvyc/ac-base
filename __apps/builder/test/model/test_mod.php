<?php #//php_start\\;

	class Test_Mod extends Base_System
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
		
		public function test()
		{
			return 'I\'m a data from test model';
		}
		
	}
	
#//php_end\\;?>