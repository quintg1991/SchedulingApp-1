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
        $sql = "SELECT * FROM reservations ORDER BY roomnumber WHERE email = '".$user."'"; //may need changing later
        $result = $conn->query($sql);

        //calls update.php on submit
        echo '<form action = "PHP/update.php">';
        
        if ($result->num_rows > 0) {
            // output data of each row as a radio input using id and value
            while($row = $result->fetch_assoc()) {
               echo "<input type=\"radio\" name=\"choice\" value=\"". $row["id"]."\" >";
               echo $row["roomnumber"]. " reserved from ". $row["start"].
                        " until ". $row["end"];
               echo "</br>";
            }
            echo '<input type="submit" value="Update"';
            echo "</form>";
        } else {
            echo "No reservations.";
        }       
        

        $conn->close();
        ?>
    </body>
</html>
