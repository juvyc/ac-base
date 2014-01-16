<?php
 class Helloworld_Mod extends Base_System
 {
	private $__table = 'user';
	private $__primary_key = 'ID';
	private $__fields = array(
		'ID', 'username', 'password', 'params', 'date_added'
	);
	
	public function test()
	{
		return 'I\'m a data from helloworld model';
	}
 }