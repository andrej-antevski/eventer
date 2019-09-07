<?php
    require 'config/config.php';
    $timezone = date_default_timezone_set("Europe/Skopje");

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
    if(isset($_GET['clear_notifications'])){
        $set_yes = mysqli_query($con,"UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn'");
    }
    else{
        $msg = $_GET['msg'];
        $msg = str_replace("&","&amp;",$msg);
        $msg = str_replace("<","&lt;",$msg);
        $msg = str_replace(">","&gt;",$msg);
        $msg = str_replace('"',"&quot;",$msg);
        $msg = str_replace("'","&#39;",$msg);        
        $date = date("Y-m-d H:i:s");
        $query = mysqli_query($con, "INSERT INTO messages VALUES ('', '$user_to', '$user_from', '$msg', '$date', 'no', 'no', 'no')");
    }
?>