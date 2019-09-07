<?php
    include 'includes/header.php';
?>
<div id="main_column" class="main_column column">
    <h4>Friend Requests</h4>
    <?php 
        $query = mysqli_query($con,"SELECT * FROM friend_request WHERE user_to = '$userLoggedIn'");
        if(mysqli_num_rows($query)==0){
            echo "You have no pending friend requests";
        }
        else{
            while($row=mysqli_fetch_array($query)){
                $user_from = $row['user_from'];
                $user_from_obj = new User($con, $user_from);

                echo $user_from_obj->GetFirstAndLastName() . " sent you a friend request.";

                $user_from_friend_array = $user_from_obj->getFriendArray();

                if(isset($_POST['accept_request' . $user_from ])) {
                    //sort the arrays
                    $get_user_array = mysqli_query($con,"SELECT friend_array FROM users WHERE username='$userLoggedIn'");
                    $user_row=mysqli_fetch_array($get_user_array);
                    $friends_user = $user_row['friend_array'];
                    $friends_user_explded = explode(",",$friends_user);
                    array_push($friends_user_explded,$user_from);
                    sort($friends_user_explded);
                    $friends_user = implode(",",$friends_user_explded);
                    $friends_user =  $friends_user . ",";
                    $friends_user = substr($friends_user, 1);
                    //
                    $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array='$friends_user' WHERE username='$userLoggedIn'");
                    //sort second array
                    $get_user_array = mysqli_query($con,"SELECT friend_array FROM users WHERE username='$user_from'");
                    $user_row=mysqli_fetch_array($get_user_array);
                    $friends_user = $user_row['friend_array'];
                    $friends_user_explded = explode(",",$friends_user);
                    array_push($friends_user_explded,$userLoggedIn);
                    sort($friends_user_explded);
                    $friends_user = implode(",",$friends_user_explded);
                    $friends_user =  $friends_user . ",";
                    $friends_user = substr($friends_user, 1);
                    $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array='$friends_user' WHERE username='$user_from'");
                    //
                    $delete_query = mysqli_query($con, "DELETE FROM friend_request WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                    echo "You are now friends!";
                    header("Location: requests.php");
                }
    
                if(isset($_POST['ignore_request' . $user_from ])) {
                    $delete_query = mysqli_query($con, "DELETE FROM friend_request WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                    echo "Request ignored!";
                    header("Location: requests.php");
                }

                ?>
                <form action="requests.php" method="POST">
                    <input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_button" value="Accept">
                    <input type="submit" name="ignore_request<?php echo $user_from; ?>" id="ignore_button" value="Ignore">
                </form>
                <?php

            }
        }
    ?>


</div>
</div><!--Closing div for wrapper from header-->
</body>
</html>