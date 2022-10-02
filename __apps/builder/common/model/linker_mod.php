<?php
 /**
 * @User_Mod
 * Type: Object
 * Desc: It handle all database related processes exclusive to user controller
 */
 
 class Linker_Mod extends Base_System
 {
	public $_table = "linker";
	public $_primary_key = "id";
	public $_fields = array(
		'id',
		'type',
		'base_id',
		'ref_id'
	);
 }