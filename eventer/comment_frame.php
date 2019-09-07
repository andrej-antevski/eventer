<?php
        require 'config/config.php'; 
        include 'includes/classes/User.php';
        include 'includes/classes/Post.php';

        if(isset($_SESSION['username'])){
            $userLoggedIn=$_SESSION['username'];
            $user_details_query=mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
            $user=mysqli_fetch_array($user_details_query);
        }
        else {
            header("Location: register.php");
        }
    ?>

<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <style>
        *{
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        }
        a{
            color:gray;
            text-decoration:none;
        }
        a:visited{
            color:gray;
        }
        a:hover{
            color: #b8bcd1;
            text-decoration: underline;
        }
        .theUser{
            position:relative;
            top:5px;
        }
        .post_info{
            display:inline;
        }
        .all_info{
            padding:70px 30px 0 10px;
        }
        .all_info img{
            border-radius:17px;
        }
        @media only screen and (max-width: 500px) {
            #comment_form textarea{
                width: 95%;
                margin:3px;
            }
            #comment_form input[type="submit"]{
                top:5px;
                width: 95%;
                position: relative; 
                margin:3px;
            }
            .form_align{
                text-align:center;
            }
        }
        </style>
    </head>
    <body>

        <script>
            //on click show and hide the comments section
            function toggle(){
                var element = document.getElementById("comment_section");
                if(element.style.display == "block") element.style.display = "none";
                else element.style.display = "block";
            }

            if ( window.location === window.parent.location ) {
                    window.location.href = "index.php";
            }
        </script>

        <?php
            //get id of post
            if(isset($_GET['post_id'])){
                $post_id=$_GET['post_id'];
            }
            //delete comment
            if(isset($_GET['hello'])){
                $com_q = $_GET['delcomment'];
                $q = mysqli_query($con,"UPDATE comments SET removed='yes' WHERE id='$com_q'");
                
                $check_last = mysqli_query($con,"SELECT * FROM comments WHERE posted_by='$userLoggedIn' AND removed='no' AND post_id=$post_id");
                $num_rows = mysqli_num_rows($check_last);
                $array_last = mysqli_fetch_array($check_last);
                if($num_rows == 0) {
                    $delete_ntf = mysqli_query($con,"DELETE FROM notifications WHERE (message='commented on your post' OR message='commented on a post that you have commented on') AND user_from='$userLoggedIn' AND link='$post_id'");
                    $delete_mine = mysqli_query($con,"DELETE FROM notifications WHERE (message='commented on your post' OR message='commented on a post that you have commented on') AND user_to='$userLoggedIn' AND link='$post_id'");
                }
                else{
                    $fix_notifications_q = mysqli_query($con,"SELECT MAX(date_added) FROM comments WHERE posted_by='$userLoggedIn' AND removed='no' AND post_id=$post_id");
                    $f_n_a = mysqli_fetch_array($fix_notifications_q);
                    $date_time_fix = $f_n_a[0];
                    $delete_ntf = mysqli_query($con,"UPDATE notifications SET datetime='$date_time_fix' WHERE (message='commented on your post' OR message='commented on a post that you have commented on') AND user_from='$userLoggedIn' AND link='$post_id'");    
                }
            }
            $user_query = mysqli_query($con,"SELECT added_by, user_to FROM posts WHERE id='$post_id'");
            $row = mysqli_fetch_array($user_query);
            $posted_to = $row['added_by'];
            if(isset($_POST['postComment' . $post_id])){
                $post_body = $_POST['post_body'];
                $post_body = mysqli_escape_string($con,$post_body);
                $date_time_now = date("Y-m-d H:i:s");
                $post_body = str_replace("&","&amp;",$post_body);
                $post_body = str_replace("<","&lt;",$post_body);
                $post_body = str_replace(">","&gt;",$post_body);
                $post_body = str_replace('"',"&quot;",$post_body);
                $post_body = str_replace("'","&#39;",$post_body);   
                $insert_post = mysqli_query($con,"INSERT INTO comments VALUES('', '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')");
                if($userLoggedIn != $posted_to){
                    $check_if_commented_q = mysqli_query($con, "SELECT * FROM notifications WHERE message='commented on your post' AND user_from='$userLoggedIn' AND user_to='$posted_to'");
                    $it_exists = mysqli_num_rows($check_if_commented_q);
                    if($it_exists == 0)
                        $insert_notification = mysqli_query($con,"INSERT INTO notifications VALUES('', '$posted_to','$userLoggedIn', 'commented on your post','$post_id','$date_time_now','no')");
                    else
                        $update_query = mysqli_query($con,"UPDATE notifications SET datetime='$date_time_now', opened='no' WHERE message='commented on your post' AND user_from='$userLoggedIn' AND user_to='$posted_to'");
                }
                    
                //send notifications to everyone who commented on the post
                $get_users_query = mysqli_query($con,"SELECT * FROM comments WHERE post_id='$post_id' GROUP BY posted_by");
                while ($get_users_array = mysqli_fetch_array($get_users_query)){
                    //ako tj sto e najaven ne e ist ss toga sto postiraja i tj sto postiraja komentar ne e ist ss toga sto objavija post
                    if($userLoggedIn != $get_users_array['posted_by'] && $get_users_array['posted_by'] != $get_users_array['posted_to']){
                        $send_to = $get_users_array['posted_by'];
                        $check_if_commented_q2 = mysqli_query($con, "SELECT * FROM notifications WHERE message='commented on a post that you have commented on' AND user_from='$userLoggedIn' AND user_to='$send_to'");
                        $it_exists2 = mysqli_num_rows($check_if_commented_q2);
                        if($it_exists2 == 0)
                            $insert_query = mysqli_query($con, "INSERT INTO notifications VALUES('', '$send_to','$userLoggedIn', 'commented on a post that you have commented on', '$post_id' ,'$date_time_now', 'no')");
                        else
                            $update_query = mysqli_query($con,"UPDATE notifications SET datetime='$date_time_now', opened='no' WHERE message='commented on a post that you have commented on' AND user_from='$userLoggedIn' AND user_to='$send_to'");

                    }
                } 
            }
    
        ?>
        <div class="form_align">
            <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
                <textarea style="overflow:hidden" name="post_body" placeholder="Add a comment" oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"'></textarea>
                <input type="submit" id="myBtn" name="postComment<?php echo $post_id; ?>" value="Post">
                <script>
                    var input = document.getElementById("comment_form");
                    input.addEventListener("keyup", function(event) {
                        if (event.keyCode === 13 && event.ketCode != 16) {
                            event.preventDefault();
                            document.getElementById("myBtn").click();
                        }
                    });
                </script>
            </form>
        </div>
        <!--load comments-->
        <?php
            $get_comments = mysqli_query($con,"SELECT * FROM comments WHERE post_id='$post_id' AND removed='no' ORDER BY id DESC");
            $count = mysqli_num_rows($get_comments);
            if($count != 0 ){
                while($comment=mysqli_fetch_array($get_comments)){
                    $comment_body = $comment['post_body'];
                    $posted_to = $comment['posted_to'];
                    $posted_by = $comment['posted_by'];
                    $date_added = $comment['date_added'];
                    $removed = $comment['removed'];
                    $id = $comment['id'];
                    
                    $date_time_now= date("Y-m-d H:i:s");
                    $start_date = new DateTime($date_added);//time of post
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
                    $user_obj = new User($con,$posted_by);

                    ?>
                    <div class="comment_section" id="comments_refresh">
                        <a href="<?php echo $posted_by ?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic(); ?>" alt="Profile Picture" title="<?php echo $posted_by; ?>" style="float:left;" height="30"></a>
                        <a class="theUser" href="<?php echo $posted_by ?>" target="_parent"><b><?php echo $user_obj->GetFirstAndLastName(); ?></b></a>
                        &nbsp;&nbsp;&nbsp;&nbsp; <span class="theUser"><?php echo $time_message . "<br><br>" . $comment_body; ?></span>
                        
                        
                        <?php

                        if($userLoggedIn==$posted_by) echo "
                        <form method='GET' action='comment_frame.php'>
                            <input type='text' value='$id' name='delcomment' hidden>
                            <input type='text' value='$post_id' name='post_id' hidden>
                            <input type='submit' name='hello' class='delete_button btn-danger' title='Delete this comment' value='X' style='position:relative;bottom:22px;'>
                        </form>";
                        
                        ?>
                        <hr>
                    </div>
                    <?php
                }
                
            }     else echo "<center><br>There are currently no comments for this post!</center>";       
        ?>

    </body>
</html>