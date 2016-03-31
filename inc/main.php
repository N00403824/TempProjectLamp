<?
// Brendan M. Lowe

 // Must be created and edited for each LAMP instance
 require_once"/home/loweb/password.php";
 /* Information in password.php
  * -----------------------------
  *  $db_server;
  *  $db_name;
  *  $db_username;
  *  $db_password;
  * -----------------------------
  *  $climate_db = "climate_info";
  *  $user_db = "user_info";
  *  $device_db = "device_info";
  */



// General function used for database connections
function sql_dmca(){

 global $db_server;
 global $db_name;
 global $db_username;
 global $db_password;
 

 $connection = new mysqli($db_server, $db_username, $db_password, $db_name);
 
 if ($connection->connect_errno){
 	
	 $some_error['error'] = $connection->connect_errno;
	 return $some_error;
 }

 return $connection;
}


// Function for treating array to remove escape characters for MySQL.
function arrayEscape($array_to_be_treated, $database_connection){

 $arr = array();
 foreach ($array_to_be_treated as $key => $value)
 {
	$arr[$key] = $database_connection->real_escape_string($value);
 }
       
 return $arr;
}
 
// Treats an array for use in HMTL
function html_entities($array_to_be_treated)
{
	$arr = array();
	foreach ($array_to_be_treated as $key => $value)
	{
		$arr[$key] = htmlentities($value);
	}
        
	return $arr;

}
 
 
?>