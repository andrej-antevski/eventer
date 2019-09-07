<!doctype HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <style>
            body{
                background:#fff;
            }
            #Likers{
                color:black;
                font-size:20px;
            }
            a{
                text-decoration:none;
            }
            img{
                border-radius:15px;
            }
            p{
                display:inline;
                position:relative;
                bottom:7px;
            }
        </style>
    </head>
    <body>
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
        //get id of post
        if(isset($_GET['post_id'])){
            $post_id=$_GET['post_id'];
        }

        
    ?>
    <div id="Likers">
        <?php
            $get_people = mysqli_query($con, "SELECT username FROM likes WHERE post_id='$post_id'");
            while($get_data=mysqli_fetch_array($get_people)){
                $liker_obj = new User($con,$get_data['username']);
                $userwholiked = $get_data['username'];
                    $profilePic = $liker_obj->getProfilePic();
                    echo "<a href='$userwholiked' target='_parent'>  <img src='$profilePic' width='30'> ";  
                    echo "<p class='the_name'>" . $liker_obj->GetFirstAndLastName() . "</p></a><br>";            
            }
        ?>
    </div>
    <script>
        if ( window.location == window.parent.location ) {
            window.location.href = "index.php";
        }
    </script>
    </body>
</html>