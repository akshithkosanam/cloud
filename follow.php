<?php 
session_start();
$user_id = $_SESSION['user_id'];

if(isset($_GET['userid']) && isset($_GET['username'])){
    $follow_userid = $_GET['userid'];
    $follow_username = $_GET['username'];

    if($follow_userid != $user_id){
        include 'connect.php';

        // Check if the user is already following
        $query = $conn->prepare("SELECT id FROM following WHERE user1_id=? AND user2_id=?");
        $query->bind_param("ii", $user_id, $follow_userid);
        $query->execute();
        $query->store_result();

        if($query->num_rows < 1){
            // Insert new follow relationship
            $insert_follow = $conn->prepare("INSERT INTO following (user1_id, user2_id) VALUES (?, ?)");
            $insert_follow->bind_param("ii", $user_id, $follow_userid);
            $insert_follow->execute();

            // Update the user's following count
            $update_user_following = $conn->prepare("UPDATE users SET following = following + 1 WHERE id = ?");
            $update_user_following->bind_param("i", $user_id);
            $update_user_following->execute();

            // Update the followed user's followers count
            $update_followed_user_followers = $conn->prepare("UPDATE users SET followers = followers + 1 WHERE id = ?");
            $update_followed_user_followers->bind_param("i", $follow_userid);
            $update_followed_user_followers->execute();
        }

        $query->close();
        $insert_follow->close();
        $update_user_following->close();
        $update_followed_user_followers->close();

        $conn->close();
    }

    header("Location: ./".$follow_username);
} else {
    // Handle error case where userid or username is not set
    // Redirect to appropriate error page or perform error handling
    header("Location: ./error.php");
}
?>
