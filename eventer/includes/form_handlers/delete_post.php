<?php
    require '../../config/config.php';
    if(isset($_GET['post_id'])) $post_id = $_GET['post_id'];
    if(isset($_POST['result'])) {
        if($_POST['result'] == 'true'){
            $query = mysqli_query($con,"UPDATE posts SET deleted='yes' WHERE id='$post_id'");
            $query_user = mysqli_query($con,"SELECT * FROM posts WHERE id='$post_id'");
            $user_row = mysqli_fetch_array($query_user);
            $username = $user_row['added_by'];
            $post_likes = $user_row['likes'];

            $query = mysqli_query($con,"SELECT * FROM users WHERE username = '$username'");
            $user_array = mysqli_fetch_array($query);
            $num_posts = $user_array['num_posts'];
            $num_likes = $user_array['num_likes'];
            $num_likes = $num_likes - $post_likes;
            $num_posts--;
            $query = mysqli_query($con, "UPDATE users SET num_posts='$num_posts' WHERE username = '$username'");
            $query = mysqli_query($con, "UPDATE users SET num_likes='$num_likes' WHERE username = '$username'");

            $query = mysqli_query($con,"DELETE FROM notifications WHERE link='$post_id'");
            $query = mysqli_query($con,"UPDATE  comments SET removed='yes' WHERE post_id = '$post_id'");


        }
    } 
?>