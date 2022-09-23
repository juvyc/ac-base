<?php #//php_start\\;

	class Customers_Mod extends Base_System
	{
		public $_table = "customers";
		public $_primary_key = "id";
		public $_fields = array(
			'id',
			'customer_name',
			'address',
			'phone',
		);
	}
	
#//php_end\\;?>