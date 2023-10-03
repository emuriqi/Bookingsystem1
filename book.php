
<?php
$date = ""; // Initialiser variabelen

if (isset($_GET['date'])) {
    $date = $_GET['date'];
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/book.css">
    <title>Book for date</title>
</head>
<body>
    <div class="container">
    <h1 class="text-center">Book for date: <?php echo date('d/m/Y', strtotime($date)); ?></h1>
        <div class="row">
        <div class="col-md-6 col-md-offset-3">
        <form action="" method="post">
            <div class="form-group">
                <label for="">Name</label>
                <input type="text" class="form-control" name="name"> 
            </div>
            <div class="form-group">
                <label for="">Email</label>
                <input type="email" class="form-control" name="email">
            </div>
            <button class="btn btn-primary" type="submit" name="submit">Submit</button>
        </form>
        </div>
        </div>

    </div>
</body>
</html>