<?php session_start();
require('connection.php');

$query = "SELECT * FROM users JOIN messages ON users.id = messages.user_id ORDER BY messages.created_at_msg DESC";
$posts = fetch_all($query);

// var_dump($posts);
// var_dump($comments);
// die();

?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Welcome to the Wall</title>
	<link rel="stylesheet" type="text/css" href="style_wall.css">
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script type="text/javascript">

	$(document).ready(function() {
		$('.post_comment_form').hide();
		$('.add_comment_span').click(function(){
			$(this).hide();
			$(this).next().show();
		});
	}) 
	</script>
</head>
<body>
<div id="header">
	<img src="coding_dojo.png" alt="coding dojo logo">
	<ul>
		<li><p>
		<?php 
		if (isset($_SESSION['first_name'])) {
			?>Welcome 
			<?= $_SESSION['first_name']; 
		}
		?>
		</p></li>
		<li><a href="./login.php?logout=true">Log Out</a></li>
	</ul>
</div>
	<div id="content">
	<h1>Welcome to the wall!</h1>
	<form action="process.php" method="post" id="add_message">
		<input type="hidden" name="submit_message" value="true">
		<input type="hidden" name="action" value="post_message">
		<textarea name="message" id="message" value="post_message"></textarea>
		<input type="submit" class="butt" name="submit_message_butt" value="Submit message">
	</form>
<?php
//---------------BEGIN DATABASE DUMPING---------------//
if (isset($posts) && $posts != null){
	foreach ($posts as $key => $value) {

		//get the id for this message
		$message_id = $value['id'];	
		//get the ID for the currently logged in user	
		$users_id = $_SESSION['user_id'];

		// var_dump($posts);
		// die();

		$query = "SELECT * FROM comments JOIN messages ON comments.message_id = messages.id WHERE comments.message_id = '" . $message_id . "'";
		$comments = fetch_all($query);
		?>
		<!-----------------------BEGIN MESSAGE DISPLAY-------------------->
		
		<div class="messages">
			<h2><?= $value['message']; ?></h2>
			<p class="message_info">Submitted: <?php $msg_date = date_create($value['created_at_msg']); ?>
			<?= date_format($msg_date, 'm/d/Y H:i'); ?> by <?= $value['first_name'] . ' ' . $value['last_name']?></p>
			<form action="process.php" method="post" class="delete_form">
				<input type="hidden" name="message_id_send" value="<?= $message_id; ?>">
				<input type="hidden" name="delete_message_id_send" value="<?= $value['user_id']; ?>">
				<input type="hidden" name="action" value="delete_message">
				<input type="submit" name="submit_delete" class="butt_small butt_delete_msg" value="Delete";>
			</form>
		</div>

		<!-----------------------END MESSAGE DISPLAY-------------------->


		<?php 
		// var_dump($_SESSION);
		// var_dump($comments);
		// die();

		//----------------------BEGIN COMMENT DISPLAY--------------------//

		if (isset($comments)) {
			foreach ($comments as $index => $name) {	
				//grab the commenting user's name
				$query = "SELECT first_name, last_name FROM users JOIN comments ON users.id = comments.comment_user_id WHERE users.id = '" . $name['comment_user_id'] . "'"; 
				$comment_name = fetch_record($query);

				//grab the comment id
				$query = "SELECT id FROM comments WHERE created_at_cmt = '" . $name['created_at_cmt'] . "'";
				$comment_id = fetch_record($query);
				?>

				<!-- begin comment formatting -->
				<div class="comments">
					<h2><?= $name['comment']; ?></h2>
					<p class="comment_info">Submitted: <?php $cmt_date = date_create($name['created_at_cmt']); ?> 
					<?= date_format($cmt_date, 'm/d/Y H:i'); ?> by <?= $comment_name['first_name'] . ' ' . $comment_name['last_name']; ?></p>
					<form action="process.php" method="post" class="delete_form">
						<input type="hidden" name="comment_id_send" value="<?= $comment_id['id']; ?>">
						<input type="hidden" name="delete_comment_id_send" value="<?= $name['comment_user_id']; ?>">
						<input type="hidden" name="action" value="delete_comment">
						<input type="submit" name="submit_delete" class="butt_small butt_delete_cmt" value="Delete";>
					</form>
				</div>
				<!----------------------END COMMENT DISPLAY-------------------->

			<?php
			}
		}
			?>
			<!--here we are adding the 'add comment' forms after every message and its comments-->
			<span class="add_comment_span">
				<button class="butt_small add_comment_butt" name="add_comment">Add Comment</button>
			</span>
			<form action="process.php" class="post_comment_form" method="post">
				<input type="hidden" name="message_id_send" value="<?= $message_id; ?>">
				<input type="hidden" name="action" value="post_comment">
				<textarea name="comment" id="comment"></textarea>
				<input type="submit" name="submit_comment" class="butt_small post_comment_butt">
			</form>
<?php
	}
} else {//if there are no messages to show then say so!
	?>
	<div class="message"><h2>There are no messages to show!</h2></div>
	<?php
}
?>
	</div>
</body>
</html>
<?php unset($posts); ?>