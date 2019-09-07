<?php
    require 'config/config.php';
    require 'includes/form_handlers/register_handler.php';
    require 'includes/form_handlers/login_handler.php';
    
    
    if(isset($_SESSION['username'])){
        header("Location: index.php");
    }

?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Register for Eventer</title>
        <link rel="stylesheet" type="text/css" href="assets/css/register_style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="assets/js/register.js"></script>
    </head>
    <body>
        <?php
            if(isset($_POST['register_button'])){
                echo '
                    <script>
                        $(document).ready(function(){
                            $("#first").hide();
                            $("#second").show();
                        });
                    </script>
                ';
            }
        ?>


        <div id="wrapper">
            <div id="login_box">
                <div id="login_header">
                    <h1>Eventer</h1>
                    Login or sign up below!
                </div>
                <div id="first">
                    <form action="register.php" method="POST">
                        <input type="text" name="log_email" placeholder="Email" value="<?php if(isset($_SESSION['log_email'])) echo $_SESSION['log_email'];?>" required>
                        <br>
                        <input type="password" name="log_password" placeholder="Password" required>
                        <br>
                        <div class="err_msg">
                            <?php if(in_array("Wrong email or password",$error_array)) echo "Wrong email or password<br>"; ?>
                        </div>
                        <input type="submit" name="login_button" value="Login">
                        <br>
                        <a href="#" id="signup" class="signup">If you don't have an account register here!</a>
                    </form>
                </div>
                <div id="second">
                    <form action="register.php" method="POST">
                        <input type="text" name="reg_fname" placeholder="First Name" maxlength="25" value="<?php if(isset($_SESSION['reg_fname'])) echo $_SESSION['reg_fname'];?>" required>
                        <br>
                        <input type="text" name="reg_lname" placeholder="Last Name" maxlength="25" value="<?php if(isset($_SESSION['reg_lname'])) echo $_SESSION['reg_lname'];?>" required>
                        <br>
                        <input type="email" name="reg_email" placeholder="Email" maxlength="100" value="<?php if(isset($_SESSION['reg_email'])) echo $_SESSION['reg_email'];?>" required>
                        <br>
                        <div class="err_msg">
                            <?php if(in_array("Email already exists<br>",$error_array)) echo "Email already exists";
                                else if(in_array("Email invalid Format<br>",$error_array)) echo "Email invalid Format"; ?>
                        </div>
                        <input type="email" name="reg_email2" placeholder="Confirm Your Email" value="<?php if(isset($_SESSION['reg_email2'])) echo $_SESSION['reg_email2'];?>" required>
                        <br>
                        <div class="err_msg">
                            <?php if(in_array("Email do not match<br>",$error_array)) echo "Emails do not match"; ?>
                        </div>
                        <input type="password" name="reg_password" placeholder="Password" maxlength="30" minlength="8" required>
                        <br>
                        <input type="password" name="reg_password2" placeholder="Confirm Your Password" required>
                        <br>
                        <div class="err_msg">
                            <?php if(in_array("Password do not match<br>",$error_array)) echo "Passwords do not match"; ?>
                        </div>
                         <br>
                        <input type="submit" name="register_button" value="Register">
                        <br>
                        <a href="#" id="signin" class="signin">Login here</a>
                        <?php if(in_array("Successful registration!<br>",$error_array)) echo "<br>Successful registration!<br>";?>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>