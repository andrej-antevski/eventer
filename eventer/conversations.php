<?php

require 'config/config.php'; 
include 'includes/classes/User.php';
$userLoggedIn = $_GET['loggedIn'];
if(!$userLoggedIn){
    header("Location:index.php");
}

function getLatestMessage($userLoggedIn,$user2,$con){
    $details_array = array();
    $query = mysqli_query($con,"SELECT body,user_to,date FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$user2') OR (user_to='$user2' AND user_from='$userLoggedIn') ORDER BY id DESC LIMIT 1 ");
    
    $row = mysqli_fetch_array($query);
    $sent_by = ($row['user_to'] == $userLoggedIn )? "They: " : "You: ";
    

    $date_time_now= date("Y-m-d H:i:s");
    $start_date = new DateTime($row['date']);//time of post
    $end_date = new DateTime($date_time_now);//current time
    $interval = $start_date->diff($end_date);
    //for years
    if($interval->y >= 1){
        if($interval->y == 1) $time_message = $interval->y . " year ago";
        else $time_message = $interval->y . " years ago";
    }
    //for months and days
    else if ($interval->m >= 1){
        if($interval->d == 0) $days = "ago";
        else if ($interval->d ==1 ) $days = $interval->d . " day ago";
        else $days =$interval->d . " days ago";
        if($interval->m == 1) $time_message = $interval->m . " month " . $days;
        else $time_message = $interval->m . " months " . $days;       
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

    //if message is opened
    $if_opened = mysqli_query($con,"SELECT opened FROM messages WHERE user_to='$userLoggedIn' AND user_from='$user2' ORDER BY id DESC LIMIT 1");
    $row_opened = mysqli_fetch_array($if_opened);
    
    array_push($details_array,$sent_by);
    array_push($details_array,$row['body']);
    array_push($details_array,$time_message);
    if(mysqli_num_rows($if_opened)>0) array_push($details_array,$row_opened['opened']);
    else array_push($details_array,"yes");
    return $details_array;
}
function getConvos($con,$userLoggedIn) {
        $return_string = "";
        $convos = array();

        $query = mysqli_query($con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");

        while($row = mysqli_fetch_array($query)) {
            /*check if account is closed if closed continue
                $user_close = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];
                $close_q = mysqli_query($con,"SELECT * FROM users WHERE username='$user_close' AND user_closed='yes'");
                if(mysqli_num_rows($close_q) > 0 ) continue;
            //end check*/
            $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

            if(!in_array($user_to_push, $convos)) {
                array_push($convos, $user_to_push);
            }
        }

        foreach($convos as $username) {
            $user_found_obj = new User($con, $username);
            $latest_message_details = getLatestMessage($userLoggedIn, $username,$con);

            $dots = (strlen($latest_message_details[1]) >= 23) ? "..." : "";
            $split = str_split($latest_message_details[1], 23);
            $split = $split[0] . $dots; 
            if($latest_message_details[3]=='yes'){
            $return_string .= "<a href='messages.php?u=$username' style='text-decoration: none; color:#353535;'> <div class='user_found_messages'>
                                <img src='" . $user_found_obj->getProfilePic() . "'>
                                " . $user_found_obj->getFirstAndLastName() . "
                                <span class='timestamp_smaller' id='grey'><br> " . $latest_message_details[2] . "</span>
                                <p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . " </p>
                                </div>
                                </a>";
            }
            else $return_string .= "<b><a href='messages.php?u=$username' style='text-decoration: none; color:#353535;'> <div class='user_found_messages'>
                                    <img src='" . $user_found_obj->getProfilePic() . "'>
                                    " . $user_found_obj->getFirstAndLastName() . "
                                    <span class='timestamp_smaller' id='grey'><br> " . $latest_message_details[2] . "</span>
                                    <p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . " </p>
                                    </div>
                                    </a></b>";
        }

        return $return_string;

    }
    
    echo getConvos($con,$userLoggedIn);
?>
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
