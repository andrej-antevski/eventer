<?php
    if(isset($_POST['login_button'])){
        $email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL);
        
        $_SESSION['log_email'] = $email;
        $password = md5($_POST['log_password']);

        $check_database = mysqli_query($con,"SELECT * FROM users WHERE email = '$email' AND password = '$password'");
        //check if inputed data is in out database
        if(mysqli_num_rows($check_database)){
            $row=mysqli_fetch_array($check_database);
            $username = $row['username']; 
            //reopening closed accounts
            $user_closed = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND user_closed='yes'");
            if(mysqli_num_rows($user_closed)){
                $reopen_account = mysqli_query($con, "UPDATE users SET user_closed='no' WHERE email='$email'");
                $user_array = mysqli_fetch_array($user_closed);
                $userLoggedIn = $user_array['username'];
                $update_posts = mysqli_query($con,"UPDATE posts SET user_closed='no' WHERE added_by='$userLoggedIn'");
                $update_acc = mysqli_query($con,"UPDATE users SET user_closed='no' WHERE username='$userLoggedIn'");
                $update_comm = mysqli_query($con,"UPDATE comments SET removed='no' WHERE posted_by = '$userLoggedIn'");
                $update_not = mysqli_query($con,"UPDATE notifications SET closed='no' WHERE user_from='$userLoggedIn'");
            }

            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        }
        else array_push($error_array, "Wrong email or password");
    }
?>