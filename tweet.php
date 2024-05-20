<?php 
session_start();
$user_id = $_SESSION['user_id'];

if($user_id){
	if($_POST['tweet']!=""){
		$tweet = htmlentities($_POST['tweet']);
		$timestamp = time();
		include 'connect.php';
		
		// Prepare and execute a SELECT query to retrieve the username
		$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
		$stmt->bind_param("i", $user_id);
		$stmt->execute();
		$stmt->bind_result($username);
		$stmt->fetch();
		$stmt->close();
		
		// Prepare and execute an INSERT query to add the tweet
		$stmt = $conn->prepare("INSERT INTO tweets (username, user_id, tweet, timestamp) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("siss", $username, $user_id, $tweet, $timestamp);
		$stmt->execute();
		$stmt->close();
		
		// Update the user's tweet count
		$stmt = $conn->prepare("UPDATE users SET tweets = tweets + 1 WHERE id = ?");
		$stmt->bind_param("i", $user_id);
		$stmt->execute();
		$stmt->close();
		
		$conn->close();
		header("Location: .");
	}
}
?>
