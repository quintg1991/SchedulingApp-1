<?php session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.html"); 
    exit;
}
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cs455";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
		$room = $_REQUEST['roomnumber'];
		$hour = $_REQUEST['starthour'];
        $min = $_REQUEST['startminute'];

		$sql = "UPDATE reservations SET roomnumber = '".$room."', starthour = '".$hour."', startminute = '".$min."'";	//may need updating
        
        if ($conn->query($sql) === TRUE) 
		{
			$_SESSION['error'] = '';
		} 
		else 
		{
			$_SESSION['error'] = "Error updating record: " . $conn->error;
		}

        $conn->close();
        ?>
    </body>
</html>
