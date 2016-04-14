<?
// Brendan M. Lowe
 require_once ("../inc/climate.php");

/*
session_start();

if(!isset($_SESSION["username"])){
	
	$connection['error'] = "Connection Error: No Current Session";
	echo json_encode($connection);
  	break;
}
 */

 switch($_SERVER['REQUEST_METHOD'])
 {

 	case 'POST':
		
		// Used to clear the Climate data of a single Pi.
		if($_POST['delete']==TRUE){
			
		
		}

		
		echo json_encode($climate_info);		
		
		break;	


	default: //GET - Index & Details

	
		/* Input Example:
		 * climate.php?climate=true&building=BLI&room=120&startTime=2016-04-10%2020%3A30&stopTime=2016-04-10%2120%3A45
		 * Results Example:
		 * {	"building":"BLI",
		 * 		"room":"120",
		 * 		"error":"",
		 *		"statement":"SELECT time, 
		 * 							temp AS tempC, 
		 * 							(temp*9/5 + 32) AS tempF
		 * 						FROM temp_info
		 *						JOIN device_info
		 *						ON temp_info.location_id=device_info.id
		 * 						WHERE device_info.building= 'BLI'
		 * 							AND device_info.room= '120'
		 * 							AND time >= '2016-04-10 20:30'
		 * 							AND time <= '2016-04-10!20:45'",
		 * 		"info":[	{"time":"2016-04-10 20:30:04","tempC":"20.375","tempF":"68.675"},
		 * 					{"time":"2016-04-10 20:40:04","tempC":"20.375","tempF":"68.675"}	],
		 * 		"results":"Climate Data for BLI 120 2016-04-10 20:30 between 2016-04-10!20:45"
		 * }
		 */	

		// Function used to obtain the climate data for a single Pi.
		if($_GET['climate']==TRUE){
			
			$climate = getData($_GET);
			
			$temp_info = array();
			
			while($temp = $climate['info']->fetch_assoc()) {
				
				array_push($temp_info, $temp);
			}
		}
		
		$climate['info'] = $temp_info;

		echo json_encode($climate);
 }



?>