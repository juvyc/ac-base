<?php
 class Helloworld_Mod
 {
	var $__table = 'sample';
	var $__primary_key = 'id';
	var $__fields = array(
		'id', 'email'
	);
	
	public function test()
	{
		return 'I\'m a data from helloworld model';
	}
 }