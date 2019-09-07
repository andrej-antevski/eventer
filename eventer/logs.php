
<?php
    require 'config/config.php'; 

    if(isset($_SESSION['username'])){
        $userLoggedIn=$_SESSION['username'];
        $user_details_query=mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
        $user=mysqli_fetch_array($user_details_query);
    }
    else {
        header("Location: register.php");
    }
    $user_from = $_GET['user_from'];
    $user_to = $_GET['user_to'];

    if($userLoggedIn != $user_from && $userLoggedIn != $user_to) header("Location: index.php");

$result1 = mysqli_query($con, "SELECT * FROM messages WHERE (user_from='$user_from' AND user_to='$user_to') OR (user_to='$user_from' AND user_from='$user_to') ORDER by id DESC");

$seeMessage = mysqli_query($con, "UPDATE messages SET opened='yes' WHERE (user_from='$user_from' AND user_to='$userLoggedIn') OR (user_to='$userLoggedIn' AND user_from='$user_to')");

$data ="";
while($extract = mysqli_fetch_array($result1)){

        $user_to = $extract['user_to'];
        $user_from = $extract['user_from'];
        $body = $extract['body'];

        $div_top = ($user_to == $userLoggedIn) ? "<div class='message' id='green' title='$userLoggedIn'>" : "<div class='message' id='blue' title='$user_to'>";
        $data = $data . $div_top . $body . "</div><br><br>";
	}
    echo $data;
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    </head>
</html>
