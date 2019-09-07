<?php
    include 'includes/header.php';
    include 'includes/settings_handler.php';

?>

<div class='main_column column settings_main'>
    <h4>Account Settings</h4>
    <?php
        echo "<img src='". $user['profile_pic'] ."' id='small_profile_pic'>";
    ?>
    <br>
    <a href="upload.php">Upload new Profile Picture</a>

    <h4><a href="#" onclick="show_name()">Change name</a></h4>
    <div id="name_change">
        <form action="settings.php" method="POST">
        <span class="align_settings_name"> First Name: </span><input type="text" name="first_name_change" value="<?php echo $user['first_name'] ?>" maxlength="25" required><br>
        <span class="align_settings_name"> Last Name:  </span><input type="text" name="last_name_change" value="<?php echo $user['last_name'] ?>" maxlength="25" required><br>
        <input type="submit" name="submit_name" value="Confirm">
        <?php echo $name_msg ?>
        </form>
    </div>
    <h4> <a href="#" onclick="show_email()">Change Email</a></h4>
    <div id="email_change">
    
        <form action="settings.php" method="POST">
            <input type="email" name="email" placeholder="New Email Address" maxlength="100"><br>
            <input type="password" name="password_email" placeholder="Confirm Password"> <br>
            <input type="submit" name="submit_email" value="Confirm">
            <?php echo $email_msg ?>
        </form>
    </div>
    <h4> <a href="#" onclick="show_pass()">Change password</a></h4>
    <div id="password_change">
        <form action="settings.php" method="POST">
           <input type="password" name="old_password" placeholder="Old Password"><br>
           <input type="password" name="new_password_1" placeholder="New Password"><br>
           <input type="password" name="new_password_2" placeholder="Repeat New Password"><br>
        <input type="submit" name="submit_pass" value="Confirm">
        <?php echo $pass_msg; ?>
        </form>
    </div>
    <h4> <a href="#" onclick="show_close()">Close Account</a></h4>
    <div id="close_account">
            <button data-toggle="modal" data-target="#close" class="close_btn">Close Account</button>
    </div>
</div>

<div class="modal fade" id="close" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Are you sure you want to close this account?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form action="settings.php" method="POST">
            <div class="modal-body">
                Closing your account means that you won't be visible to others, and all your posts and comments will be removed. <br>
                To re-open your account just login.
            </div>
            <div class="modal-footer">
                <input type="submit" class="danger" value="Yes" name="acc_close">
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            </div>
        </form>
        </div>
    </div>
</div>

</body>
</html>