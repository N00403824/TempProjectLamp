<?
// Brendan M. Lowe
 require_once("../inc/main.php");
 require_once("../inc/device.php");
 
 /* DB Table information from Loweb_db
  * -----------------------------------------------------------------
  * $climate_db = 'climate_info';
  * $user_db = 'user_info';
  * $device_db = 'device_info';
  * 
  * 
  * -----------------------------------------------------------------
  * 
  * Example of Join Statement
  * -----------------------------------------------------------------
  * SELECT Orders.OrderID, Customers.CustomerName, Orders.OrderDate
  * FROM Orders
  * INNER JOIN Customers
  * ON Orders.CustomerID=Customers.CustomerID
  * WHERE Customers.City='Lyon';
  * 
  * 
  * -----------------------------------------------------------------
  */

  
// Function for obtaining the last entry for a specific device in the climate table
// Function is used to limit the JSon data that is being provided by the remote devices.
// 
// If the foreign key lookup fails, the function creates a new entry in the device table.
// Example of $climate array:
// Array
// (
//    [timestamp] => 2016-03-22 23:53:17
//    [temperature] => 45.2
//    [humidity] => 100
//	  [building] => HDH
//    [room] => 101A
// )  
 
function getLastData($device){
	
 global $climate_db;
 global $device_db;

 //Open MySQL Connection inorder to get last data point for a device.
 $database = sql_dmca();

 $device = arrayEscape($device, $database);

 $device_info['statement'] = "SELECT MAX(time) AS lastData 
 								FROM $climate_db
 								INNER JOIN $device_db
 								ON $climate_db.location_id = $device_db.id
 								WHERE $device_db.building = '$device[building]'
									AND $device_db.room = '$device[room]'";


 $device_info['info'] = $database->query($device_info['statement']);
 $device_info['results'] = "Results for Last Data from Device: $device[building] $device[room]";
 $device_info['error'] = $database->error;

 $database->close();

 return $device_info;
}


// Function for inserting data into the climate table.
// Function does a look up of the foreign key using the Building and Room infomration that was provided by the JSON data
// If the foreign key lookup fails, the function creates a new entry in the device table.
// Example of $climate array:
// Array
// (
//    [timestamp] => 2016-03-22 23:53:17
//    [temperature] => 45.2
//    [humidity] => 100
//	  [building] => HDH
//    [room] => 101A
// )
function insertData($climate){
	
 global $climate_db;
 global $device_db;

 //Open MySQL Connection inorder to get last data point for a device.
 $database = sql_dmca();

 $climate = arrayEscape($climate, $database);
 
 $climate_results['error'] = "";
 
 // Check that the foreign key will be valid
 $device_check = getDevice($climate);
 
 // If the device doesn't already exist then create it in the device table
 if($device_check["info"]->num_rows == 0){
 			
 	$device_add_results = addDevice($climate);
	 
	if($device_add_results['error']){
 		
 		$climate_results['error'] .= $device_add_results['error'];
 	}
 }

 $climate_results['statement'] = "INSERT INTO $climate_db
 									(location_id, time, temp)
 									VALUES
 									( (SELECT id FROM $device_db WHERE building='$climate[building]' AND room = '$climate[room]'), 
 										'$climate[timestamp]', 
 										'$climate[temperature]')";


 $climate_results['info'] = $database->query($climate_results['statement']);
 $climate_results['error'] .= $database->error;
 if($climate_results['error']){
 		
 	$climate_results['results'] .= "Climate Insertion Error $climate[building] $climate[room] $climate[timestamp]";
 }
 else{
	$climate_results['results'] .= "Climate Insertion Success $climate[building] $climate[room] $climate[timestamp]";
 }
 

 $database->close();

 return $climate_results;
}
 
function getData($climate){

 global $climate_db;
 global $device_db;

 //Open MySQL Connection inorder to get last data point for a device.
 $database = sql_dmca();


 $climate = arrayEscape($climate, $database);
 
 $climate_results['building'] = $climate['building'];
 $climate_results['room'] = $climate['room'];
 
 $climate_results['error'] = "";

 // Statement Where there's a stop and start time
 
 if( $climate['startTime']  &&  $climate['stopTime'] ){
 	
	$climate_results['results'] .= "Climate Data for $climate[building] $climate[room] $climate[startTime] between $climate[stopTime]";

 	 $climate_results['statement'] = "SELECT time, temp AS tempC, (temp*9/5 + 32) AS tempF
										FROM $climate_db
										JOIN $device_db
											ON $climate_db.location_id=$device_db.id
										WHERE $device_db.building= '$climate[building]' 
											AND $device_db.room= '$climate[room]'
											AND time >= '$climate[startTime]'
											AND time <= '$climate[stopTime]'";
	
 }
    
 // Statement Where there's no stop time and a start time
 elseif( $climate['startTime'] && !$climate['stopTime']){
 	
	$climate_results['results'] .= "Climate Data for $climate[building] $climate[room] after $climate[startTime]";
 
 	$climate_results['statement'] = "SELECT time, temp AS tempC, (temp*9/5 + 32) AS tempF
										FROM $climate_db
										JOIN $device_db
											ON $climate_db.location_id=$device_db.id
										WHERE $device_db.building= '$climate[building]' 
											AND $device_db.room= '$climate[room]'
											AND time >= '$climate[startTime]'";    
 } 
 // Statement Where there's a stop time and no start time
 elseif(!$climate['startTime'] && $climate['stopTime']){
 	
	$climate_results['results'] .= "Climate Data for $climate[building] $climate[room] before $climate[stopTime]";
 	
 	$climate_results['statement'] = "SELECT time, temp AS tempC, (temp*9/5 + 32) AS tempF
										FROM $climate_db
										JOIN $device_db
											ON $climate_db.location_id=$device_db.id
										WHERE $device_db.building= '$climate[building]' 
											AND $device_db.room= '$climate[room]'
	 										AND time <= '$climate[stopTime]'";    
 }
 // Statement Where there's no stop and start time
 else{
 	
	$climate_results['results'] .= "Climate Data for $climate[building] $climate[room]";
 
 	$climate_results['statement'] = "SELECT time, temp AS tempC, (temp*9/5 + 32) AS tempF
										FROM $climate_db
										JOIN $device_db
											ON $climate_db.location_id=$device_db.id
										WHERE $device_db.building= '$climate[building]' 
											AND $device_db.room= '$climate[room]'";
 }

 $climate_results['info'] = $database->query($climate_results['statement']);
 
  $climate_results['error'] .= $database->error;
 if($climate_results['error']){
 		
 	$climate_results['results'] = "Climate Insertion Error $climate[building] $climate[room]";
 }
 

 $database->close();

 return $climate_results;

	
	
}
 
 
?>