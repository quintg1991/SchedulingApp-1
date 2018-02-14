<?php session_start();
    //$_SESSION['logged_in_useremail'];
    //$roomnumber = $_POST['roomnumber'];
    //$day = $_POST['day'];
    //$month = $_POST['month'];
?>

<!DOCTYPE html>
<html>
<head>

<link rel="stylesheet" type="text/css" href="styles.css">

<title>UNA Scheduling app</title>
</head>
<body>

<div class="dayview">

</div>

<div class="makeReservation">
  <div class="test">
  <h1>Make Reservation</h1>
  <form method="POST" action="/scripts/PHP/CreateReservation.php">
    Reserving email*:
    <input type="text" name="owneremail"><br><p/>

    <p>Duration*:</p>
    &nbsp;&nbsp;Start time:
    <input type="text" name="startHour" style="width: 48px">
    <input type="text" name="startMinute" style="width: 48px">
    <select>
    <option value="startAM">AM</option>
    <option value="startPM">PM</option>
    <input id="date" type="date" name="date" placeholder="2018/01/26" required/><br>
  </select>

  &nbsp;&nbsp;End time:&nbsp; 
    <input type="text" name="endHour" style="width: 48px">
    <input type="text" name="endMinute" style="width: 48px">
    <select>
    <option value="endAM">AM</option>
    <option value="endPM">PM</option>
    </select>
    <input id="date" type="date" name="date" placeholder="2018/01/26" required/><br><p/>

    Recurring:
    <select>
    <option value="Once">Just Once</option>
    <option value="Weekly">Weekly</option>
    <option value="Monthly">Monthly</option>
  </select><p/><br>

    <input type="checkbox" name="allowshare" value="true">Allow room sharing<br><p/>

    Expected number of seats needed:
    <input type="text" name="numberOfSeats" style="width: 48px"><br><p/>

    <!-- add css for this boi -->
    Comments<br>
    <textarea rows="20" cols="60" name="comment">
    </textarea><br><p/>

    <button type="submit" value="Submit">Make reservation</button>
  </form>
</div>
</div>

<div class="calendar">
  <table style="width:100%">
    <tr>
      <th style="width:10%">Time</th>
      <th style="width:30%"></th>
      <th style="width:30%">Bookings</th>
      <th style="width:30%"></th>
    </tr>
    <tr>
      <td>7:00AM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>8:00AM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>9:00AM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>10:00AM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>11:00AM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>12:00PM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>1:00PM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>2:00PM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>3:00PM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>4:00PM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>5:00PM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>6:00PM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>7:00PM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>8:00PM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>9:00PM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>10:00PM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>11:00PM</td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
  </table>
</div>
</body>
</html>
