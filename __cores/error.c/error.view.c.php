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
					ini_set("auto_detect_line_endings", true);
					$lines = file($_SESSION['_ERROR_']['FILE']);
					foreach($lines as $line => $codes_list){
						$line = $line + 1;
						if($line == $_SESSION['_ERROR_']['LINE']){
							echo '<pre>'. $line . '&nbsp;&nbsp;<span class="prior_hightlighted">' . htmlspecialchars($codes_list) .'</span></pre>';
						}else{
							echo '<pre>'. $line . '&nbsp;&nbsp;' . htmlspecialchars($codes_list) .'</pre>';
						}
					}
				?>
				</div>
			</div>
		</div>
	</body>
</html>	