<?php
    //Variables
    $fname=""; //First Name
    $lname=""; //Last Name
    $em=""; //Email
    $em2=""; //Email Confirm
    $password=""; //Password
    $password2=""; //Password Confirm
    $date=""; //Sign up Date
    $error_array=array(); //Store error messages

    if(isset($_POST['register_button'])){
        //When submit button is pressed do this
        $fname = strip_tags($_POST['reg_fname']); // First Name
        $_SESSION['reg_fname']=$fname;
        $lname = strip_tags($_POST['reg_lname']); // Last Name
        $_SESSION['reg_lname']=$lname;
        $em = strip_tags($_POST['reg_email']); // Email
        $_SESSION['reg_email']=$em;
        $em2 = strip_tags($_POST['reg_email2']); // Email Confirm
        $_SESSION['reg_email2']=$em2;
        $password = strip_tags($_POST['reg_password']); // Password
        $password2 = strip_tags($_POST['reg_password2']); // Password Confirm
        $date = date("Y-m-d"); // Current Date

        //Check if Email info is correct
        if($em == $em2){
            //Check if both mails are the same
            
            if(filter_var($em, FILTER_VALIDATE_EMAIL)){
                // Checks if email is in valid form
                $em = filter_var($em, FILTER_VALIDATE_EMAIL);
                // Check if email exists
                $e_check = mysqli_query($con,"SELECT email from USERS WHERE email='$em'");
                if(mysqli_num_rows($e_check)){
                    array_push($error_array, "Email already exists<br>");
                }
            }
            else
                array_push($error_array, "Email invalid Format<br>");
        }
        else
            array_push($error_array, "Email do not match<br>");
        if($password != $password2)
            array_push($error_array, "Password do not match<br>");
        //If there are no errors do this
        if(empty($error_array)){
            $password = md5($password); //Encrypt the password
            //generate an unique username for every registrations
            $username = strtolower($fname . "_" . $lname);
            $check_username = mysqli_query($con, "SELECT username FROM users WHERE username = '$username' ");
            
            $i=0;
            while(mysqli_num_rows($check_username)){
                $i++;
                $temp_user = $username.$i;
                $check_username = mysqli_query($con, "SELECT username FROM users WHERE username = '$temp_user' ");
                if(mysqli_num_rows($check_username) == 0) {
                    $username=$temp_user;
                    break;
                }
            }

            $profile_pic = "assets/images/profile_pics/defaults/head_turqoise.png";

            $query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");
            array_push($error_array, "Successful registration!<br>");
            $_SESSION['reg_fname']="";
            $_SESSION['reg_lname']="";
            $_SESSION['reg_email']="";
            $_SESSION['reg_email2']="";
        }
    }
?>