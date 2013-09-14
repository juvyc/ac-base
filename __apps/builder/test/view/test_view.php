<?php #//php_start\\;

	class Test_View extends Test_Mod
	{
		public function home_view(){
			/**
			* Assign the template
			*/
			
			$this->_affdata();
			
			return $this
					->Ini()
						->View()
							->set_data($this->data)
								->set_template('default/test');
					
		}
		
	}
	
#//php_end\\;?>