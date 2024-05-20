<?php
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$error_msg = '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, user-scalable=no">

    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Twitter</title>
    
</head>
<body>
    <form action="register.php" method="POST" role="form" style="max-width: 300px; margin-left: 20px;">
        <h3>Twitter</h3>
        <h4>Register For An Account</h4>
        <?php
        include 'connect.php'; // Include the connect.php file to access database connection details

        if(isset($_POST['btn']) && $_POST['btn']=="submit-register-form"){
            if($_POST['username']!="" && $_POST['password']!="" && $_POST['confirm-password']!=""){
                if($_POST['password']==$_POST['confirm-password']){
                    // Create a MySQLi connection using connection details from connect.php
                    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $username = strtolower($_POST['username']);

                    // Prepare the query using a prepared statement
                    $stmt = $conn->prepare("SELECT username FROM users WHERE username=?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Check if the username already exists
                    if($result->num_rows >= 1) {
                        $error_msg="Username already exists please try again";
                    } else {
                        $password = md5($_POST['password']);
                        // Insert the new user into the database
                        $stmt = $conn->prepare("INSERT INTO users(username, password) VALUES (?, ?)");
                        $stmt->bind_param("ss", $username, $password);
                        $stmt->execute();
                        $stmt->close();

                        echo "<div class='alert alert-success'>Your account has been created!</div>";
                        echo "<a href='.' class='btn btn-info'>Go Home</a>";
                        exit;
                    }

                    $conn->close();
                } else {
                    $error_msg="Passwords did not match";
                }
            } else {
                $error_msg="All fields must be filled out";
            }
        }
        ?>

        <div class="input-group" style="margin-bottom:10px;">
            <span class="input-group-addon">@</span>
            <input type="text" class="form-control" placeholder="Username" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>">
        </div>

        <input type="password" style="margin-bottom:10px;" class="form-control" placeholder="Password" name="password">
        <input type="password" style="margin-bottom:10px;" class="form-control" placeholder="Confirm Password" name="confirm-password">
        <?php
        if($error_msg){
            echo "<div class='alert alert-danger'>".$error_msg."</div>";
        }
        ?>
        <button type="submit" style="width:100%;" class="btn btn-success" name="btn" value="submit-register-form">Register</button>
        <a href="." style="width:100%;" class="btn btn-info">Go Home</a>
    </form>
    <br>
</body>
</html>
