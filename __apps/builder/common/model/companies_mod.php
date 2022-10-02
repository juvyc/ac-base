<?php
 /**
 * @User_Mod
 * Type: Object
 * Desc: It handle all database related processes exclusive to user controller
 */
 
 class Companies_Mod extends Base_System
 {
	public $_table = "companies";
	public $_primary_key = "company_id";
	public $_fields = array(
		'company_id',
		'company_name',
		'company_industry',
		'company_website',
		'company_address_street',
		'company_address_city',
		'company_address_state',
		'company_address_postalcode',
		'company_address_country',
		'deleted_status',
	);
 }