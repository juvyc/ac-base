<?php
 /**
 * @common_hpr
 * Type: Object
 * Desc: Pickable methods to any controller
 */
 
 class common_hpr extends Base_System
 {
	 
	 public $DTFieldTypes = array(
		'text' => 'Input Text',
		'textarea' => 'Textarea',
	 );
	 
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
	 
	 
	 /**
	 * @slugify
	 * $text - the text to convert to slug format
	 * $limit - limit of slug characters to generate
	 */
	public function slugify($text,$limit=100)
	{ 
		$text = htmlentities($text);
		$text = html_entity_decode($text);
		$text = strip_tags($text);
		
		$text = preg_replace("/&#?[a-z0-9]+;/i","",$text);
		
		$text = htmlspecialchars($text);
		
		$text = str_replace("&#39;", '', $text);
		$text = str_replace('&#34;', '', $text);
		
		
		$text = preg_replace('/[^A-Za-z0-9 ]/', ' ', $text);

		// trim
		$text = trim($text, '-');

		// lowercase
		$text = strtolower($text);


		if (empty($text))
		{
		return false;
		}
		
		if(strlen($text) > $limit){
		$text = substr($text, 0, $limit);
		if(substr($text, $limit - 1, $limit) != ""){
			$explodedtospace = explode(' ',$text);
			if(count($explodedtospace))
				unset($explodedtospace[count($explodedtospace) - 1]);
			$text = implode(' ', $explodedtospace);
		}
		}
		
		$text = str_replace(' ', ' ', $text);
		$text = str_replace(' ', '-', $text);
		$text = str_replace('--', '-', $text);
		
		if(substr($text, strlen($text) - 1, strlen($text)) == '-')
		$text = substr($text, 0, strlen($text) - 1);
		
		if(substr($text, 0, 1) == '-')
		$text = substr($text, 1, strlen($text));
		
		$text = str_replace('--', '-', $text);
		
		return $text;
	}
	/**
	* @toDBOT - convert string to DB object text, like db table field name or db table name format
	*/
	public function toDBObjectText($string)
	{
		$toSlug = $this->slugify($string);
		return str_replace('-','_', $toSlug);
	}
	
	public function tagsToString($text = "")
	{
		$text = str_replace('"', '&quot;', $text);
		$text = str_replace("'", '&#39;', $text);
		$text = str_replace("<", '&#60;', $text);
		$text = str_replace('>', '&#62;', $text);
		
		return $text;
	}
	
	public function toPrice($n, $d = 2, $currency=0)
	{
		$n = preg_replace('/[^0-9.]/', '', $n);
		
		if(substr($n, 0, 1) == '.') $n = substr($n, 1, strlen($n));
		
		if(substr($n, strlen($n) - 1, strlen($n)) == ' ') $n = substr($n, 0, strlen($n) - 1);
		
		return (($currency)?$currency:'') . number_format($n, $d);
	}
 }