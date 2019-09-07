<?php
    include '../../config/config.php';
    include '../classes/Trending.php';

    $offset = $_POST['offset'];
    $limit = $_POST['limit'];
    $word = $_POST['word'];

    $posts = new Trending($con);
    $posts->MostRecent($limit, $offset,$word);
?>