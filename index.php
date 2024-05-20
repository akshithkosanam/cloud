<?php
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$error_msg = '';

if(isset($_POST['login-btn']) && $_POST['login-btn']=="login-submit"){
  if($_POST['username']!="" && $_POST['password']!=""){
    $username = strtolower($_POST['username']);
    include "connect.php";
    $query = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    $query->close();
    $conn->close();

    if($result->num_rows >= 1){
      $password = md5($_POST['password']);
      if($password == $row['password']){
        $_SESSION['user_id'] = $row['id'];
        header('Location: .');
        exit;
      } else {
        $error_msg = "Incorrect username or password";
      }
    } else {
      $error_msg = "Incorrect username or password";
    }
  } else {
    $error_msg = "All fields must be filled out";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=425px, user-scalable=no">
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
  <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style.css">
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
  <title>Twitter</title>
  
</head>
<body>
  <h3>Twitter</h3>
  <?php
  if($user_id){
    include "dashboard.php";
    exit;
  }
  ?>
  <form role="form" action="index.php" method="POST">
    <div class="input-group">
      <span class="input-group-addon">@</span>
      <input type="text" class="form-control" placeholder="Username" name="username">
    </div>
    <input type="password" class="form-control" placeholder="Password" name="password">
    <?php
    if($error_msg){
        echo "<div class='alert alert-danger'>".$error_msg."</div>";
    }
    ?>
    <div class="btn-group">
      <a href="register.php" class="btn btn-success">Register</a>
      <button type="submit" class="btn btn-info" name="login-btn" value="login-submit">Log In</button>
    </div>
  </form>
  <br>
</body>
</html>

