<?php
 
 //Welcome controller class
 class Welcome_Clr extends \Base_System
 {
	//Set temporary template data handler
	private $params = [];
	//Controller initializer
	//__where you can initialize common properties 
	//__to use with in a class or in a sub controller
	public function load_before()
	{
		$this->params['htmlClass'] = 'welcome-page';
	}
	//Controller landing method, it'll be 
	//__executed automatically if you visit a controller path like '/welcome'
	public function action_index()
	{	  
		//Return the welcome index template together the common header and footer within the __themes
		return $this->Ini()
				->View()
					->set_data($this->params)
						->use_prepared('content', 'welcome/index');
	}
 }