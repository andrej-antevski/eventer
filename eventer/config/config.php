<?php
    ob_start();
    session_start();
    $con = mysqli_connect("localhost","root","","social");  // connection to database variable
    $timezone = date_default_timezone_set("Europe/Skopje");
    if(!$con){
        echo "Failed to connect to database server. Reason: ".mysqli_connect_error(); //if there is an error print it
    }

?>