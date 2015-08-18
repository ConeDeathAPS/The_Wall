<?php session_start();
require('connection.php');

// session_destroy();

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Register for the Wall</title>
	<link rel="stylesheet" type="text/css" href="style_registration.css">
</head>
<body>
<div id="header">
	<img src="coding_dojo.png" alt="coding dojo logo">
	<ul>
		<li><p>The Wall</p></li>
	</ul>
</div>
<div id="registration_field">
	<h1>Welcome!</h1>
	<h4>Just fill out the information below to get posting!</h4>
	<form action="process.php" method="post">
	<table>
		<thead>
		</thead>
		<tr>
			<td><p>First Name:</p></td>
			<td><input type="text" name="first_name"
				<?php
				if (isset($_SESSION['errors']['fn'])) {
					echo "class='fix'";
				}
				?>
				>
			</td>
		</tr>
		<tr>
			<td><p>Last Name:</p></td> 
			<td><input type="text" name="last_name"		
				<?php
				if (isset($_SESSION['errors']['ln'])) {
					echo "class='fix'";
				}
				?>
				>			
			</td>
		</tr>
		<tr>
			<td><p>Email:</p></td>
			<td><input type="text" name="email"		
				<?php
				if (isset($_SESSION['errors']['em'])) {
					echo "class='fix'";
				}
				?>
				>
			</td>
		</tr>
		<tr>
			<td><p>Password:</p></td>
			<td><input type="password" name="password"		
				<?php
				if (isset($_SESSION['errors']['pw'])) {
					echo "class='fix'";
				}
				?>
				>
			</td>
		</tr>
		<tr>
			<td><p>Re-enter Password:</p></td>
			<td><input type="password" name="pass_confirm"
				<?php
				if (isset($_SESSION['errors']['pw'])) {
					echo "class='fix'";
				}
				?>
				>
			</td>
		</tr>
		</table>
			<input type="submit" name="register" class="butt" value="Submit">
			<h4><a href="./login.php" id = "old_user">Existing user? Click here!</a></h4>
		
	</form>
	<div id="reg_errors">
	<?php 

	// var_dump($_SESSION['errors']);
	// die();


	if (isset($_SESSION['errors'])) {
		foreach ($_SESSION['errors'] as $key => $value) {
			?>
			<p>
				<?= $value; ?>
			</p>
			<?php 
		}
		unset($_SESSION['errors']);
	}
	?>
	</div>
</div>
	
</body>
</html>