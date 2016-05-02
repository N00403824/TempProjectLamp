<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Climate Information</title>
				<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">

		<!-- Latest compiled and minified JavaScript -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>

	</head>
	<body>

	
		<div class="container" name="main">
			<div class="row">
				<div class="col-xs-1"></div>
				<div class="col-xs-10">
					<table class="table table-hover table-striped table-width">
						<caption id='tablecaption'><h4>Device List</h4></caption>
		    			<thead>
		    				<tr>
		    					<th class="col-xs-2">Data</th>
		    					<th class="col-xs-3">Time </th>
		    					<th class="col-xs-2">TempC</th>
		    					<th class="col-xs-4">TempF</th>
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


			function getUrlVars(url) {
				    var hash;
				    var myJson = {};
				    var hashes = url.slice(url.indexOf('?') + 1).split('&');
				    for (var i = 0; i < hashes.length; i++) {
				        hash = hashes[i].split('=');
				        myJson[hash[0]] = hash[1];
				    }
				    return myJson;
			}


			function climate_info(){
				
				var location = getUrlVars( window.location.search.substring(1) );
				console.log(location);
				
				
				var climate_params = "../api/climate.php?climate=true&building=" + location.building +"&room=" + location.room;
				
				if(location.startTime != null){
					
					climate_params = climate_params +"&startTime=" + location.startTime;
				}
				
				if(location.stopTime != null){
					
					climate_params = climate_params +"&stopTime=" + location.stopTime;
				}
				
				$.getJSON( climate_params, function(climate_data){
				
					//console.log(climate_data.info);
					
					$("#tablecaption").empty().append(climate_data.results)
					
					$.each(climate_data.info, function(key, value) {

					
							var table_row = "<tr><td><a class='btn btn-primary btn-xs new'>Climate Data</a></td><td>"+
												value.time+
												"</td><td>"+
												value.tempC+
												"</td><td>"+
												value.tempF+
												"</td><td><a class='btn btn-default btn-sm reset' role='button' data-toggle='modal' data-target='#myModal'"+
												"href=''>Device</a></td></tr>";
							
						$("tbody").append( table_row );
				
					});
					
				});
			}
			
			climate_info();

 
			//$('body').on('hidden.bs.modal', '.modal', function () {
			//	$(this).removeData('bs.modal');
			//	$('tbody').empty();
			//	dmca_info();
			//});
		</script>
	</body>
</html>