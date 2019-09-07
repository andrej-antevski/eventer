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
            header("Location: index.php");
        }
        else {
            echo "<div style='text-align:center' class='alert-danger'>$errorMessage</div>";
        }


    }

?>
            <div class="user_details column">
                <a href="<?php echo $userLoggedIn?>"> <img src="<?php echo $user['profile_pic'];?>" alt="Profile Picture"> </a>
                
                <div class="user_details_left_right">
                    <a href="<?php echo $userLoggedIn?>">
                        <?php echo $user['first_name'] . " " . $user['last_name']; ?><br>
                    </a>
                    
                    <?php echo "Posts :" . $user['num_posts'];
                    echo  "<br>Likes :" . $user['num_likes']; ?>
                </div>
            </div>
            <div class="main_column column">
                <form class="post_form" action="index.php" method="post" enctype="multipart/form-data" id="dropContainer">
                                        
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

                <div class="posts_area"></div>
                <img id="loading" src="assets/images/icons/loading.gif" alt="Loading...">

            </div>

            <script>
                var userLoggedIn = '<?php echo $userLoggedIn; ?>';
                $(document).ready(function() {

                    $('#loading').show();

                    //Original ajax request for loading first posts 
                    $.ajax({
                        url: "includes/handlers/ajax_load_posts.php",
                        type: "POST",
                        data: "page=1&userLoggedIn=" + userLoggedIn,
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

                        if (($(window).scrollTop() >= ($(document).height() - $(window).height())*0.9) && noMorePosts == 'false' ) {
                            $('#loading').show();
                            var ajaxReq = $.ajax({
                                url: "includes/handlers/ajax_load_posts.php",
                                type: "POST",
                                data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
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