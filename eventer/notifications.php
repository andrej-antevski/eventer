<?php
    require 'config/config.php';
    include 'includes/classes/User.php';
    if(isset($_SESSION['username'])){
        $userLoggedIn=$_SESSION['username'];
        $user_details_query=mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
        $user=mysqli_fetch_array($user_details_query);
    }
    else {
        header("Location: register.php");
    }

    $date_query = mysqli_query($con, "SELECT MAX(datetime) AS dt, COUNT(*) FROM `notifications` WHERE user_to = '$userLoggedIn' AND closed='no' GROUP BY `link`,`message` ORDER BY dt DESC LIMIT 4");
    if(mysqli_num_rows($date_query) == 0) echo "<div class='notifications'>You don't have any notifications</div>";
    while($date_array = mysqli_fetch_array($date_query)){
        $user_query = mysqli_query($con, "SELECT * FROM notifications WHERE user_to = '$userLoggedIn' AND closed='no' AND datetime = '$date_array[0]' LIMIT 1");
        $user_array = mysqli_fetch_array($user_query);
        $user_from = $user_array['user_from'];
        //date
        $date_time_now= date("Y-m-d H:i:s");
        $start_date = new DateTime($date_array[0]);//time of post
        $end_date = new DateTime($date_time_now);//current time
        $interval = $start_date->diff($end_date);
        //for years
        if($interval->y >= 1){
            if($interval == 1) $time_message = $interval->y . " year ago";
            else $time_message = $interval->y . " years ago";
        }
        //for months and days
        else if ($interval->m >= 1){
            if($interval->d == 0) $days = "ago";
            else if ($interval->d ==1 ) $days = $interval->d . " day ago";
            else $days =$interval->d . " days ago";
            if($interval->m == 1) $time_message = $interval->m . " month" . $days;
            else $time_message = $interval->m . " months" . $days;       
        }
        else if($interval->d >= 1){
            if($interval->d==1) $time_message = "Yesterday";
            else $time_message = $interval->d . " days ago";
        }
        else if($interval->h >= 1){
            if($interval->d==1) $time_message = $interval->h . " hour ago";
            else $time_message = $interval->h . " hours ago";
        }
        else if($interval->i >= 1){
            if($interval->i==1) $time_message = $interval->i . " minute ago";
            else $time_message = $interval->i . " minutes ago";
        }
        else{
            if($interval->s < 30) $time_message = "Just now";
            else $time_message = $interval->s . " seconds ago";                    
        }

        $user_pic_obj = new User ($con, $user_array['user_from']);
        $pic = $user_pic_obj->getProfilePic();
        $final_str = "";
        $num = $date_array[1];
        if($num <=2){
            $link = $user_array['link'];
            $message = $user_array['message'];
            $details_query = mysqli_query($con, "SELECT * FROM notifications WHERE user_to='$userLoggedIn' AND link='$link' AND message='$message' AND closed='no' ORDER BY datetime DESC");
            while($details_array = mysqli_fetch_array($details_query)){

                $user_obj = new User ($con, $details_array['user_from']);
                $fullname = $user_obj->GetFirstAndLastName();
                $final_str .= $fullname . ', ';
            }
            $final_str = substr_replace($final_str ,"",-2);
            $final_str .= ' ' . $message . ' <br><span class="timentf">' . $time_message . '</span><br>';
        }
        else{
            $user_obj = new User ($con, $user_array['user_from']);
            $fullname = $user_obj->GetFirstAndLastName();
            $final_str .= $fullname . ' and ' . ($num-1) . ' others ' . $user_array['message'] . ' <br><span class="timentf">' . $time_message . '</span><br>';
        }
        $link = $user_array['link'];
        echo "<a href='post.php?post_id=$link'><div class='notifications'><img src='$pic'>" . $final_str . "</div></a>";
    }

?><link rel="stylesheet" type="text/css" href="assets/css/style.css">
