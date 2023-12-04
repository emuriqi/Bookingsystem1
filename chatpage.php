<?php 
	session_start();
	if(!isset($_SESSION['admin_name']) && !isset($_SESSION['user_name'])){
		header('location:login_form.php');
		exit(); // Sørger for at ingen ytterligere behandling blir gjort etter omdirigeringen
	 }
	 
	if(isset($_SESSION['user_id']))
	{
		include "config.php"; 
		
		$sql = "SELECT c.message, u.name 
        FROM chat c 
        JOIN user_form u ON c.user_id = u.user_id 
        ORDER BY c.timestamp DESC";


		$query = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/chat.css">
</head>
<body>
<div class="container">
  <center><h2>Velkommen</h2>
	<label>Start chat</label>
  </center></br>
  <div class="display-chat">
<?php
	if(mysqli_num_rows($query)>0)
	{
		while($row= mysqli_fetch_assoc($query))	
		{
?>
		<div class="message">
			<p>
				
				<span><?php echo $row['name']; // Henter brukenes navn fra tabell med brukerskjema.  ?> :</span>

				<?php echo $row['message']; ?>
			</p>
		</div>
<?php
		}
	}
	else
	{
?>
<div class="message">
			<p>
				No previous chat available.
			</p>
</div>
<?php
	} 
?>

  </div>
  <form class="form-horizontal" method="post" action="sendMessage.php">
    <div class="form-group">
      <div class="col-sm-10">          
        <textarea name="msg" class="form-control" placeholder="Type your message here..."></textarea>
      </div>
	         
      

    </div>
	<div class="col-sm-2">
        <button type="submit" class="btn btn-primary">Send</button>
      </div>
  </form>
</div>

</body>
</html>
<?php
	}
	else
	{
		header('location:admin.php');
	}
?>