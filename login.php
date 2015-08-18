<?php session_start();
require('connection.php');

// session_destroy();
if (isset($_GET['logout'])) {
	session_destroy();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Login to the Wall</title>
	<link rel="stylesheet" type="text/css" href="style_login.css">
</head>
<body>
<div id="header">
	<img src="coding_dojo.png" alt="coding dojo logo">
	<ul>
		<li><p>The Wall</p></li>
	</ul>
</div>
<div id="login_field">
	<h1>Login</h1>
	<h4>Welcome back! Go ahead and login to get started!</h4>
	<form action="process.php" method="post">
		<p>Last Name: <input type="text" name="last_name_login"
		<?php
		if (isset($_SESSION['errors']['ln_login'])) {
			echo "class='fix'";
		}
		?>
		></p>
		<p>Password: <input type="password" name="password_login"
		<?php
		if (isset($_SESSION['errors']['pw_login'])) {
			echo "class='fix'";
		}
		?>
		></p>
		<input type="submit" name="login" class="butt" value="Login">
		<h4><a href="./registration.php" id = "new_user">New user? Click here!</a></h4>
	</form>
	<div id="login_errors">
		<?php
		if (isset($_SESSION['errors']['login']) || isset($_SESSION['errors']['pw_login']) || isset($_SESSION['errors']['ln_login'])) {
			foreach ($_SESSION['errors'] as $key => $value) {
				?>
				<p>
					<?= $value; ?>
				</p>
				<?php 
			}
		}
		?>
	</div>
</div>
	
</body>
</html>