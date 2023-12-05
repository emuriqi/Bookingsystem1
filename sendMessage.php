<?php
session_start();
include "config.php";
if(!isset($_SESSION['admin_name']) && !isset($_SESSION['user_name'])){
	header('location:login_form.php');
	exit(); 
 }
 

if($_POST)
{
	$id=$_SESSION['user_id'];
    $msg=$_POST['msg'];
    
	$sql="INSERT INTO `chat`(`user_id`, `message`) VALUES ('".$id."', '".$msg."')";

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