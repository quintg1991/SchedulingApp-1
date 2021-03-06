<?php session_start();
/******************************************************************************
Create Reservation Script

Description: Script that, when POST to, will pull variables from POST array
and use these to insert into the reservations table.
roomnumber, owneremail,numberOfSeats(as in headcount field),starthour, startminute,
endhour,endminute, allowshare, date, and occur (as in recurring enumerated type)
should be sent via POST.

Spring 2018
******************************************************************************/
/*
	if (!isset($_SESSION['username'])){
		//TODO redirect to login.
			$_SESSION['username'] = "admin@una.edu";
	}
*/

	if (!isset($_SESSION['username'])){
		header('location: ../../login.html');
	}

	processReservation();


//-------------------------------------------------------------------------------------------------------------
//Function to process the reservation into the database once reserve is clicked
//-------------------------------------------------------------------------------------------------------------
function processReservation()
{
	$logged_in_user = $_SESSION['username'];
		
	require "db_conf.php"; // set servername,username,password,and dbname


	$roomnumber = ($_POST['roomnumber']);

	//email that owns the reservation
	$owneremail = ($_POST['owneremail']);
	$owneremail = trim($owneremail);
	$owneremail = filter_var($owneremail, FILTER_SANITIZE_EMAIL);
	//$owneremail = filter_var($owneremail, FILTER_VALIDATE_EMAIL, array("options"=>array("regexp"=>"/^[a-zA-Z \.\-!,]{1,64}$/")));


	//checkbox type
	$allowshare=($_POST['allowshare']);


	//headcount
	$numberOfSeats = ($_POST['numberOfSeats']);
	$numberOfSeats = trim($numberOfSeats);
	$numberOfSeats = filter_var($numberOfSeats, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-9]{1,2}$/")));

	//Hour reservation starts
	$starthour = ($_POST['starthour']);
	$starthour = trim($starthour);
	$starthour = filter_var($starthour, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-9]{1,2}$/")));

	//minute reservation starts
	$startminute = ($_POST['startminute']);
	$startminute = trim($startminute);
	$startminute = filter_var($startminute, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-9]{1,2}$/")));

	//hour reservation ends
	$endhour = ($_POST['endhour']);
	$endhour = trim($endhour);
	$endhour = filter_var($endhour, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-9]{1,2}$/")));

	//minute reservation ends
	$endminute = ($_POST['endminute']);
	$endminute = trim($endminute);
	$endminute = filter_var($endminute, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-9]{1,2}$/")));

	//variable for end date of reservation
	$date = ($_POST['date']);

	//dropdown menu for how often the reservation should occur
	$occur = ($_POST['occur']);


	//comment variable
	$comment = ($_POST['comment']);
	$comment = trim($comment);
	//$comment = filter_var($comment, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z''-'\s]{1,250}$/")));
	//$comment = str_replace("'","\'", $comment);
        $comment = filter_var($comment, FILTER_SANITIZE_SPECIAL_CHARS);

	//We must validate the times and constraints given 
	require_once 'ValidateReservation.php'; // gain access to validation functions
	if($occur === "Once" || $occur === "null"){

		// first, we will check if numberOfSeats is set. If it was not, then they don't want to share, so we will just check for any
		// colliding reservationns with checkAnyCollision (coming from ValidateReservation.php). This function returns true if there
		// is any collision with a reservation at all. So we only allow the reservation to be made if this function returns false. We store
		// this return value in $collisionCheck and check it in the if statement that follows.
		$collisionCheck = false;
		if($numberOfSeats == null || $numberOfSeats == false){
			$collisionCheck = checkAnyCollision($starthour . ":" . $startminute, $endhour . ":" . $endminute, $date, $roomnumber);
		}


		if(checkValidTime_overload($starthour . ":" . $startminute, $endhour . ":" . $endminute, $date, $roomnumber) && !$collisionCheck){
		
			//connect to database
			$conn = new mysqli($servername, $username, $password, $dbname);

			//if connection to database fails, die
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

			//if connection is success, insert data into database and echo to user result
			//$sql = "INSERT INTO reservations (roomnumber, owneremail, allowshare, headcount, startdate, enddate, starttime, endtime, occur, comment, res_email) VALUES ('$roomnumber', '$owneremail', '$allowshare', '$numberOfSeats', '$date', '$date', '$starthour:$startminute', '$endhour:$endminute', '$occur', '$comment', '$logged_in_user')";
			$stmt = $conn->prepare("INSERT INTO reservations (roomnumber, owneremail, allowshare, headcount, startdate, enddate, starttime, endtime, occur, comment, res_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$startAssist = date('H:i', strtotime($starthour . ":" . $startminute));
			$endAssist = date('H:i', strtotime($endhour . ":" . $endminute));
			$stmt->bind_param("ssissssssss", $roomnumber, $owneremail, $allowshare, $numberOfSeats, $date, $date, $startAssist, $endAssist, $occur, $comment, $logged_in_user);
			$check = $stmt->execute();
			//$mResult = $stmt->get_result();
			
			if(isset($_POST['sendEmail'])){
				if ($check === TRUE && $_POST['sendEmail'] === "true") {
					include 'mail.php'; // uncomment when on deployed version
					
					sendMail();
				} 
			}
			if (!$check) {
				echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			else{
				echo "Reservation made successfully";
			}

		} else{
			echo "That time slot is not available.";
		}
	} else {
		if(isset($_POST['bulkConfirmed']) && $_POST['bulkConfirmed'] == 1){
			switch($occur){
				case "Weekly":
					$interval = new DateInterval('P1W');
					break;
				case "Monthly":
					$interval = new DateInterval('P1M');
					break;
			}
			
			
				//connect to database
			$conn = new mysqli($servername, $username, $password, $dbname);

			//if connection to database fails, die
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			//Used to determine success message at the end of the loop
			$success = FALSE;
			
			$begin = new DateTime($date);
			$end = new DateTime(date('Y-m-d', strtotime($date . '+ 6 months')));
			
			$daterange = new DatePeriod($begin, $interval, $end);
			$startAssist = date('H:i', strtotime($starthour . ":" . $startminute));
			$endAssist = date('H:i', strtotime($endhour . ":" . $endminute));
			$badDateArray = checkReservationRecurring($startAssist,$endAssist, $begin, $end, $roomnumber, $interval, $numberOfSeats);
			foreach ($daterange as $date) {
				//format the date
				$fDate = $date->format("Y-m-d"); 
				if(!in_array($date,$badDateArray)){
					//if connection is success, insert data into database and echo to user result
					//$sql = "INSERT INTO reservations (roomnumber, owneremail, allowshare, headcount, startdate, enddate, starttime, endtime, occur, comment, res_email) VALUES ('$roomnumber', '$owneremail', '$allowshare', '$numberOfSeats', '$date', '$date', '$starthour:$startminute', '$endhour:$endminute', '$occur', '$comment', '$logged_in_user')";
					$stmt = $conn->prepare("INSERT INTO reservations (roomnumber, owneremail, allowshare, headcount, startdate, enddate, starttime, endtime, occur, comment, res_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					$stmt->bind_param("ssissssssss", $roomnumber, $owneremail, $allowshare, $numberOfSeats, $fDate, $fDate, $startAssist, $endAssist, $occur, $comment, $logged_in_user);
					$check = $stmt->execute();
					//$mResult = $stmt->get_result();
					
					if (!$check) {
						echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
					}
					else{
						$success = TRUE;
					}
				}
			}
			if ($success === TRUE){
				echo "Reservation(s) made successfully";
				//include 'mail.php'; uncomment when on deployed version
			}
		}
		$conn->close();
			
	}
	
	
}

?>
