<?php
 /**
 * @common_hpr
 * Type: Object
 * Desc: Pickable methods to any controller
 */
 
 class common_hpr extends Base_System
 {
	 
	 /**
	 * @c_messages
	 * Type: Variable
	 * Desc: To set the name of the common messages like error validation, warning and success
	 */
	 private $c_messages = 'messages';
	 
	 /**
	 * @setMessage
	 * Type: Method
	 * Desc: To set message
	 */
	 public function setMessage($type, $message)
	 {
		$session = $this->Ini()->View();
		
		$ms = array();
		
		if($c_messages_v = $session->get_prepared_data($this->c_messages)){
			$ms = $c_messages_v;
		}
		
		$ms[$type] = $message;
		
		$session->set_prepared_data($this->c_messages, $ms);
	 }
 }