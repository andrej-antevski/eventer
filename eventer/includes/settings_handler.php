<script>

function show_name(){
    $("#name_change").toggle(300);
    $("#email_change").hide(300);
    $("#password_change").hide(300);
    $("#close_account").hide(300);
}
function show_email(){
    $("#name_change").hide(300);
    $("#email_change").toggle(300);
    $("#password_change").hide(300);
    $("#close_account").hide(300);
}
function show_pass(){
    $("#name_change").hide(300);
    $("#email_change").hide(300);
    $("#password_change").toggle(300);
    $("#close_account").hide(300);
}
function show_close(){
    $("#name_change").hide(300);
    $("#email_change").hide(300);
    $("#password_change").hide(300);
    $("#close_account").toggle(300);
}

$(document).ready(function(){
    $("#name_change").hide();
    $("#email_change").hide();
    $("#password_change").hide();
    $("#close_account").hide();
});

</script>

<?php
$name_msg = "";
$email_msg = "";
$pass_msg = "";
//error handling

if(isset($_GET['error'])){
    /****** EMAIL ERROR ******/
    //wrong password

    if($_GET['error']=="emailpw"){
        echo "<script>
        $(document).ready(function(){
            $('#email_change').show();
        });
        </script>";  
        $email_msg = "Password is incorrect";
    }
    //wrong format
    else if($_GET['error']=="emailformat"){
        echo "<script>
        $(document).ready(function(){
            $('#email_change').show();
        });
        </script>";  
        $email_msg = "Please type valid email";
    }
    //already exists
    else if($_GET['error']=="emailsame"){
        echo "<script>
        $(document).ready(function(){
            $('#email_change').show();
        });
        </script>";  
        $email_msg = "Email is in use";
    }
    /****** PASSWORD ******/
    else if($_GET['error']=="pwwrong"){
        echo "<script>
        $(document).ready(function(){
            $('#password_change').show();
        });
        </script>";  
        $pass_msg = "Old password is incorrect";
    }
    else if($_GET['error']=="pwmatch"){
        echo "<script>
        $(document).ready(function(){
            $('#password_change').show();
        });
        </script>";  
        $pass_msg = "Passwords do not match";
    }
}

//success handling
if(isset($_GET['success'])){
    //name change
    if($_GET['success']=="name"){
        echo "<script>
        $(document).ready(function(){
            $('#name_change').show();
        });
        </script>";  
        $name_msg = "Your name has been changed";
    }
    //email change
    if($_GET['success']=="email"){
        echo "<script>
        $(document).ready(function(){
            $('#email_change').show();
        });
        </script>";  
        $email_msg = "Your email has been changed";
    }
    //pw
    if($_GET['success']=="pw"){
        echo "<script>
        $(document).ready(function(){
            $('#password_change').show();
        });
        </script>";  
        $pass_msg = "Password successfuly changed";
    }
}


//forms handling
$script_tag = "";
//name
if(isset($_POST['submit_name'])){
    $first_name = strip_tags($_POST['first_name_change']);
    $last_name = strip_tags($_POST['last_name_change']);
    $update_query = mysqli_query ($con,"UPDATE users SET first_name='$first_name', last_name='$last_name' WHERE username='$userLoggedIn'");
    header("Location: settings.php?success=name");
}
//email
else if(isset($_POST['submit_email'])){
    $pw = md5($_POST['password_email']);

    $pw_check = mysqli_query($con,"SELECT * FROM users WHERE password='$pw' AND username='$userLoggedIn'");
    if(mysqli_num_rows($pw_check)==0){
        header("Location: settings.php?error=emailpw");
    }
    else{
        $email = $_POST['email'];
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            // Checks if email is in valid form
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            // Check if email exists
            $e_check = mysqli_query($con,"SELECT email from USERS WHERE email='$email'");
            if(mysqli_num_rows($e_check)){
                header("Location: settings.php?error=emailsame");
            }
            else{
                $update_query = mysqli_query($con,"UPDATE users SET email='$email' WHERE username='$userLoggedIn'");
                header("Location: settings.php?success=email");
            }
        }
        else
            header("Location: settings.php?error=emailformat");
    }
}
//password
else if(isset($_POST['submit_pass'])){
    $old_pw = md5($_POST['old_password']);
    $new_pw1 = md5($_POST['new_password_1']);
    $new_pw2 = md5($_POST['new_password_2']);
    $check_old = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn' AND password='$old_pw'");
    if(mysqli_num_rows($check_old) == 0){
        header("Location: settings.php?error=pwwrong");
    }
    else {
        if($new_pw1 != $new_pw2){
            header("Location: settings.php?error=pwmatch");
        }
        else {
            $update_query = mysqli_query($con,"UPDATE users SET password='$new_pw1' WHERE username='$userLoggedIn'");
            header("Location: settings.php?success=pw");
        }
    }
}
//close acc
if(isset($_POST['acc_close'])){
    $update_posts = mysqli_query($con,"UPDATE posts SET user_closed='yes' WHERE added_by='$userLoggedIn'");
    $update_acc = mysqli_query($con,"UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
    $update_comm = mysqli_query($con,"UPDATE comments SET removed='clo' WHERE posted_by = '$userLoggedIn'");
    $update_not = mysqli_query($con,"UPDATE notifications SET closed='yes' WHERE user_from='$userLoggedIn'");
    
    
    session_destroy();
    header("Location: register.php");
}
?>
