<?
// Brendan M. Lowe
 require_once("../inc/main.php");
 
 
//Function for adding a new device
function addDevice($device){

 global $device_db;

 //Open MySQL Connection inorder to get last data point for a device.
 $database = sql_dmca();

 $device = arrayEscape($device, $database);

 $device_info['statement'] = "INSERT INTO $device_db
 								( 	building,
									room,
									coord_x,
									coord_y,
									coord_z,
									ip_address,
									password	)
								VALUES
								(	'$device[building]',
									'$device[room]',
									'$device[coord_x]',
									'$device[coord_y]',
									'$device[coord_z]',
									'$device[ip_address]',
									'$device[password]'	)";								

 $device_info['info'] = $database->query($device_info['statement']);
 $device_info['error'] = $database->error;
 if($device_info['error']){
 		
 	$device_info['results'] = "Device Insertion Error";
 }
 else{
	$device_info['results'] = "Device Insertion Success";
 }

 $database->close();

 return $device_info;
	
}
 
function getDevice($device){
	
 global $device_db;

 //Open MySQL Connection inorder to get last data point for a device.
 $database = sql_dmca();

 $device = arrayEscape($device, $database);

 $device_info['statement'] = "SELECT * FROM $device_db
 								WHERE building = '$device[building]' AND room ='$device[room]'";								

 $device_info['info'] = $database->query($device_info['statement']);
 $device_info['error'] = $database->error;
 if($device_info['error']){
 		
 	$device_info['results'] = "Error for Device $device[building] $device[room]";
 }
 else{
	$device_info['results'] = "Device Information for $device[building] $device[room]";
 }

 $database->close();

 return $device_info;
}


function getDevices(){
	
 global $device_db;

 //Open MySQL Connection inorder to get last data point for a device.
 $database = sql_dmca();

 $device_info['statement'] = "SELECT * FROM $device_db";								

 $device_info['info'] = $database->query($device_info['statement']);
 $device_info['error'] = $database->error;
 if($device_info['error']){
 		
 	$device_info['results'] = "Error for all devices";
 }
 else{
	$device_info['results'] = "All Device Information";
 }

 $database->close();

 return $device_info;
}
 
 
 
 
 
?>