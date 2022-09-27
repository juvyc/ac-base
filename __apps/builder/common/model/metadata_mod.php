<?php
 /**
 * @User_Mod
 * Type: Object
 * Desc: It handle all database related processes exclusive to user controller
 */
 
 class Metadata_Mod extends Base_System
 {
	public $_table = "meta_data";
	public $_primary_key = "id";
	public $_fields = array(
		'id',
		'data_id',
		'meta_key',
		'meta_value'
	);
	
 }