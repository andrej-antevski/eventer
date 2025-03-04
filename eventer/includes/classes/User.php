<?php
class User{
    private $user;
    private $con;

    public function __construct($con,$user){
        $this->con = $con;
        $user_details_query = mysqli_query($con,"SELECT * FROM users WHERE username='$user'");
        $this->user = mysqli_fetch_array($user_details_query);
    }

    public function getUsername(){
        return $this->user['username'];
    }

    public function getNumPosts(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con,"SELECT num_posts FROM users where username='$username'");
        $row = mysqli_fetch_array($query);
        return $row['num_posts'];
    }

    public function GetFirstAndLastName(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "SELECT first_name, last_name FROM users WHERE username='$username'");
        $row = mysqli_fetch_array($query);
        return $row['first_name'] . " " . $row['last_name'];
    }
    public function isClosed(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con,"SELECT user_closed FROM users WHERE username='$username'");
        $row = mysqli_fetch_array($query);
        if($row['user_closed']=='yes') return true;
        else return false;

    }
    public function isFriend($username_to_check){
        $usernameComma="," . $username_to_check . ",";

        if(strstr($this->user['friend_array'],$username_to_check) || $username_to_check==$this->user['username']) return true;
        else return false;
    }
    public function getProfilePic(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "SELECT profile_pic FROM users WHERE username='$username'");
        $row = mysqli_fetch_array($query);
        return $row['profile_pic'];
    }
    public function didReceiveRequest($user_from){
        $user_to = $this->user['username'];
        $check_request_query = mysqli_query($this->con,"SELECT * FROM friend_request WHERE user_to='$user_to' AND user_from='$user_from'");
        if(mysqli_num_rows($check_request_query) > 0) return true;
        else return false;
    }
    public function didSendRequest($user_to){
        $user_from = $this->user['username'];
        $check_request_query = mysqli_query($this->con,"SELECT * FROM friend_request WHERE user_to='$user_to' AND user_from='$user_from'");
        if(mysqli_num_rows($check_request_query) > 0) return true;
        else return false;
    }
    public function removeFriend($user_to_remove){
        $logged_in_user = $this->user['username'];

        $query = mysqli_query($this->con,"SELECT friend_array FROM users WHERE username='$user_to_remove'");
        $row = mysqli_fetch_array($query);
        $friend_array_username = $row['friend_array'];

        $new_friend_array = str_replace($user_to_remove . ",","",$this->user['friend_array']);
        $remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$logged_in_user'");

        $new_friend_array = str_replace($this->user['username'] . ",","",$friend_array_username);
        $remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$user_to_remove'");
    }
    public function sendRequest($user_to){
        $user_from = $this->user['username'];
        $query = mysqli_query($this->con,"INSERT INTO friend_request VALUES ('','$user_to','$user_from')");
    }
    public function getFriendArray(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$username'");
        $row = mysqli_fetch_array($query);
        return $row['friend_array'];
    }


    public function getMutualFriends($user_to_check){
        $mutualFriends=0;
        $user_array = $this->user['friend_array'];
        $user_array_explode = explode(",", $user_array);

        $query = mysqli_query($this->con,"SELECT friend_array FROM users WHERE username = '$user_to_check'");
        $row = mysqli_fetch_array($query);
        $user_to_check_array = $row['friend_array'];
        $user_to_check_array_explode = explode(",", $user_to_check_array);
        //TODO: make a more efficient checking method this has complexity of n^2
        foreach($user_array_explode as $i){
            foreach($user_to_check_array_explode as $j){

                if($i==$j && $i!="") $mutualFriends++;

            }
        }
        return $mutualFriends;
    }
    public function showMutualFriends($user_to_check){ 
        $user_array = $this->user['friend_array'];
        $user_array_explode = explode(",", $user_array);

        $query = mysqli_query($this->con,"SELECT friend_array FROM users WHERE username = '$user_to_check'");
        $row = mysqli_fetch_array($query);
        $user_to_check_array = $row['friend_array'];
        $user_to_check_array_explode = explode(",", $user_to_check_array);
        //check sorted arrays, complexity O(n+m)
        $m = count($user_array_explode);
        $n = count($user_to_check_array_explode);
        $i = 0;
        $j = 0;
        while ($i < $m && $j < $n) 
        { 
            if ($user_array_explode[$i] < $user_to_check_array_explode[$j]) 
            $i++; 
            else if ($user_array_explode[$i] > $user_to_check_array_explode[$j]) 
            $j++; 
            else {
                $query_mutual = mysqli_query($this->con,"SELECT * FROM users WHERE username = '$user_array_explode[$i]'");
                $row_mutual = mysqli_fetch_array($query_mutual);
                $fname = $row_mutual['first_name'];
                $lname = $row_mutual['last_name'];
                $profilepic = $row_mutual['profile_pic'];
                echo "<a href='$user_array_explode[$i]'><img src='$profilepic' width='50' class='profile_modul'>".$fname." ".$lname."</a><br>";
                $i++; 
                $j++; 
            } 
        }  
    }
    public function showAllFriends($user_to_check){
        $query = mysqli_query($this->con,"SELECT friend_array FROM users WHERE username = '$user_to_check'");
        $row = mysqli_fetch_array($query);
        $user_to_check_array = $row['friend_array'];
        $user_to_check_array_explode = explode(",", $user_to_check_array);
        foreach($user_to_check_array_explode as $j){
                $query_mutual = mysqli_query($this->con,"SELECT * FROM users WHERE username = '$j'");
                $row_mutual = mysqli_fetch_array($query_mutual);
                $fname = $row_mutual['first_name'];
                $lname = $row_mutual['last_name'];
                $profilepic = $row_mutual['profile_pic'];
                echo "<a href='$j'><img src='$profilepic' width='50' class='profile_modul'>".$fname." ".$lname."</a><br>";
            }
        }
}
?>