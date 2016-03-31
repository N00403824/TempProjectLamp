<?
// Brendan M. Lowe
 require_once ("../inc/climate.php");

 //No Session is created for this page because the Remote Pi's will be 'phoning home' to input.php to upload data 

 switch($_SERVER['REQUEST_METHOD'])
 {

 	case 'POST':
		
		// A remote server will push new data to the server in JSON format. POST to this URL and POST JSON Data
		// Remote Server will POST the JSON to the input.php
		// The JSON will provide building, room, and location data as well an insert variable.
		// The info section of the JSON will provide an array of the climate data that will be input into the database.
		// ---------------------------------------------------------------------------------------------------------------
		// Input Example:
		// ---------------------------------------------------------------------------------------------------------------
		// {	"building": "HDH",
		//  	"room": "101A" 
		// 		"info": [   {"timestamp": "2016-03-22 22:45:10", "temperature": 21.0, "humidity": 85.12}, 
		//					{"timestamp": "2016-03-22 22:45:17", "temperature": 13.0, "humidity": 15.02}, 
		//					{"timestamp": "2016-03-22 23:53:17", "temperature": 45.2, "humidity": 100.0}	], 
		//		"coord_x": null, 
		//		"coord_y": null, 
		//		"coord_z": null, 
		//		"error": null, 
		//		"insert": true
		// }
		//
		// ---------------------------------------------------------------------------------------------------------------
		// Return Example 1:
		// ---------------------------------------------------------------------------------------------------------------
		// {	"results": [	"Climate Insertion Success HDH 101A 2016-03-22 22:45:10",
		//						"Climate Insertion Success HDH 101A 2016-03-22 22:45:17",
		//						"Climate Insertion Success HDH 101A 2016-03-22 23:53:17"	],
		//		"error":["","",""]
		// }
		//
		// ---------------------------------------------------------------------------------------------------------------
		// Return Example 2:
		// ---------------------------------------------------------------------------------------------------------------
		// {	"results": [	"Climate Insertion Error GAG 101A 2016-03-22 22:45:10",
		//						"Climate Insertion Error GAG 101A 2016-03-22 22:45:17",
		//						"Climate Insertion Error GAG 101A 2016-03-22 23:53:17"	],
		//		"error":   [	"Column 'location_id' cannot be null",
		//						"Column 'location_id' cannot be null",
		//						"Column 'location_id' cannot be null"	]
		// }
		// ---------------------------------------------------------------------------------------------------------------
		
		
		// Reads the POSTed JSON file from the input stream converts JSON into an array.
		$json_input = json_decode(file_get_contents("php://input"), TRUE);

		
		if($json_input['insert']==TRUE){
			
			$output_info['results'] = array();
			$output_info['error'] = array();
			
			// Confirm that there is data that needs to be entered before entering the for loop.
			if(count($json_input['info']) == 0){
				$output_info['results'] = "No input data";
			}
			
			foreach ($json_input['info'] as $key => $temp_info) {
					
					
				$temp_info['building'] = $json_input['building'];
				$temp_info['room'] = $json_input['room'];
				
				$climate_results = insertData($temp_info);

				array_push($output_info['results'], $climate_results['results']);
				array_push($output_info['error'], $climate_results['error']);
			}
		}
		else{
			
			$output_info['error'] = "There was an input error";
			$output_info['results'] = "Input Problem";
		}

		// Send output back to the python script
		echo json_encode($output_info);		
		
		break;	


	default: //GET - Index & Details

		// Provides the datetime a remote server last updated the Lamp
		// GET url for on the remote server will also provide building and room information
		// The remote server should query this URL so that it provides JSON back that is limited to only new entires
		// Example: input.php?lastData=true$building=BLI&room=425
		// Returns: JSON Data
		// If there is no building and room in the database
		// {"info":{"lastData":null},"error":""}
		// If there is a building and room in the database
		// {"info":{"lastData":"2016-03-21 11:22:09"},"error":""}
		
		if($_GET['lastData']==TRUE){
			
			$device_info = getLastData($_GET);
			
			$output_info['info'] = $device_info['info']->fetch_assoc();
			$output_info['results'] = $device_info['results'];
			$output_info['error'] = $device_info['error'];
		}
		
		// Send output back to the python script/web browser
		echo json_encode($output_info);
 }



?>