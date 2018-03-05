<?php
	
	$useremail = $_POST['useremail'];
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "cs455";
				
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$sql = "SELECT * FROM favorites WHERE email='$useremail'";
	$result = $conn->query($sql);
	
	if($result != false){
		echo "<table><tr><th>Building</th><th>Room</th></tr>";
		while ($row = $result->fetch_assoc()) {
			echo "<tr><td>Keller Hall</td><td>" . $row['roomid'] . "</td></tr>";
		}
	}
	$conn->close();
