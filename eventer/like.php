<!doctype HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <style>
        body{
            background:white;
        }
        .comment_like{
            color:#00cec9;
        }
        .comment_like:hover{
            text-decoration:underline;
            cursor:pointer;
        }
        a{
            text-decoration:none;
            color:black;
        }
        a:hover{
            text-decoration:underline;
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

        $get_likes = mysqli_query($con,"SELECT likes,added_by FROM posts WHERE id='$post_id'");
        $row = mysqli_fetch_array($get_likes);
        $total_likes = $row['likes'];
        $user_liked = $row['added_by'];

        $user_details_query = mysqli_query($con,"SELECT * FROM users WHERE username='$user_liked'");
        $row = mysqli_fetch_array($user_details_query);
        $total_user_likes = $row['num_likes'];

        //like button
        if(isset($_POST['like_button'])){
            
            $same_person = mysqli_query($con,"SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
            $can_like = mysqli_num_rows($same_person);
            if($can_like==0){
                $total_likes++;
                $query = mysqli_query($con,"UPDATE posts SET likes = '$total_likes' WHERE id='$post_id'");
                $total_user_likes++;
                $user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
                $insert_user = mysqli_query($con, "INSERT INTO likes VALUES ('', '$userLoggedIn', '$post_id')");
            }
            //insert notification
            $date = date("Y-m-d H:i:s");
            if($userLoggedIn != $user_liked){
                $notification_to_query = mysqli_query($con, "SELECT added_by FROM posts WHERE id='$post_id'");
                $notification_to_row = mysqli_fetch_array($notification_to_query);
                $user_to_notification = $notification_to_row['added_by'];
                $notification_query = mysqli_query($con,"INSERT INTO notifications VALUES ('', '$user_to_notification', '$userLoggedIn', 'test', '$post_id', '$date', 'no')");
            }
        }
        //unlike button 
        if(isset($_POST['unlike_button'])){
            $same_person = mysqli_query($con,"SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
            $can_dislike = mysqli_num_rows($same_person);
            if($can_dislike != 0 ){
                $total_likes--;
                $query = mysqli_query($con,"UPDATE posts SET likes = '$total_likes' WHERE id='$post_id'");
                $total_user_likes--;
                $user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
                $insert_user = mysqli_query($con, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
            }
            //insert notification
            $notification_query_remove = mysqli_query($con,"DELETE FROM notifications WHERE user_from='$userLoggedIn' AND link='$post_id'");
        }
        //check for previous likes
        $check_query = mysqli_query($con,"SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
        $num_rows = mysqli_num_rows($check_query);
        if($num_rows>0){
            echo '<form action="like.php?post_id=' . $post_id . '"method = "POST">
                    <input type="submit" class="comment_like" name="unlike_button" value="Unlike">
                    <div class="like_value">
                    '.$total_likes.' Likes
                    </div>
                </form>
            ';
        }
        else{
            echo '<form action="like.php?post_id=' . $post_id . '"method = "POST">
                    <input type="submit" class="comment_like" name="like_button" value="Like">
                    <div class="like_value">
                    '.$total_likes.' Likes
                    </div>
                </form>
            ';
        }
    ?>
    <div id="Likers">
        <?php
            $get_people = mysqli_query($con, "SELECT username FROM likes WHERE post_id='$post_id'");
            while($get_data=mysqli_fetch_array($get_people)){
                $liker_obj = new User($con,$get_data['username']);
                    echo $liker_obj->GetFirstAndLastName();            
            }
        ?>
    </div>
    <script>
        if ( window.location !== window.parent.location ) {
                var x = document.getElementById("Likers");
                x.style.display = "none";
        }
    </script>
    </body>
</html>