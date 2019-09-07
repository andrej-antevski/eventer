<?php
    include 'includes/header.php';

    if(isset($_POST['post'])){

        $uploadOk = true;
        $imageName = $_FILES['fileToUpload']['name'];
        $errorMessage='';

        if($imageName != ""){
            $targetDir = "assets/images/posts/";
            $imageName = $targetDir . uniqid() . basename($imageName);
            $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

            //10mb
            if ($_FILES['fileToUpload']['size'] > 10000000 ){
                $errorMessage = "File Size To Large";
                $uploadOk = false;
            }
            if (strtolower($imageFileType) != 'jpeg' && strtolower($imageFileType) != 'jpg' && strtolower($imageFileType) != 'png'){
                $errorMessage = "File Type is not jpeg, png or jpg";
                $uploadOk = false;
            }
            if ($uploadOk){
                if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'],$imageName)){
                    //image uploaded
                }
                else {
                    //not uploaded
                    $uploadOk = false;
                }
            }
        }
        else $imageName = "none";

        if($uploadOk){
            $post = new Post($con, $userLoggedIn);
            $post->submitPost($_POST['post_text'], 'none',$imageName);
            header("Location: $userLoggedIn");
        }
        else {
            echo "<div style='text-align:center' class='alert-danger'>$errorMessage</div>";
        }


    }
    if(isset($_GET['profile_username'])){
        $username=$_GET['profile_username'];
        $user_details_query = mysqli_query($con,"SELECT * FROM users WHERE username='$username'");
        $user_array = mysqli_fetch_array($user_details_query);
        $profile_user_obj = new User($con, $username);

        //can be made into a method in User.php
        $num_friends = substr_count ($user_array['friend_array'],",")-1;
    }
    if(isset($_POST['remove_friend'])){
        $user = new User($con,$userLoggedIn);
        $user->removeFriend($username);
    }
    if(isset($_POST['add_friend'])){
        $user = new User($con,$userLoggedIn);
        $user->sendRequest($username);
    }
    $visible=0;
    if($userLoggedIn == $username){
        $visible=1;
    }
    if(isset($_POST['respond_request'])){
        header("Location: profile.php");
    }
?>
            <style>
                .wrapper{
                    margin-left:0;
                    padding-left:0;
                }
                @media only screen and (max-width: 1390px) {
                    .wrapper{
                        width:100%;
                        min-width:600px;
                    }
                    .main_column{
                        float:right;
                        width: 70%;
                        z-index: -1;
                        min-height: 150px;
                    }
                 }
            </style>
            <div class="profile_left">
                <img src="<?php echo $user_array['profile_pic']; ?>" alt="Profile Image">
                <p class='messageProfileText'><?php echo $profile_user_obj->GetFirstAndLastName(); ?></p>
                 <?php if($userLoggedIn != $username){?>
                <button class='messageProfile' onClick='window.location.href = "messages.php?u=<?php echo $username; ?>";'>Send Message</button>
                 <?php }?>
                <div class="profile_info">
                    <p><?php echo "Posts: " . $user_array['num_posts'] ?></p>
                    <p><?php echo "Likes: " . $user_array['num_likes'] ?></p>
                    <p class="all_friends" data-toggle="modal" data-target="#exampleModal1"><?php echo "Friends: " . $num_friends ?></p>
                </div>
                <form action="<?php echo $username; ?>" method="POST">
                    <?php
                        if($profile_user_obj->isClosed()) header("Location: user_closed.php");

                        $logged_in_user_obj = new User($con,$userLoggedIn);

                        if ($userLoggedIn != $username){
                            if ($logged_in_user_obj->isFriend($username)){
                                echo '<input type="submit" name="remove_friend" class="danger" value="Unfriend"><br>';
                            }
                            else if($logged_in_user_obj->didReceiveRequest($username)){
                                echo '<input type="submit" name="respond_request" class="warning" value="Respond to Request"><br>';
                            }
                            else if($logged_in_user_obj->didSendRequest($username)){
                                //TODO on click unsend request
                                echo '<input type="submit" name="" class="default" value="Request Sent"><br>';
                            }
                            else{
                                echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';
                            }
                        }
                    ?>
                    
                </form>

                <?php
                    if($visible==0){
                        echo '<div class="profile_info_bottom" data-toggle="modal" data-target="#exampleModal">';
                        echo $logged_in_user_obj->getMutualFriends($username) . " Mutual friends";
                        echo "</div>";
                    }
                ?>
            </div>
            <div class="main_column column">
                <!-- Modal All-->
                <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">All Friends</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php 
                            $logged_in_user_obj->showAllFriends($username)
                        ?>
                    </div>
                    </div>
                </div>
                </div>
                <!-- Modal End -->

                <!-- Modal Mutual-->
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Mutual Friends</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php 
                            $logged_in_user_obj->showMutualFriends($username)
                        ?>
                    </div>
                    </div>
                </div>
                </div>
                <!-- Modal End -->
                <?php if($visible==1){ ?>
                <form class="post_form" action="<?php echo $username; ?>" method="post" enctype="multipart/form-data">
                    <input type="file" name="fileToUpload" id="fileToUpload">
                    
                    <img id="blah" src="assets/images/icons/blank_image.png" onerror="this.src='assets/images/icons/image_not_available.png';" style="display:inline-block; max-width:130px; margin-bottom:5px;">
                <script>
                    function readURL(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        
                        reader.onload = function (e) {
                            $('#blah').attr('src', e.target.result);
                        }
                        
                        reader.readAsDataURL(input.files[0]);
                    }
                }
                $(document).ready(function(){

                $("input[type=file]").change(function(){
                    if( document.getElementById("fileToUpload").files.length == 0 ){
                        $('#blah').attr('src', "assets/images/icons/blank_image.png");
                    }
                });
            });

                $("#fileToUpload").change(function(){
                    readURL(this);
                });
                </script>
                    
                    <textarea style="overflow:hidden" name="post_text" id="post_text" placeholder="Write a post..." oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"'></textarea>
                    <center><input type="submit" name="post" id="post_button" value="Post"></center>
                </form>
                <?php } ?>

                <div class="posts_area"></div>
                <img id="loading" src="assets/images/icons/loading.gif" alt="Loading...">
            </div>

            <script>
                var userLoggedIn = '<?php echo $userLoggedIn; ?>';
                var profileUsername = '<?php echo $username; ?>'
                $(document).ready(function() {

                    $('#loading').show();

                    //Original ajax request for loading first posts 
                    $.ajax({
                        url: "includes/handlers/ajax_load_profile_posts.php",
                        type: "POST",
                        data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                        cache:false,

                        success: function(data) {
                            $('#loading').hide();
                            $('.posts_area').html(data);
                        }
                    });

                    $(window).scroll(function() {
                        var height = $('.posts_area').height(); //Div containing posts
                        var scroll_top = $(this).scrollTop();
                        var page = $('.posts_area').find('.nextPage').val();
                        var noMorePosts = $('.posts_area').find('.noMorePosts').val();

                        if (($(window).scrollTop() >= ($(document).height() - $(window).height())*0.9) && noMorePosts == 'false') {
                            $('#loading').show();
                            var ajaxReq = $.ajax({
                                url: "includes/handlers/ajax_load_profile_posts.php",
                                type: "POST",
                                data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                                cache:false,

                                success: function(response) {
                                    $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
                                    $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

                                    $('#loading').hide();
                                    $('.posts_area').append(response);
                                }
                            });

                        } //End if 

                        return false;

                    }); //End (window).scroll(function())


                });
            </script>

        </div><!--Closing div for wrapper from header-->
    </body>
</html>