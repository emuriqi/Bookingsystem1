<?php 
include 'config.php';

    if( isset($_GET["id"])){
        $id = $_GET["id"]; 

        
        $sql = "DELETE FROM bookings WHERE id=$id";
        $conn->query($sql);


    }

header("location: oversiktUser.php");
exit; 

?>