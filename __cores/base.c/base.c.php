<?php #//php_start\\;

	class Base_System
	{
		public function Ini(){
			require_once(dirname(__FILE__) . '/base.c.type.php');
			return new Base_Type();
		}
	}
	
#//php_end\\;?>