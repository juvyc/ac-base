<?php #//php_start\\;	

	set_error_handler('system_error_handler');
	
	
	function system_error_handler($code, $message, $errFile, $errLine) {
		global $_error_parameters;
	  //Set message/log info
	  $subject = "$message: MAJOR PROBLEM at " . $_SERVER['HTTP_HOST'] . ': ' . date("F j, Y, g:i a");
	  $body = "errFile: $errFile\r\n"
				. "errLine: $errLine\r\n"
				. trigger_dump($GLOBALS);

		/*
		An email will be sent to the site administrator.
		Its subject line will have the date and time it occurred while
		the body will contain the state of all of the global variables. This information
		is obtained through the function trigger_dump.
	  */
	  if($_error_parameters['ADMIN_EMAIL_ADDR'] != ""){
		@mail($_error_parameters['ADMIN_EMAIL_ADDR'],$subject,$body);
	  }

	  //The same subject line and body of the email will get written to the error log.
	  error_log("$subject\r\n $body");
	
	   $_SESSION['_ERROR_']['CODE'] = $code;
	   $_SESSION['_ERROR_']['MESSAGE'] = $message;
	   $_SESSION['_ERROR_']['FILE'] = $errFile;
	   $_SESSION['_ERROR_']['LINE'] = $errLine;
	   $_SESSION['_ERROR_']['EXEC'] = 'TRUE';
		
	  /*
		We don't want users to know the true nature of the problem so
		we just redirect them to a generic error page that has been created.
		The generic page should have a simple message, such as "System down
		for maintenance." The key idea is not to let any potentially malicious
		user learn about the actual problem that had occurred.
	  */
	   header("HTTP/1.0 500 Internal Error");
	   require_once(dirname(__FILE__) . '/error.view.c.php');
	   exit;
	}

	  /*
		The function below is called by the mail
		and error_log calls above.
	  */

	function trigger_dump( $mixed,$type = 0 ) {
	  /*
		$mixed will handle whatever you may decide to pass to it.
		$type will determine what this function should do with the
		information obtained by var_dump
	  */
	  switch( (int) $type ) {
		case 0:
		  /*
		  Grab the contents of the output buffer
		  when you want to send the information
		  back to the calling function
		  */
		  ob_start();
		  var_dump($mixed);
		  //If you are using PHP ver. 4.3 use the
		  //code below:
		  return ob_get_clean();
		  //If you are using an earlier version
		  //of PHP, then use the code below:
		  $ob_contents = ob_get_contents();
		  ob_end_clean();
		  return $ob_contents;
		case 1:
		  /*
		   When you want to display the information to the browser
		  */
		  print '<pre>';
		  var_dump($mixed);
		  print '</pre>';
		  break;
		case 2:
		  //When you want your error handler to deal with the information
		  ob_start();
		  var_dump($mixed);
		  //If you are using PHP ver. 4.3 use the
		  //code below:
		  trigger_error(ob_get_clean());
		  break;
		  //If you are using an earlier version
		  //of PHP, then use the code below:
		  $ob_contents = ob_get_contents();
		  ob_end_clean();
		  trigger_error($ob_contents);
		  break;
		}
	} 

#//php_end\\;?>