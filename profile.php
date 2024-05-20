<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <title>Twitter</title>
	
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h3>Twitter</h3>
                <a href='.'>Go Home</a>
                <?php
                session_start();
                $user_id = $_SESSION['user_id'];

                function getTime($t_time){
                    $pt = time() - $t_time;
                    if ($pt>=86400)
                        $p = date("F j, Y",$t_time);
                    elseif ($pt>=3600)
                        $p = (floor($pt/3600))."h";
                    elseif ($pt>=60)
                        $p = (floor($pt/60))."m";
                    else
                        $p = $pt."s";
                    return $p;
                }
                
                if(isset($_GET['username'])){
                    $username = strtolower($_GET['username']);
                    include 'connect.php';
                    $stmt = $conn->prepare("SELECT id, username, followers, following, tweets FROM users WHERE username = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    if($row){
                        $id = $row['id'];
                        $username = $row['username'];
                        $tweets = $row['tweets'];
                        $followers = $row['followers'];
                        $following = $row['following'];
                        if($user_id){
                            if($user_id != $id){
                                $stmt2 = $conn->prepare("SELECT id FROM following WHERE user1_id = ? AND user2_id = ?");
                                $stmt2->bind_param("ii", $user_id, $id);
                                $stmt2->execute();
                                $result2 = $stmt2->get_result();
                                if($result2->num_rows >= 1){
                                    echo "<a href='unfollow.php?userid=$id&username=$username' class='btn btn-default btn-xs' style='float:right;'>Unfollow</a>";
                                } else {
                                    echo "<a href='follow.php?userid=$id&username=$username' class='btn btn-info btn-xs' style='float:right;'>Follow</a>";
                                }
                            }
                        } else {
                            echo "<a href='./register.php' class='btn btn-info btn-xs' style='float:right;'>Signup</a>";
                        }
                        echo "
                        <table style='margin-bottom:5px;'>
                            <tr>
                                <td>
                                    <img src='./default.jpg' style='width:35px;' alt='display picture'/>
                                </td>
                                <td valign='top' style='padding-left:8px;'>
                                    <h6><a href='./$username'>@$username</a>";
                        $stmt3 = $conn->prepare("SELECT id FROM following WHERE user1_id = ? AND user2_id = ?");
                        $stmt3->bind_param("ii", $id, $user_id);
                        $stmt3->execute();
                        $result3 = $stmt3->get_result();
                        if($result3->num_rows >= 1){
                            echo " - <i>Follows You</i>";
                        }
                        echo "</h6>
                                    <h6 style='width:300px;margin-top:-10px;'>Tweets: <a href='#'>$tweets</a> | Followers: <a href='#'>$followers</a> | Following: <a href='#'>$following</a></h6>
                                </td>
                            </tr>
                        </table>
                        ";
                        $stmt4 = $conn->prepare("SELECT username, tweet, timestamp FROM tweets WHERE user_id = ? ORDER BY timestamp DESC LIMIT 0, 10");
                        $stmt4->bind_param("i", $id);
                        $stmt4->execute();
                        $result4 = $stmt4->get_result();
                        while($tweet = $result4->fetch_assoc()){
                            echo "<div class='well well-sm' style='padding-top:4px;padding-bottom:8px; margin-bottom:8px; overflow:hidden;'>";
                            echo "<div style='font-size:10px;float:right;'>".getTime($tweet['timestamp'])."</div>";
                            echo "<table>";
                            echo "<tr>";
                            echo "<td valign=top style='padding-top:4px;'>";
                            echo "<img src='./default.jpg' style='width:35px;' alt='display picture'/>";
                            echo "</td>";
                            echo "<td style='padding-left:5px;word-wrap: break-word;' valign=top>";
                            echo "<a style='font-size:12px;' href='./".$tweet['username']."'>@".$tweet['username']."</a>";
                            $new_tweet = preg_replace('/@(\\w+)/','<a href=./$1>$0</a>',$tweet['tweet']);
                            $new_tweet = preg_replace('/#(\\w+)/','<a href=./hashtag/$1>$0</a>',$new_tweet);
                            echo "<div style='font-size:10px; margin-top:-3px;'>".$new_tweet."</div>";
                            echo "</td>";
                            echo "</tr>";
                            echo "</table>";
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Sorry, this profile doesn't exist.</div>";
                        echo "<a href='.' style='width:300px;' class='btn btn-info'>Go Home</a>";
                    }
                    $conn->close();
                }
                ?>
                <br>
            </div>
        </div>
    </div>
</body>
</html>
