<?php session_start();

require_once('connection.php');
$validation = 0;

// var_dump($_POST);
// var_dump($_SESSION);
// die();


$_SESSION['errors'] = array();

//------------------------BEGIN CODE FOR REGISTRATION-------------------//
//check for registration submission
if (isset($_POST['register'])) {
	//validate first_name
	if(isset($_POST['first_name']) && $_POST['first_name'] != '') {
		$_SESSION['first_name'] = $_POST['first_name'];
		$first_name = escape_this_string($_SESSION['first_name']);
	} else {
		$_SESSION['errors']['fn'] = "Please enter your first name.";
		$validation++;
		header('location: registration.php');
	}
	//validate last_name
	if(isset($_POST['last_name']) && $_POST['last_name'] != '') {
		$_SESSION['last_name'] = $_POST['last_name'];
		$last_name = escape_this_string($_SESSION['last_name']);
	} else {
		$_SESSION['errors']['ln'] = "Please enter your last name.";
		$validation++;
		header('location: registration.php');
	}
	//validate email
	if(isset($_POST['email']) && $_POST['email'] != '' && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$_SESSION['email'] = $_POST['email'];
		$email = escape_this_string($_SESSION['email']);
	} else {
		$_SESSION['errors']['em'] = "Please enter a valid email address.";
		$validation++;
		header('location: registration.php');
	}
	//validate password
	if(isset($_POST['password']) && $_POST['password'] != '' && $_POST['password'] == $_POST['pass_confirm']) {
		$_SESSION['password'] = $_POST['password'];
		$password = escape_this_string($_SESSION['password']);
	} else {
		$_SESSION['errors']['pw'] = "Please make sure that your passwords match.";
		$validation++;
		header('location: registration.php');
	}
	if ($validation == 0) {
		$salt = bin2hex(openssl_random_pseudo_bytes(22));
		$password_encrypted = md5($password . ' ' . $salt);
		$query = "INSERT INTO users (first_name, last_name, email, password, created_at, updated_at, salt) VALUE ('{$first_name}', '{$last_name}', '{$email}', '{$password_encrypted}', NOW(), NOW(), '{$salt}')";
		run_mysql_query($query);
		$query = "SELECT id FROM users WHERE last_name = '" . $_SESSION['last_name'] . "'";
		$user_id = fetch_record($query);
		$_SESSION['user_id'] = $user_id['id'];
		header('location: wall.php');
	} else if ($validation > 0) {
		header('location: registration.php');
	}
	// var_dump($_SESSION);
	// die();
}

$validation = 0;

//------------------------BEGIN CODE FOR LOGIN----------------------//
if (isset($_POST['login'])) {
	//validate first_name
	if(isset($_POST['last_name_login']) && $_POST['last_name_login'] != '') {
		$last_name_login = escape_this_string($_POST['last_name_login']);
		//validate password (not with )
	} else {
		$_SESSION['errors']['ln_login'] = "Please enter your last name";
		$validation++;
	}
	if(isset($_POST['password_login']) && $_POST['password_login'] != '') {
		$password_login = escape_this_string($_POST['password_login']);
	} else {
		$_SESSION['errors']['pw_login'] = "Please enter your password";
		$validation++;
	} 
	if ($validation == 0) {
		$query = "SELECT * FROM users WHERE last_name = '" . $last_name_login . "'";
		$user = fetch_record($query);
		$password_login_encrypted = md5($password_login . ' ' . $user['salt']);
		//check password encryption
		// echo $password_login_encrypted . "<br>";
		// echo $user['password'];
		// die();
		if (!empty($user)) {
			if ($password_login_encrypted == $user['password']) {
				$_SESSION['first_name'] = $user['first_name'];
				$_SESSION['user_id'] = $user['id'];
				// var_dump($_SESSION);
				// die();
				header('location: wall.php');
			} else {
				$_SESSION['errors']['login'] = "Invalid password!";
				header('location: login.php');
			}
		} else {
				$_SESSION['errors']['login'] = "Invalid username/password combination!";
				header('location: login.php');
		}
	} else {
		header('location: login.php');
	}
}

if (isset($_GET['logout'])) {
	session_destroy();
	header('location: login.php');
}
// var_dump($_POST);
// // var_dump($_SESSION);
// die();

//------------------------BEGIN CODE FOR WALL----------------------//
//begin comment handling
if (isset($_POST['action']) && $_POST['action'] == 'post_comment') { //check what button was clicked
	if (isset($_POST['submit_comment']) && $_POST['submit_comment'] != null && !empty($_POST['comment'])){ //as long as the comment is not blank
		// var_dump($_POST);
		// die();
		$user_id = $_SESSION['user_id']; //set some variables for easier syntax in the query below
		$message_id = $_POST['message_id_send'];
		$comment = escape_this_string($_POST['comment']); //prevent MySQL injection
		// echo $comment;
		// echo $message_id;
		// echo $user_id;
		// die();
		$query = "INSERT INTO comments (comment, message_id, comment_user_id, created_at_cmt, updated_at_cmt) VALUE ('{$comment}', '{$message_id}', '{$user_id}', NOW(), NOW())";
		run_mysql_query($query);
		header('location: wall.php');
	} else {
		header('Location: wall.php');
	}	
	//message handling
} else if ($_POST['action'] == 'post_message') { //check what button was clicked
		if (isset($_POST['submit_message']) && $_POST['submit_message'] != null && !empty($_POST['submit_message'])) { //make sure the message isn't blank
		
		$user_id = $_SESSION['user_id']; //again, variables for easier syntax in the query
		$message = escape_this_string($_POST['message']); //prevent MySQL injection

		$query = "INSERT INTO messages (message, user_id, created_at_msg, updated_at_msg) VALUE ('{$message}', '{$user_id}', NOW(), NOW())";
		// var_dump($user_id);
		// die();
		run_mysql_query($query);
		header('location: wall.php');
		} else if ($_POST['submit_message'] == null) {
				header('location: wall.php');
		} else  {
		header("Location: wall.php"); //if no button was clicked, then refresh
		} 
//delete message handling
} else if ($_POST['action'] == 'delete_message') {
	if($_SESSION['user_id'] == $_POST['delete_message_id_send']) {//if the user ID logged in matches the ID of the creator
		$message_id = $_POST['message_id_send'];
		$query = "DELETE FROM messages WHERE id = " . $message_id;
		run_mysql_query($query);
		header("Location: wall.php");
	} else {
		header("Location: wall.php");
	}
//delete comment handling
} else if ($_POST['action'] == 'delete_comment') {
	if ($_SESSION['user_id'] == $_POST['delete_comment_id_send']) {
		$comment_id = $_POST['comment_id_send'];
		$query = "DELETE FROM comments WHERE id = " . $comment_id;
		run_mysql_query($query);
		header("Location: wall.php");
	} else {
		header("Location: wall.php");
	}
}


// var_dump($_POST);
// die();




?>