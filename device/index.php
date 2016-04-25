<?
// Brendan M Lowe
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Device List</title>
				<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">

		<!-- Latest compiled and minified JavaScript -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>


	</head>
	<body>

	
		<div class="container" name="main">
			<div class="row">
				<div class="col-xs-1"></div>
				<div class="col-xs-10">
					<table class="table table-hover table-striped table-width">
						<caption><h4>Device List</h4></caption>
		    			<thead>
		    				<tr>
		    					<th class="col-xs-2">Data</th>
		    					<th class="col-xs-3">Building </th>
		    					<th class="col-xs-2">Room</th>
		    					<th class="col-xs-4">IP Address</th>
		    					<th class="col-xs-1"></th>
		    				</tr>
		    			</thead>
		    			<tbody>
		    			</tbody>
		    		</table>
	    		</div>
		    	<div class="col-xs-1"></div>
			</div>
			
		<footer><p>Raspberry Pi Temp Sensors</p></footer>	
		</div>


		<!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="modal-dialog">
		        <div class="modal-content">
		        </div> <!-- /.modal-content -->
		    </div> <!-- /.modal-dialog -->
		</div> <!-- /.modal -->

		
		<script type="text/javascript">

			function device_info()
			{
				
				$.getJSON( "../api/device.php?device_list=true", function(device_data){
				
					console.log(device_data.info);
					
					$.each(device_data.info, function(key, value) {

					
							var table_row = "<tr><td><a class='btn btn-primary btn-xs new' href='../climate/?building=" + 
												value.building+		
												"&room="+
												value.room+
												"'>Climate Data</a></td><td>"+
												value.building+
												"</td><td>"+
												value.room+
												"</td><td><a target='_blank' href='http://"+
												value.ip_address+
												"'>"+
												value.ip_address+
												"</a></td><td><a class='btn btn-default btn-sm reset' role='button' data-toggle='modal' data-target='#myModal'"+
												"href=''>Device</a></td></tr>";
							
						$("tbody").append( table_row );
				
					});
					
				});
			}
			
			device_info();

 
			//$('body').on('hidden.bs.modal', '.modal', function () {
			//	$(this).removeData('bs.modal');
			//	$('tbody').empty();
			//	dmca_info();
			//});
		</script>
	</body>
</html>