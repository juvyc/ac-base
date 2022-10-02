<?php
 /**
 * @User_Mod
 * Type: Object
 * Desc: It handle all database related processes exclusive to user controller
 */
 
 class Leads_Mod extends Base_System
 {
	public $_table = "leads";
	public $_primary_key = "lead_id";
	public $_fields = array(
		'lead_id',
		'lead_title',
		'lead_fname',
		'lead_lname',
		'lead_phone',
		'lead_emailadd',
	);
 }