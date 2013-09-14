<?php #//php_start\\;	
	
	/**
	* Starting session
	*/
	session_start();
	
	$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
	
	if(isset($_SESSION['_ERROR_']) && count($_SESSION['_ERROR_']) && $_SESSION['_ERROR_']['EXEC'] == "FALSE" && isset($_GET['referer']) && $_GET['referer'] != ""){		
		header('location:http://' . $s . $_GET['referer']);
	}else if(!isset($_SESSION['_ERROR_']) || !isset($_GET['referer'])){
		header('location:../');
	}
	
	$_SESSION['_ERROR_']['EXEC'] = 'FALSE';
	
#//php_end\\;?>

<!DOCTYPE html>
<html>
	<head>
		<title>Error found: <?=$_SESSION['_ERROR_']['MESSAGE'];?></title>
		<style type="text/css">
			*{margin:0px; padding:0px; font-family:verdana; font-size:12px;}
			.errorPanel{width:750px; margin:0 auto; padding:15px; border:1px solid #E5E5E5; margin-top:20px; overflow:auto; min-height:300px; margin-bottom:20px;}
			.errorPanel h1{margin-top:5px; font-size:18px; color:#707070;}
			.errorPanel p{margin-top:}
			.syntaxPanel{width:100%; background:#EDEDED; margin-top:10px;}
			.syntaxContainer{width:95%; padding: 15px 0px; margin:0 auto;}
			.syntaxContainer .prior_hightlighted{color:#CC1212; font-weight:bolder; text-decoration:underline;}
		</style>
	</head>
	<body>
		<div class="errorPanel">
			<h1>Error found: <?=$_SESSION['_ERROR_']['MESSAGE'];?> @ <?=$_SESSION['_ERROR_']['FILE'];?> on line <?=$_SESSION['_ERROR_']['LINE']?></h1>
			<div class="syntaxPanel">
				<div class="syntaxContainer">
				<?php
					$get_files_data = explode("\n", str_replace('<', '&lt;',file_get_contents($_SESSION['_ERROR_']['FILE'])));
					$line = 1;
					foreach($get_files_data as $codes_list){
						
						if($line == $_SESSION['_ERROR_']['LINE']){
							echo '<pre>'. $line . '&nbsp;&nbsp;<span class="prior_hightlighted">' . $codes_list .'</span></pre>';
						}else{
							echo '<pre>'. $line . '&nbsp;&nbsp;' . $codes_list .'</pre>';
						}
						$line++;
					}
				?>
				</div>
			</div>
		</div>
	</body>
</html>	