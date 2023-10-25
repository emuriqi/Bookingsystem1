<?php

include "config.php";
session_start();
if($_POST)
{
	$id=$_SESSION['id'];
    $msg=$_POST['msg'];
    
	$sql="INSERT INTO `chat`(`id`, `message`) VALUES ('".$id."', '".$msg."')";

	$query = mysqli_query($conn,$sql);
	if($query)
	{
		header('Location: chatpage.php');
	}
	else
	{
		echo "Something went wrong";
	}
	
	}
?>