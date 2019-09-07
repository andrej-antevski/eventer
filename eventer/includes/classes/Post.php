<?php
class Post{
    private $user_obj;
    private $con;

    public function __construct($con,$user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }
    public function loadPostsFriends($data, $limit){
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();

        if($page==1) $start = 0;
        else $start = ($page-1)*$limit;

        $str="";
        $data_query= mysqli_query($this->con,"SELECT * FROM posts WHERE user_closed='no' AND deleted = 'no' ORDER BY id DESC");

        if(mysqli_num_rows($data_query) > 0){
            $num_iterations = 0;
            $count = 1;

            while($row=mysqli_fetch_array($data_query)){
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];
                $imagePath = $row['image'];
                
                //if posted on own wall not to someone
                if ($row['user_to']=="none"){
                    $user_to="";
                }
                else{
                    $user_to_obj = new User($con, $row['user_to']);
                    $user_to_name = $user_to_obj->GetFirstAndLastName();
                    $user_to = "to <a href='". $row['user_to'] . ".>" . $user_to_name . "</a>";
                }
                
                //check if account is closed
                $added_by_object = new User($this->con, $row['user_to']);
                if($added_by_object->isClosed()){
                    continue;
                }
                
                $user_logged_obj=new User($this->con,$userLoggedIn);
                if($user_logged_obj->isFriend($added_by)){
                    if ($num_iterations++ < $start) continue;

                    if($count > $limit) break;
                    else $count++ ;

                    if($userLoggedIn==$added_by) $delete_button="<button class='delete_button btn-danger' title='Delete this post' id='post$id'>X</button>";
                    else $delete_button = "";

                    $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users where username = '$added_by'");
                    $user_row=mysqli_fetch_array($user_details_query);
                    $first_name = $user_row['first_name'];
                    $last_name = $user_row['last_name'];
                    $profile_pic = $user_row['profile_pic'];
                    //show comments
                    ?>
                    <script>
                        function toggle<?php echo $id;?>(){

                            var target=$(event.target);
                            if (!target.is("a")){
                                var element = document.getElementById("toggleComment<?php echo $id; ?>");
                                if(element.style.display == "block") element.style.display = "none";
                                else element.style.display = "block";
                            }
                    }
                    </script>
                    <?php
                    $comments_check = mysqli_query($this->con,"SELECT * FROM comments WHERE post_id='$id' AND removed='no'");
                    $comments_check_num = mysqli_num_rows($comments_check);
                    //timeframe
                    $date_time_now= date("Y-m-d H:i:s");
                    $start_date = new DateTime($date_time);//time of post
                    $end_date = new DateTime($date_time_now);//current time
                    $interval = $start_date->diff($end_date);
                    //for years
                    if($interval->y >= 1){
                        if($interval == 1) $time_message = $interval->y . " year ago";
                        else $time_message = $interval->y . " years ago";
                    }
                    //for months and days
                    else if ($interval->m >= 1){
                        if($interval->d == 0) $days = "ago";
                        else if ($interval->d ==1 ) $days = $interval->d . " day ago";
                        else $days =$interval->d . " days ago";
                        if($interval->m == 1) $time_message = $interval->m . " month" . $days;
                        else $time_message = $interval->m . " months" . $days;       
                    }
                    else if($interval->d >= 1){
                        if($interval->d==1) $time_message = "Yesterday";
                        else $time_message = $interval->d . " days ago";
                    }
                    else if($interval->h >= 1){
                        if($interval->d==1) $time_message = $interval->h . " hour ago";
                        else $time_message = $interval->h . " hours ago";
                    }
                    else if($interval->i >= 1){
                        if($interval->i==1) $time_message = $interval->i . " minute ago";
                        else $time_message = $interval->i . " minutes ago";
                    }
                    else{
                        if($interval->s < 30) $time_message = "Just now";
                        else $time_message = $interval->s . " seconds ago";                    
                    }

                    if($imagePath != "none"){
                        $imageDiv = "<div class='postedImage'><img src='$imagePath'></div>";
                    }
                    else $imageDiv = "";

                    $str .= "<div class='status_post'>
                                <div class='first_line'>
                                    <div class='post_profile_pic'>
                                        <img src='$profile_pic' width='50'>
                                    </div>
                                    <div class='posted_by' style='color:#acacac;'>
                                        <a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;<a style='color:#acacac; font-size:15px' href='post.php?post_id=$id'>$time_message</a>
                                        $delete_button
                                    </div>
                                </div>
                                <div id='post_body'>$body<br>
                                $imageDiv
                                </div>
                            </div>
                            <div class='newsfeedPostOptions'>
                                <p style='display:inline;' onClick='javascript:toggle$id()'>Comments( $comments_check_num )</p>
                                &nbsp;&nbsp;&nbsp;
                                <iframe src='like.php?post_id=$id'></iframe>

                                <p title='Who liked this post' style='display:inline;' data-toggle='modal' data-target='#exampleModal$id' >?</p>

                                <div class='modal fade' id='exampleModal$id' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                                <div class='modal-dialog' role='document'>
                                    <div class='modal-content'>
                                    <div class='modal-header'>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                        <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>
                                    <div class='modal-body' style='height:500px;'>
                                        <iframe src='likers.php?post_id=$id' id='like_iframe'></iframe>
                                    </div>
                                    <div class='modal-foote'>

                                    </div>
                                    </div>
                                </div>
                                </div>

                            </div>
                            <div class='post_comment' id='toggleComment$id' style='display:none;'>
                                <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0' required></iframe>
                            </div>

                            <hr>";
                }
                ?>
                <script>
                    $(document).ready(function() {
                        $('#post<?php echo $id; ?>').on('click', function() {
                            bootbox.confirm("Are you sure you want to delete this post?", function(result) {
                                $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});
                                if(result)
                                    location.reload();
                            });
                        });
                    });
                </script>
                <?php
            }//end while
            if($count>$limit)
                $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                        <input type='hidden' class='noMorePosts' value='false'>";
            else
                $str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align:center;'>No more posts</p>";
        }
        echo $str;
    }
    public function loadProfilePosts($data, $limit){
        $page = $data['page'];
        $profileUser = $data['profileUsername'];
        $userLoggedIn = $this->user_obj->getUsername();

        if($page==1) $start = 0;
        else $start = ($page-1)*$limit;

        $str="";
        $data_query= mysqli_query($this->con,"SELECT * FROM posts WHERE deleted = 'no' AND (added_by='$profileUser' AND user_to='none') ORDER BY id DESC");
        if(mysqli_num_rows($data_query) ==0){
            echo "This person hasn't posted yet";
        }
        if(mysqli_num_rows($data_query) > 0){
            $num_iterations = 0;
            $count = 1;

            while($row=mysqli_fetch_array($data_query)){
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];
                $imagePath = $row['image'];

                    if ($num_iterations++ < $start) continue;

                    if($count > $limit) break;
                    else $count++ ;

                    if($userLoggedIn==$added_by) $delete_button="<button class='delete_button btn-danger' title='Delete this post' id='post$id'>X</button>";
                    else $delete_button = "";

                    $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users where username = '$added_by'");
                    $user_row=mysqli_fetch_array($user_details_query);
                    $first_name = $user_row['first_name'];
                    $last_name = $user_row['last_name'];
                    $profile_pic = $user_row['profile_pic'];
                    //show comments
                    ?>
                    <script>
                        function toggle<?php echo $id;?>(){

                            var target=$(event.target);
                            if (!target.is("a")){
                                var element = document.getElementById("toggleComment<?php echo $id; ?>");
                                if(element.style.display == "block") element.style.display = "none";
                                else element.style.display = "block";
                            }
                    }
                    </script>
                    <?php
                    $comments_check = mysqli_query($this->con,"SELECT * FROM comments WHERE post_id='$id'");
                    $comments_check_num = mysqli_num_rows($comments_check);
                    //timeframe
                    $date_time_now= date("Y-m-d H:i:s");
                    $start_date = new DateTime($date_time);//time of post
                    $end_date = new DateTime($date_time_now);//current time
                    $interval = $start_date->diff($end_date);
                    //for years
                    if($interval->y >= 1){
                        if($interval == 1) $time_message = $interval->y . " year ago";
                        else $time_message = $interval->y . " years ago";
                    }
                    //for months and days
                    else if ($interval->m >= 1){
                        if($interval->d == 0) $days = "ago";
                        else if ($interval->d ==1 ) $days = $interval->d . " day ago";
                        else $days =$interval->d . " days ago";
                        if($interval->m == 1) $time_message = $interval->m . " month" . $days;
                        else $time_message = $interval->m . " months" . $days;       
                    }
                    else if($interval->d >= 1){
                        if($interval->d==1) $time_message = "Yesterday";
                        else $time_message = $interval->d . " days ago";
                    }
                    else if($interval->h >= 1){
                        if($interval->d==1) $time_message = $interval->h . " hour ago";
                        else $time_message = $interval->h . " hours ago";
                    }
                    else if($interval->i >= 1){
                        if($interval->i==1) $time_message = $interval->i . " minute ago";
                        else $time_message = $interval->i . " minutes ago";
                    }
                    else{
                        if($interval->s < 30) $time_message = "Just now";
                        else $time_message = $interval->s . " seconds ago";                    
                    }
                    
                    if($imagePath != "none"){
                        $imageDiv = "<div class='postedImage'><img src='$imagePath'></div>";
                    }
                    else $imageDiv = "";
                    $str .= "<div class='status_post'>
                                <div class='first_line'>
                                    <div class='post_profile_pic'>
                                        <img src='$profile_pic' width='50'>
                                    </div>
                                    <div class='posted_by' style='color:#acacac;'>
                                        <a href='$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;<a style='color:#acacac; font-size:15px' href='post.php?post_id=$id'>$time_message</a>
                                        $delete_button
                                    </div>
                                </div>
                                <div id='post_body'>$body<br>
                                $imageDiv
                                </div>
                            </div>
                            <div class='newsfeedPostOptions'>
                                <p style='display:inline;' onClick='javascript:toggle$id()'>Comments( $comments_check_num )</p>
                                &nbsp;&nbsp;&nbsp;
                                <iframe src='like.php?post_id=$id'></iframe>

                                <p title='Who liked this post' style='display:inline;' data-toggle='modal' data-target='#exampleModal$id' >?</p>

                                <div class='modal fade' id='exampleModal$id' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                                <div class='modal-dialog' role='document'>
                                    <div class='modal-content'>
                                    <div class='modal-header'>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                        <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>
                                    <div class='modal-body' style='height:500px;'>
                                        <iframe src='likers.php?post_id=$id' id='like_iframe'></iframe>
                                    </div>
                                    <div class='modal-foote'>

                                    </div>
                                    </div>
                                </div>
                                </div>

                            </div>
                            <div class='post_comment' id='toggleComment$id' style='display:none;'>
                                <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0' required></iframe>
                            </div>

                            <hr>";
                ?>
                <script>
                    $(document).ready(function() {
                        $('#post<?php echo $id; ?>').on('click', function() {
                            bootbox.confirm("Are you sure you want to delete this post?", function(result) {
                                $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});
                                if(result)
                                    location.reload();
                            });
                        });
                    });
                </script>
                <?php
            }//end while
            if($count>$limit)
                $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                        <input type='hidden' class='noMorePosts' value='false'>";
            else
                $str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align:center;'>No more posts</p>";
        }
        echo $str;
    }
    public function submitPost($body, $user_to, $imageName){
            $body = str_replace("&","&amp",$body);
            $body = str_replace("<","&lt;",$body);
            $body = str_replace('"',"&quot;",$body);
            $body = str_replace("'","&#39;",$body);
            $body = strip_tags($body);
            $body = mysqli_real_escape_string($this->con, $body);
            $check_empty = preg_replace('/\s+/', '',$body);

            if($check_empty!= ""){
                //current date and time
                $date_added = date("Y-m-d H:i:s");
                //who shared it
                $added_by = $this->user_obj->getUsername();
                //to who it is shared, if to none it is on your wall
                if ($user_to == $added_by){
                    $user_to="none";
                }

                //insert post
                //$query = mysqli_query($this->con, "INSERT into posts values ('','$body','$added_by','$user_to','$date_added','no','no','0','$imageName')");

                $last_id = mysqli_query($this->con,"SELECT max(id) FROM posts");
                $last_id_a = mysqli_fetch_array($last_id);
                $id = $last_id_a[0]+1;

                $body .= " ";
                $body_arr = str_split($body);
                $word="";
                $start_pos = 0;
                $end_pos = 0;
                $len = strlen($body);
                for($i=0;$i<$len;$i++,$start_pos++,$end_pos++){
                    if($body_arr[$i] == '#'){ 
                        $i++;
                        $end_pos++;
                        while($body_arr[$i] != '#' && $body_arr[$i] != " " && $i<$len){
                            $word .= $body_arr[$i];
                            $i++;
                            $end_pos++;
                        }
                        //insert hyperlink
                        $replace = '<a href="trending.php?word='.$word.'">';
                        $body = substr_replace($body,$replace,$start_pos,0);
                        $end_pos+=strlen($word)+29;
                        if(strlen($word)==0)$body = substr_replace($body,"</a>",$end_pos-1,0);
                        else
                        $body = substr_replace($body,"</a>",$end_pos,0);
                        $start_pos+=2*strlen($word)+29+1+4;
                        $end_pos=$start_pos;
                        //insert word
                        if(strlen($word)>0)
                            $query = mysqli_query($this->con,"INSERT INTO trending_words VALUES ('','$word','$date_added','$id','$added_by')");
                            
                        $word="";
                    }
                }

                $query = mysqli_query($this->con, "INSERT into posts values ('','$body','$added_by','$user_to','$date_added','no','no','0','$imageName')");


                $returned_id = mysqli_insert_id($this->con);

                //update post counter
                $num_posts = $this->user_obj->getNumPosts();
                $num_posts++;
                $update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username = '$added_by'");

            }
        }






    
    
    }
?>