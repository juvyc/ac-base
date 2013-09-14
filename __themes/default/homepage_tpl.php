<!DOCTYPE html>
<html>
	<head>
		<title>AC-Base Framework</title>
		<link rel="stylesheet" href="<?=$theme_root;?>assets/css/style.css" media="all"/>
	</head>
	<body>
		<div class="header">
			<div class="headerPanel">
				<a href="" class="logo"><img src="<?=$theme_root;?>assets/css/img/logo.png"/></a>
			</div>
		</div>
		<div class="contentsPanel">
			<div class="welcomeMessage">
				<h1>Welcome!</h1>
				<p>You have successfully installed the AC-Base Framework.</p>
				<p class="codes">
					<b style="color:red;">NOTE</b>: <i>We expect that you are running the lastest version of PHP in your server like PHP 5.3+. 
					However if you run below that version, you are required to edit the <span>$GLOBALS['path']['base_dir'] = realpath(__DIR__);</span>
					and change the value to a static one like <span>$GLOBALS['path']['base_dir'] = '/home/var/public_html';</span>. 
					For more information regarding installation process, please visit our <a href="http://docs.ac-base.org/installation/">installation</a> guidelines.
					</i>
				</p>
				<h2>Get Started</h2>
				<p class="codes">The controller that generate this page can be found at <span>[apps_path]/builder/default/controller/default_clr.php</span></p>
				<p class="codes">The model can be found at <span>[apps_path]/builder/default/model/default_mod.php</span></p>
				<p class="codes">The view can be found at <span>[apps_path]/builder/default/view/default_view.php</span></p>
				<p class="codes">And the template can be found at <span>[themes_path]/default/homepage_tpl.php</span></p>
				<p><a href="http://docs.ac-base.org" class="button-yg">Read Documentation</a></p>
			</div>
			
			<div class="footer">
				<p><a href="http://ac-base.org">AC-Base</a> is released under the MIT license.</p>
				<p class="version">Version: 1.2</p>
			</div>
		</div>
	</body>
</html>