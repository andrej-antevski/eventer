<?php

class Trending{
    private $con;

    public function __construct($con){
        $this->con = $con;
    }

    public function MostRecent($limit,$offset,$word){
        $str = "";

        $data_query = mysqli_query($this->con, "SELECT * FROM trending_words LIMIT $limit OFFSET $offset");
        
        
        if(mysqli_num_rows($data_query) > 0){

            while($row = mysqli_fetch_array($data_query)){
                $id = $row['id'];

                $str .= $id . "<br><br><br><br><br><br><br><br><br><br><br>";
                
                
            }
            echo $str;
        }
        else echo "no more posts";

        


    }
}



?>