<?php
 
 class Hi_Clr extends Base_System
 {
	public function daction_index()
	{
		return $this
				->Ini()
				->View()
				->set_data(array())
				->use_prepared('content', 'helloworld/helloworld');
	}
	
	public function action_lovely()
	{
		return 'Hello World~';
	}
 }