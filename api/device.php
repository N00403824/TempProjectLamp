<?
// Brendan M. Lowe
 require_once ("../inc/climate.php");

 //No Session is created for this page because the Remote Pi's will be 'phoning home' to input.php to upload data 

 switch($_SERVER['REQUEST_METHOD'])
 {

 	case 'POST':
		


		// Send output back to the python script/web browser
		echo json_encode($device_output);

		break;	


	default: //GET - Index & Details
		
		if($_GET['device_list']==TRUE){
			
			$device_output = getDevices();
			
			
			$device_info = array();
			
			while($device = $device_output['info']->fetch_assoc()) {
				
				array_push($device_info, $device);
			}
			
			$device_output['info'] = $device_info;
			
		}
		
		// Send output back to the python script/web browser
		echo json_encode($device_output);
 }



?>