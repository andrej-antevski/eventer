<?php
    require 'config/config.php'; 
    include 'includes/classes/User.php';
    include 'includes/classes/Post.php';
    include 'includes/classes/Message.php';
    include 'includes/classes/Trending.php';

    if(isset($_SESSION['username'])){
        $userLoggedIn=$_SESSION['username'];
        $user_details_query=mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
        $user=mysqli_fetch_array($user_details_query);
    }
    else {
        header("Location: register.php");
    }
    $var;
    if(isset($var)) echo "basd";
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Eventer</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/bootbox.min.js"></script>
        <script src="assets/js/jquery.jcrop.js"></script>
        <script src="assets/js/jcrop_bits.js"></script>
        <script src="assets/js/eventer.js"></script>

        <script src="https://kit.fontawesome.com/35be290bd5.js"></script>
        
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />

    </head>
    <body>
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
                        $( "#message_notifications" ).load(window.location.href + " #message_notifications" );
                    }, 3000);
                setInterval(function() {
                        $( "#notifications" ).load(window.location.href + " #notifications" );
                    }, 3000);
                setInterval(function() {
                    $( "#friends_notifications" ).load(window.location.href + " #friends_notifications" );
                }, 10000);
                setTimeout(function() {
                    $('#notification_dropdown').load('notifications.php?user_from=<?php echo $userLoggedIn;?>');
                }, 0);

                setInterval(function() {
                    $('#notification_dropdown').load('notifications.php?user_from=<?php echo $userLoggedIn;?>');
                }, 2000);
                //close and open the notification bar
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
                //when clicked outside notification close the dropdown
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
                <!-- First name and Profile Picture -->
                <a href="<?php echo $userLoggedIn?>">
                    <img src="<?php echo $user['profile_pic'];?>" alt="Profile Picture" width=30>
               <?php
                    echo $user['first_name'];                
                ?></a>
                <a href="index.php"><i class="fa fa-home fa-lg"></i></a>
                <!-- Messages -->
                <a href="messages.php"><i class="fa fa-envelope fa-lg"></i>
                <span id="message_notifications"><?php 
                    if($num_messages>0) echo $num_messages;
                ?></span></a>
                <!-- Notifications -->
                <a href="#" class='show_dropdown fa fa-bell-o fa-lg' id='show_dropdown' onclick="notifications()">
                <span id="notifications"><?php 
                    if($num_notifications>0) echo $num_notifications;
                ?></span></a>
                <div id='notification_dropdown'></div>
                <!-- Friend Requests -->        
                <a href="requests.php"><i class="fa fa-users fa-lg"></i>
                <span id="friends_notifications"><?php 
                    if($num_friends>0) echo $num_friends;
                ?></span></a>
                <!-- Options -->
                <a href="settings.php"><i class="fa fa-cog fa-lg"></i></a>
                <!-- Logout -->
                <a href="includes/handlers/logout.php"><i class="fa fa-sign-out fa-lg"></i></a>

            </nav>
        </div>
        <div class="wrapper">