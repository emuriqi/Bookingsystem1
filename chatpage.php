<?php 
	session_start();
	if(isset($_SESSION['id']))
	{
		include "config.php"; 
		
		$sql = "SELECT c.message, u.name 
        FROM chat c 
        JOIN user_form u ON c.id = u.id 
        ORDER BY c.timestamp DESC";


		$query = mysqli_query($conn,$sql);
?>

<div class="container">
  <center><h2>Welcome</h2>
	<label>Join the chat</label>
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
				
				<span><?php echo $row['name']; // This now fetches the user's name from the joined user_form table ?> :</span>

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
	         
      <div class="col-sm-2">
        <button type="submit" class="btn btn-primary">Send</button>
      </div>

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