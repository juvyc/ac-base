<?php
 /**
 * @User_Mod
 * Type: Object
 * Desc: It handle all database related processes exclusive to user controller
 */
 
 class Data_Mod extends Base_System
 {
	public $_table = "data";
	public $_primary_key = "id";
	public $_fields = array(
		'id',
		'type',
		'datetime_created',
		'datetime_updated',
		'created_by',
		'status',
	);
	
 }