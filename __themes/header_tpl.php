<!DOCTYPE html>
<html class="<?php echo $htmlClass ?? ''; ?>">
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
		<script src="<?php echo base_url . $js; ?>"></script>
	<?php
		}
	?>
	
</head>
<body class="<?php echo $bodyClass ?? ''; ?>">
