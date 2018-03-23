<?php session_start();

	/*=========================================================================
		Retrive Rooms PHP Script
		Purpose: Generates the rooms table that is shown on the primary page.
		constraints are given via GET, and the table generated will provide only
		those rooms who meet those constraints.
		Constraints are:
		1. Start time and end time and date - note that we need both of these to validate
		if a room can be booked or not. The range is checked for each room. If a room
		is taken during that range of time, then the room will note be returned from 
		the query.

		2. Room Type - the type of room as defined in the database. If this constraint
		is specified, then we will query only for those rooms that are defined to be
		that type. For example, if $_GET['roomtype'] == 'Classroom', then only rooms
		with a type of 'classroom' will be shown.

		3. Roomsharing and headcount - if this is roomsharing is true, then we check 
		the headcount field. Computations are performed to ensure that headcount doesn't
		exceed the number of open seats in each room. Only those rooms which have enough
		seats (even with other professors who choose to share and give their headcount)
		will be shown. Note that we will only be posting a headcount. They can only type
		a headcount when allow room sharing is checked, meaning a headcount is what we will
		be waiting for. If we get headcount, we know they selected allow room sharing.

		4. recur - recur is an enumerated type, a check will be performed to ensure 
		the user's preference on recurring reservations doesn't conflict with another
		reservation somewhere down the line in the semester (recall that recurring 
		reservations will be valid for only the length of the semester.

		The variables for these are 'starttime', 'endtime', 'type', 'headcount', 
		'recur'.


		Note that we also check the current group the $_SESSION['username'] is 
		in to ensure they only see rooms they are allowed to book.


	=========================================================================*/

	//////////////////////////////////////////////////////////////////////////
	// GET GET VARIABLES
	$starttime = (isset($_GET['starttime'])) ? $_GET['starttime'] : null; // format as 'H:i' (24-hr format)
	$endtime = (isset($_GET['endtime'])) ? $_GET['endtime'] : null;     // format as 'H:i' (24-hr format)
	$type = (isset($_GET['type'])) ? $_GET['type'] : null;
	$headcount = (isset($_GET['headcount'])) ? $_GET['headcount'] : null;
	$recur_enum = (isset($_GET['recur'])) ? $_GET['recur'] : null;
	$date = (isset($_GET['date'])) ? $_GET['date'] : null; // format as 'Y/d/m'
	//////////////////////////////////////////////////////////////////////////

	//set username if not already set for testing
	if (!isset($_SESSION['username'])){
			$_SESSION['username'] = "dbrown4@una.edu";
	}

	//obtain datbase metadata
    require "db_conf.php";

	//perform conneciton to database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
	

	$sql = "SELECT DISTINCT rooms.roomid, rooms.seats, rooms.type FROM rooms ";
	$additional = "";


	if($starttime != null || $endtime != null || $type != null || $headcount != null || $recur_enum != null){

		//if user provides times
		if($starttime != null && $endtime != null && $date != null){
			//ensure user gives valid time first
			if(!checkDateTime(true, substr($starttime,1,-1), substr($endtime,1,-1))){
				
				exit;
			}

			$additional = $additional . "LEFT JOIN (SELECT DISTINCT roomid, seats, type FROM rooms RIGHT JOIN reservations ON rooms.roomid = reservations.roomnumber 
				WHERE startdate = $date AND (allowshare = '0' AND ((starttime > $starttime AND endtime <= $endtime) OR (endtime >= $endtime AND starttime < $endtime) 
				OR(starttime < $starttime AND endtime <= $endtime)))) AS subquery ON rooms.roomid = subquery.roomid WHERE subquery.roomid IS NULL AND ";


/*
			$additional = $additional . "LEFT JOIN (SELECT DISTINCT roomid, seats, type FROM rooms RIGHT JOIN reservations ON rooms.roomid = reservations.roomnumber 
				WHERE startdate = $date AND (allowshare = '0' AND ((starttime > $starttime AND endtime <= $endtime) OR (endtime >= $endtime AND starttime < $endtime) 
				OR(starttime < $starttime AND endtime <= $endtime)))) AS subquery ON rooms.roomid = subquery.roomid WHERE subquery.roomid IS NULL AND ";
*/


		} else {
			$additional = "WHERE ";
		}
		if($type != null){
			$additional = $additional . "rooms.type = '$type' AND ";
		}
		if($headcount != null){
		//	$additional = $additional . "
		}


		$additional = $additional . '1';
	}



	// show favorites 
	/*
	$sql = "SELECT * FROM favorites WHERE email='" . $_SESSION['username'] . "' ORDER BY roomid";
    $result = $conn->query($sql);
	$i = 0;
	$headerPrinted = false;
	while ($row = $result->fetch_assoc()) 
	{
		if ($headerPrinted == false){
			echo "<tr style='height: 7%;'><th style='font-size: 3vmin;'>Favorites</th></tr>";
			$headerPrinted = true;
		}
		$imgName = "images/fav-unselect.png";
		if($i == 0)
		{
			echo "<tr>";
		}
		if($i == 6)
		{
			$sql = "SELECT * FROM favorites WHERE roomid='" . $row['roomid'] . "' AND email='" . $_SESSION['username'] . "'";
			$result2 = $conn->query($sql);

			while ($row2 = $result2->fetch_assoc()) {
				// there is a favorite.
				$imgName = "images/fav-select.png";
			}
			echo "<td class = 'room' id = '".$row['roomid']."'><img src='" . $imgName . "' onclick='favoriteClicked(this.parentElement);' class='favoriteIcon'><font id = '".$row['roomid']."' onclick='selectRoom(this.id)' class='roomText'><b>" . $row['roomid'] ."</b><br></font></td>";
			echo "</tr>";
			$i = 0;
		}
		
		else
		{	
			$sql = "SELECT * FROM favorites WHERE roomid='" . $row['roomid'] . "' AND email='" . $_SESSION['username'] . "'";
			$result2 = $conn->query($sql);

			while ($row2 = $result2->fetch_assoc()) {
				// there is a favorite.
				$imgName = "images/fav-select.png";
			}
			echo "<td class = 'room' id = '".$row['roomid']."'><img src='" . $imgName . "' onclick='favoriteClicked(this.parentElement);' class='favoriteIcon'><font id = '".$row['roomid']."' onclick='selectRoom(this.id)' class='roomText'><b>" . $row['roomid'] ."</b><br></font></td>";
			$i++;
		}
		

		
	}
	*/
	
	$sql = $sql . $additional . " ORDER BY rooms.roomid";
	// echo $sql; 
    $result = $conn->query($sql);
	$i = 0;


	$firstPrinted = false;
	
	echo "<tr style='height: 7%;'><th style='font-size: 3vmin;'>All Rooms</th></tr>";
	while ($row = $result->fetch_assoc()) 
	{

		$imgName = "images/fav-unselect.png";
		if($i == 0)
		{
			echo "<tr>";
		}
		if($i == 4)
		{
			$sql = "SELECT * FROM favorites WHERE roomid='" . $row['roomid'] . "' AND email='" . $_SESSION['username'] . "'";
			$result2 = $conn->query($sql);

			while ($row2 = $result2->fetch_assoc()) {
				// there is a favorite.
				$imgName = "images/fav-select.png";
			}
			echo "<td class = 'room' id = '".$row['roomid']."'><img src='" . $imgName . "' onclick='favoriteClicked(this.parentElement);' class='favoriteIcon'><font id = '".$row['roomid']."' onclick='selectRoom(this.id)' class='roomText'><b>" . $row['roomid'] ."</b><br><br>". $row['seats'] ."<br>" . $row['type'] . "</font></td>";
			echo "</tr>";
			$i = 0;
		}
		
		else
		{	
			$sql = "SELECT * FROM favorites WHERE roomid='" . $row['roomid'] . "' AND email='" . $_SESSION['username'] . "'";
			$result2 = $conn->query($sql);

			while ($row2 = $result2->fetch_assoc()) {
				// there is a favorite.
				$imgName = "images/fav-select.png";
			}
			echo "<td class = 'room' id = '".$row['roomid']."'><img src='" . $imgName . "' onclick='favoriteClicked(this.parentElement);' class='favoriteIcon'><font id = '".$row['roomid']."' onclick='selectRoom(this.id)' class='roomText'><b>" . $row['roomid'] ."</b><br><br>". $row['seats'] ."<br>" . $row['type'] . "</font></td>";
			$i++;
		}
		

		
	}
	if($result->num_rows == 0){
		echo "<h4> No Results </h4>";
	}

$conn->close();



//*************************************************************************************
//This function checks a number of requirements on a new reservation being made.
//If the reservation being made is before or after the start day and end day time
//it returns FALSE and the correct error message to the user. If the reservation 
//made is not on a fifteen minute interval, then it returns FALSE and the correct 
//message is displayed to the user. If the reservation start time occurs after the
//end time then return FALSE and display correct message to the user. If the reser-
//vation being made is good, the correct message is displayed and returnsVal = TRUE.
//************************************************************************************
function checkDateTime($outputError, $startToCheck, $endToCheck)
{
	//msg variables to indicate the problem that occurred 
	$returnVal = FALSE;

	$dayStart = DateTime::createFromFormat('H:i', '7:00');
	$dayEnd  = DateTime::createFromFormat('H:i', '23:00');
	// use the dayStart and dayEnd times
	$startToCheck = DateTime::createFromFormat('H:i', $startToCheck);
	$endToCheck = DateTime::createFromFormat('H:i', $endToCheck);
	$startDayErrMsg = "Your reservation cannot be made before 7 AM!";
	$endDayErrMsg = "Your reservation cannot be made after 10 PM!";
	$minuteErrMsg = "Your reservation must be made on 15 minute increments!";
	$startTimeErrMsg = "Your reservation start time is occurring after your end time!";

	

	//returns false if reservation made is before the valid start day time
	if($startToCheck < $dayStart)
	{
		$retValue = FALSE;
		if($outputError)
		{
			echo $startDayErrMsg;
		}

	}
	//returns false if reservation made is after the valid end day time
	else if($endToCheck > $dayEnd)
	{
		
	//	echo $endToCheck->format('H:i') . " is greater than " . $dayEnd->format('H:i');
		$retValue = FALSE;
		if($outputError)
		{
			echo $endDayErrMsg;
		}
	}
	//returns false if reservation made has a minute value that is not on the required fifteen minute interval
	/* Might need to be removed later, depending on how time is handled.
	else if($startToCheck%15 != 0)
	{
		$retValue = FALSE;
		if($outputError)
		{
			echo $startToCheck%15;
			echo $minuteErrMsg;
		}
	} */
	//returns false if reservation made has a start time that is after the end time
	else if($startToCheck > $endToCheck)
	{
		$retValue = FALSE;
		if($outputError)
		{
			echo $startTimeErrMsg;
		}
	}
	//returns true if reservation made has no conflicting reservations times already made
	else
	{
		$retValue = TRUE;
	}
	
	
	return $retValue;
}
?>