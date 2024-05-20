<?php 
session_start();
$user_id = $_SESSION['user_id'];

if(isset($_GET['userid']) && isset($_GET['username'])){
    $unfollow_userid = $_GET['userid'];
    $unfollow_username = $_GET['username'];

    if($unfollow_userid != $user_id){
        include 'connect.php';

        // Check if the user is following
        $query = $conn->prepare("SELECT id FROM following WHERE user1_id=? AND user2_id=?");
        $query->bind_param("ii", $user_id, $unfollow_userid);
        $query->execute();
        $query->store_result();

        if($query->num_rows >= 1){
            // Unfollow the user
            $delete_follow = $conn->prepare("DELETE FROM following WHERE user1_id=? AND user2_id=?");
            $delete_follow->bind_param("ii", $user_id, $unfollow_userid);
            $delete_follow->execute();

            // Update the user's following count
            $update_user_following = $conn->prepare("UPDATE users SET following = following - 1 WHERE id = ?");
            $update_user_following->bind_param("i", $user_id);
            $update_user_following->execute();

            // Update the unfollowed user's followers count
            $update_unfollowed_user_followers = $conn->prepare("UPDATE users SET followers = followers - 1 WHERE id = ?");
            $update_unfollowed_user_followers->bind_param("i", $unfollow_userid);
            $update_unfollowed_user_followers->execute();
        }

        $query->close();
        $delete_follow->close();
        $update_user_following->close();
        $update_unfollowed_user_followers->close();

        $conn->close();
    }

    header("Location: ./".$unfollow_username);
} else {
    // Handle error case where userid or username is not set
    // Redirect to appropriate error page or perform error handling
    header("Location: ./error.php");
}
?>
