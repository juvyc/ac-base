<!DOCTYPE html>
<html>
	<head>
		<title>Test Form</title>
	</head>
	<body>
		<form method="POST">
			<fieldset>
				<legend>Testing Form Submission</legend>
				<label>Full Name</label>
				<input type="text" name="full_name" value="<?=$full_name;?>"/><br/>
				
				<label>Email</label>
				<input type="text" name="email" value="<?=$email;?>"/><br/>
				<input type="submit" value="Submit"/>
			</fieldset>
		</form>
	</body>
</html>