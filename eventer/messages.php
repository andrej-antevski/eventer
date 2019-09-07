<?php
    include 'includes/header.php';
    $disabled='';
    $placeholder="Write Your Message";
    $message_obj = new Message($con, $userLoggedIn);
    if(isset($_GET['u'])){
        $user_to = $_GET['u'];
        $close_q = mysqli_query($con,"SELECT * FROM users WHERE username='$user_to' AND user_closed='yes'");
        if(mysqli_num_rows($close_q) > 0 ){
            $disabled="disabled";
            $placeholder="You can&#39;t reply to the conversation. This person has closed their account";
        }
    }
    else{
        $user_to = $message_obj->getMostRecentUser();
        $close_q = mysqli_query($con,"SELECT * FROM users WHERE username='$user_to' AND user_closed='yes'");
        if(mysqli_num_rows($close_q) > 0 ){
            $disabled="disabled";
            $placeholder="You can&#39;t reply to the conversation. This person has closed their account";
        }
        if($user_to == false)
            $user_to = 'new';
    }
    if($user_to != 'new') $user_to_obj = new User($con, $user_to);
    if(isset($_POST['post_message'])){
        if(isset($_POST['message_body'])){
            $body = mysqli_real_escape_string($con,$_POST['message_body']);
            $date = date("Y-m-d H:i:s");
            $message_obj->sendMessage($user_to,$body,$date);
        }
    }

?>
    <script>
        function submitChat(){
            //sends the data	
            var msg = form1.message_body.value;
            msg = msg.replace(new RegExp("&", 'g'), "%26");
            msg = msg.replace(new RegExp("#", 'g'), "%23");
            msg = msg.replace(new RegExp("<", 'g'), "%3C");
            msg = msg.replace(new RegExp('"', 'g'), "%22");
            msg = msg.replace(new RegExp("'", 'g'), "%E2%80%98");
            

            var xmlhttp = new XMLHttpRequest();
            
            xmlhttp.onreadystatechange = function(){
                    if(xmlhttp.readyState==4&&xmlhttp.status==200){
                            
                        }
                }
            xmlhttp.open('GET','insert.php?user_from=<?php echo $userLoggedIn;?>&user_to=<?php echo $user_to ?>&msg='+msg,true);
            xmlhttp.send();
            document.getElementById("message_textarea").value = "";
            setTimeout(function() {$('#scroll_messages').load('logs.php?user_from=<?php echo $userLoggedIn;?>&user_to=<?php echo $user_to ?>');}, 0);
		}
        $(document).ready(function(e) {
            $.ajaxSetup({cache:true});
            setTimeout(function(){$( "#loaded_conversations" ).load('conversations.php?loggedIn=<?php echo $userLoggedIn;?>');} , 0);
            setTimeout(function() {$('#scroll_messages').load('logs.php?user_from=<?php echo $userLoggedIn;?>&user_to=<?php echo $user_to ?>');}, 0);
            setInterval(function() {$('#scroll_messages').load('logs.php?user_from=<?php echo $userLoggedIn;?>&user_to=<?php echo $user_to ?>');}, 3000);
            setInterval(function(){$( "#loaded_conversations" ).load('conversations.php?loggedIn=<?php echo $userLoggedIn;?>');} , 3000);
            
            });

        $(function(){
			 $("#message_textarea").keyup(function (e) {
			  if (e.which == 13) {
				$('input[name="post_message"]').trigger('click');	
				$(this).val("");
						e.preventDefault();
                        document.getElementById("message_textarea").value = "";
			  }
			 });
		});
    </script>
    
    <!--
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
        -->
    <div class="main_column column" id="main_column">
        <div class="message_post">
                <form name="form1">
                    <?php
                        if($user_to == "new"){
                            echo "Select a friend to send a message <br><br>";?>
                            To: <input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Name' autocomplete='off' id='seach_text_input'>
                            <?php
                            echo "<div class='results'></div>";
                        }
                        else{
                            echo "<textarea name='message_body' id='message_textarea' placeholder='$placeholder' $disabled></textarea>";
                            echo "<input type='button' name='post_message' class='info' id='message_submit' value='Send' onclick='submitChat()' $disabled>";
                        }
                    ?>
                </form>
        </div>
        <?php
            if($user_to != "new"){ 
                echo "<div class='loaded_messages' id='scroll_messages'>";
                echo "</div>";
                echo "<h4> You and <a href='$user_to'>" . $user_to_obj->GetFirstAndLastName() . "</a></h4><hr>";
            }
        ?>

    </div>
    <div class="user_details column" id="conversations">
        <h4>Conversations</h4>
        <div class="loaded_conversations" id="loaded_conversations">
        </div>
        <br>
        <a href="messages.php?u=new">New Message</a>
    </div>
</div><!---wrapper end-->