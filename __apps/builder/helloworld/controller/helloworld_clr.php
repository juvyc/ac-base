<?php
 
 class Helloworld_Clr extends Base_System
 {
	public function action_index()
	{
		return $this
				->Ini()
				->View()
				->set_data(array())
				->use_prepared('content', 'helloworld/helloworld');
	}
	
	public function action_hi(){
		return 1;
	}
 }