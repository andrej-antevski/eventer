<?php
    include '../../config/config.php';
    include '../classes/User.php';
    include '../classes/Post.php';

    $limit=10;//number of posts to be loaded pre call

    $posts = new Post($con, $_REQUEST['userLoggedIn']);
    $posts->loadPostsFriends($_REQUEST, $limit);
?>