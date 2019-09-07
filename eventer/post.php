<?php
        require 'config/config.php'; 
        include 'includes/classes/User.php';
        include 'includes/classes/Post.php';
        include 'includes/classes/Message.php';

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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="https://kit.fontawesome.com/35be290bd5.js"></script>
        <script src="assets/js/eventer.js"></script>

        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <style>
        *{
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        }
        .all_info a{
            color:gray;
            text-decoration:none;
        }
        .all_info a:visited{
            color:gray;
        }
        .all_info a:hover{
            color: #b8bcd1;
            text-decoration: underline;
        }
        .theUser{
            position:relative;
            top:2px;
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
        .post_body{
            width:82%;
            background:white;
            padding:10px;
            border-radius:10px;
        }
        .who_liked{
                display:inline; 
                cursor:pointer;
                color:#00cec9;
        }
        .who_liked:hover{
            cursor:pointer;
            text-decoration:underline;
        }
        .post_body img{
            border-radius: 0;
            margin:10px;
        }
        @media only screen and (max-width: 500px) {
            #comment_form textarea{
                width: 95%;
                margin:3px;
                margin-top:20px;
            }
            #comment_form input[type="submit"]{
                top:1px;
                width: 95%;
                position: relative; 
                margin:3px;
            }
            .form_align{
                text-align:center;
            }
            .post_body{
                width:103%;
            }

        }
        </style>
    </head>
    <body>

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
                        $update_query = mysqli_query($con,"UPDATE notifications SET datetime='$date_time_now' AND opened='no' WHERE message='commented on your post' AND user_from='$userLoggedIn' AND user_to='$posted_to'");
                }
                header('Location: post.php?post_id=' . $post_id);

                //send notifications for everyone who commented on the post
                $get_users_query = mysqli_query($con,"SELECT * FROM comments WHERE post_id='$post_id' GROUP BY posted_by");
                while ($get_users_array = mysqli_fetch_array($get_users_query)){
                    if($userLoggedIn != $get_users_array['posted_by']){
                        $send_to = $get_users_array['posted_by'];
                        $insert_query = mysqli_query($con, "INSERT INTO notifications VALUES('', '$send_to','$userLoggedIn', 'commented on a post that you have commented on', '$post_id' ,'$date_time_now', 'no')");
                    }
                } 


            }
    
        ?>



        <div id="showWindow">
            <div class="top_bar">
                <div class="logo">
                    <a href="index.php">
                        <img src="assets/images/logos/logo_nav.png" alt="logo" width=40>
                        Eventer
                    </a>
                </div>


            <div class="search">
                <form name="search_form" action="search.php" method="GET">
                    <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input">
                    <div class="button_holder"><img src="assets/images/icons/search_icon.png" alt="srch" width="52"></div>

                </form>
                <div class="search_results"></div>
                <div class="search_results_footer_empty"></div>
            </div>


            
                <?php 
                $messages = new Message($con,$userLoggedIn);
                $num_messages = $messages->getUnreadNumber();
                $num_notifications = $messages->getNotificationNumber();
                $num_friends = $messages->getFriendNumber();
            ?>
            <script>
                setInterval(function() {
                    $( "#friends_notifications" ).load(window.location.href + " #friends_notifications" );
                }, 10000);
                setInterval(function() {
                        $( "#message_notifications" ).load(window.location.href + " #message_notifications" );
                    }, 3000);
                setInterval(function() {
                        $( "#notifications" ).load(window.location.href + " #notifications" );
                    }, 3000);
                setTimeout(function() {
                    $('#notification_dropdown').load('notifications.php?user_from=<?php echo $userLoggedIn;?>');
                }, 0);
                setInterval(function() {
                    $('#notification_dropdown').load('notifications.php?user_from=<?php echo $userLoggedIn;?>');
                }, 2000);
                
                function notifications() {
                    var x = document.getElementById("notification_dropdown");
                    if (x.style.display === "none") {
                        x.style.display = "block";
                    }
                    else if(x.style.display === "block"){
                        x.style.display = "none";
                    }
                    else{
                        x.style.display = "block";
                    }

                    //set the notifications to seen
                    var xmlhttp = new XMLHttpRequest();
                    
                    xmlhttp.onreadystatechange = function(){
                            if(xmlhttp.readyState==4&&xmlhttp.status==200){
                                    
                                }
                        }
                    xmlhttp.open('GET','insert.php?user_from=<?php echo $userLoggedIn;?>&clear_notifications=yes',true);
                    xmlhttp.send();
                    
                }

                onload = function(){
                    var hideMe = document.getElementById('notification_dropdown');
                    document.onclick = function(e){
                    if(e.target.id !== 'show_dropdown'){
                        hideMe.style.display = 'none';
                    }
                };
            };

            </script>
                <nav>
                    <a href="<?php echo $userLoggedIn?>">
                        <img src="<?php echo $user['profile_pic'];?>" alt="Profile Picture" width=30>
                <?php
                        echo $user['first_name'];                
                    ?></a>
                    <a href="index.php"><i class="fa fa-home fa-lg"></i></a>
                    <a href="messages.php"><i class="fa fa-envelope fa-lg"></i>
                    <span id="message_notifications"><?php 
                    if($num_messages>0) echo $num_messages;
                    ?></span>
                    </a>
                    <a href="#" class='show_dropdown fa fa-bell-o fa-lg' id='show_dropdown' onclick='notifications()'>
                    <span id="notifications"><?php 
                    if($num_notifications>0) echo $num_notifications;
                    ?></span></a>
                    <div id='notification_dropdown'></div>
                    <a href="requests.php"><i class="fa fa-users fa-lg"></i>
                    <span id="friends_notifications"><?php 
                    if($num_friends>0) echo $num_friends;
                    ?></span></a>
                    <a href="settings.php"><i class="fa fa-cog fa-lg"></i></a>
                    <a href="includes/handlers/logout.php"><i class="fa fa-sign-out fa-lg"></i></a>

                </nav>
            </div>
            <div class="all_info">
                <?php 
                    $get_post = mysqli_query($con,"SELECT * FROM posts WHERE id='$post_id'");
                    $post_row = mysqli_fetch_array($get_post);
                    $posted_post=$post_row['added_by'];
                    $post_obj = new User($con,$posted_post);
                ?>

                <a href="<?php echo $posted_post; ?>">
                    <img src="<?php echo $post_obj->getProfilePic(); ?>" alt="Profile Picture" title="<?php echo $posted_post; ?>" width="75"></a>
                    <p class="post_info">
                        <a href="<?php echo $posted_post; ?>"><?php echo $post_obj->GetFirstAndLastName(); ?></a>
                    &nbsp;&nbsp;&nbsp;Added on:
                    <?php
                        echo $post_row['date_added'];
                    ?>
                    </p>
                

                <br><br>
                <div class="post_body">
                    <?php echo $post_row['body']; $imagelink = $post_row['image']; ?> <br>
                    <?php if($imagelink != 'none'){?>
                        <center><img src="<?php echo $imagelink ?>" ></center>
                    <?php } ?>
                    <br>
                    <iframe src='like.php?post_id=<?php echo $post_id;?>' style="
                        border: 0px;
                        height: 25px;
                        width: 120px;
                        position:relative;
                        top:10px;
                    "></iframe>
                    <p class="who_liked" title='Who liked this post' data-toggle='modal' data-target='#exampleModal'>?</p>

                    <div class='modal fade' id='exampleModal' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                    <div class='modal-dialog' role='document'>
                        <div class='modal-content'>
                        <div class='modal-header'>
                            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                            </button>
                        </div>
                        <div class='modal-body' style='height:500px;'>
                            <iframe src='likers.php?post_id=<?php echo $post_id; ?>' id='like_iframe' frameborder='0'></iframe>
                        </div>
                        <div class='modal-foote'>

                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        if ( window.location !== window.parent.location ) {
                var x = document.getElementById("showWindow");
                x.style.display = "none";
        }
        </script>
        <div class="form_align">
            <form action="post.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
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

                    <div class="comment_section">
                        <a href="<?php echo $posted_by ?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic(); ?>" alt="Profile Picture" title="<?php echo $posted_by; ?>" style="float:left;" height="30"></a>
                        <a class="theUser" href="<?php echo $posted_by ?>" target="_parent"><b><?php echo $user_obj->GetFirstAndLastName(); ?></b></a>
                        &nbsp;&nbsp;&nbsp;&nbsp; <span class="theUser"><?php echo $time_message . "<br><br>" . $comment_body; ?></span>
                        <hr>
                    </div>
                    <?php

                    if($userLoggedIn==$posted_by) echo "
                    <form method='GET' action='post.php'>
                    <input type='text' value='$id' name='delcomment' hidden>
                    <input type='text' value='$post_id' name='post_id' hidden>
                    <input type='submit' name='hello' class='delete_button btn-danger' title='Delete this comment' value='X' style='position:relative;bottom:70px;'>
                    </form>";

                }
                
            }     else echo "<center><br>There are currently no comments for this post!</center>";       
        ?>

            
    </body>
</html>