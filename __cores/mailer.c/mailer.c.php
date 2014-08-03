<?php
 /**
 * @_Mailer
 * Type: Object
 * Desc: To send emails with or without attachements and plain or html contents
 */
 class _Mailer
 {
	//Build up a customizable variables for user
	public $To = '',
		   $From = '',
		   $CC = '',
		   $BCC = '',
		   $ReplyTo = '',
		   $Title = "", 
		   $Message = "", 
		   $Attachement = array(),
		   $PlainText = false;
		   
	private $params = array();	   
	
	private function setUpTo(){
		if(is_array($To)){
			$this->params['to'] = implode(', ', $To);
		}else{
			$this->params['to'] = $To;
		}
	}
	
	private function setUpFrom(){
		if(is_array($From)){
			$this->params['from'] = implode(', ', $From);
		}else{
			$this->params['from'] = $From;
		}
	}
	
	private function setUpBCC(){
		if(is_array($BCC)){
			$this->params['bcc'] = implode(', ', $BCC);
		}else{
			$this->params['bcc'] = $BCC;
		}
	}
	
	private function setUpReplyTo(){
		if(is_array($ReplyTo)){
			$this->params['replyto'] = implode(', ', $ReplyTo);
		}else{
			$this->params['replyto'] = $ReplyTo;
		}
	}
	
	private function setUpCC(){
		if(is_array($CC)){
			$this->params['cc'] = implode(',', $CC);
		}else{
			$this->params['cc'] = $CC;
		}
	}
	
	private function setUpAttachement(){
		if(is_array($Attachement)){
			$this->params['attachement'] = $Attachement;
		}else{
			$this->params['attachement'] = array($Attachement);
		}
	}
	
	private function prepare()
	{
		$this->setUpTo();
		$this->setUpFrom();
		$this->setUpBCC();
		$this->setUpReplyTo();
		$this->setUpCC();
		$this->setUpAttachement();
		
		$this->params['headers'] = 'To:' . $this->params['to'] . "\r\n";
		
		if($this->params['from'])
			$this->params['headers'] .= 'From:' . $this->params['from'] . "\r\n";
		
		if($this->params['cc'])	
			$this->params['headers'] .= 'Cc:' . $this->params['cc'] . "\r\n";
		
		if($this->params['bcc'])	
			$this->params['headers'] .= 'Bcc:' . $this->params['bcc'] . "\r\n";
		
		$message = '';
			
			// boundary 
			$semi_rand = md5(time()); 
			$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
			
			if($this->PlainText){
				// multipart boundary 
				$message .= "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" 
							. "Content-Type: text/plain; charset=\"UTF-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $this->message . "\n\n"; 
			}else{
				// multipart boundary 
				$message .= "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" 
							. "Content-Type: text/html; charset=\"UTF-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $this->message . "\n\n"; 
			}
			
		if(count($this->params['attachement'])){
			
			// headers for attachment 
			$this->params['headers'] .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
			
			$message .= "--{$mime_boundary}\n";
		
			$files = $this->params['attachement'];
			
			// adding attachment
			for($x=0;$x<count($files);$x++){
				$file = fopen($files[$x],"rb");
				$data = fread($file,filesize($files[$x]));
				fclose($file);
				$data = chunk_split(base64_encode($data));
				$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$files[$x]\"\n" . 
				"Content-Disposition: attachment;\n" . " filename=\"$files[$x]\"\n" . 
				"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
				$message .= "--{$mime_boundary}\n";
			}
			
		}
		
		$this->params['message'] = $message;
		
	}
	
	public function send()
	{
		$this->prepare();
		
		return @mail($this->params['to'], $this->Title, $this->params['message'], $this->params['headers']); 
	}
	
 }