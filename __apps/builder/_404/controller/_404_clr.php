<?php
 
 //404 Controller Class
 class _404_Clr extends \Base_System
 {
	//Set temporary template data handler
	private $params = [];
	//Controller initializer
	//__where you can initialize common properties 
	//__to use with in a class or in a sub controller
	
	public function load_before()
	{
		$this->params['htmlClass'] = 'page404';
	}
	//Controller landing method, it'll be 
	//__executed automatically if you visit a non-routed path like '/4ddfdff'
	public function action_index()
	{
		return $this->Ini()
				->View()
					->set_data($this->params)
						->use_prepared('content', '_404');
	}
 }