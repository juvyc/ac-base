<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title; ?></title>
	
	<?php 
		foreach($assets_css as $css){
		  echo '<link rel="stylesheet" href="'. base_url . $css .'"/>';
		}
	?>
	
	<?php 
		foreach($assets_js as $js){
	?>
		<script src="<?php echo base_url . $css; ?>"></script>
	<?php
		}
	?>
	
</head>
<body>
