<?php
    include 'includes/header.php';

    if(isset($_GET['word'])){
        $word= $_GET['word'];

        $num_q = mysqli_query($con,"SELECT * FROM trending_words WHERE word='$word'");
        $num = mysqli_num_rows($num_q);
    } 

?>

<div class='posts_area'></div>
<img id="loading" src="assets/images/icons/loading.gif" alt="Loading...">

<script>
        
    var word = '<?php echo $word ?>';
    var max= <?php echo $num ?>;
    var offset = 0;
    var limit = 10;
    var holdload = false;

    $('#loading').show();
    
    $(function(){
        loadPosts(4);
    });

    $(window).scroll(function(){
        if($(window).scrollTop() >= $(document).height() - $(window).height() - 100){
            
            if(offset<=10+max)
            loadPosts(12);
        }
    });

    function loadPosts(a){
        if(!holdload){
            $('#loading').show();        
            holdload = true;
            $.ajax({
                url: "includes/handlers/ajax_load_trending.php",
                type : "POST",
                data : {
                    offset:offset,
                    limit:limit,
                    word:word
                },
                dataType: "text",
                success: function(data){
                    $(".posts_area").append(data);
                    $('#loading').hide();
                    holdload = false; 
                    offset+=10;
                }
            });
        }
        
    }
</script>