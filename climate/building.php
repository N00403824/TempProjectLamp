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
					
					<canvas id='myCart' width="768" height="768">
						
						
						
						
						
					</canvas>
					
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
				//console.log(location);
				
				
				var climate_params = "../api/climate.php?climate=true&building=" + location.building +"&room=" + location.room;
				
				if(location.startTime != null){
					
					climate_params = climate_params +"&startTime=" + location.startTime;
				}
				
				if(location.stopTime != null){
					
					climate_params = climate_params +"&stopTime=" + location.stopTime;
				}
				
				$.getJSON( climate_params, function(climate_data){
					
					
					var option = {   
						//Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
					    //scaleBeginAtZero : true,
					
					    //Boolean - Whether grid lines are shown across the chart
					    scaleShowGridLines : true,
					
					    //String - Colour of the grid lines
					    scaleGridLineColor : "rgba(0, 0, 0, .05)",
					
					    //Number - Width of the grid lines
					    scaleGridLineWidth : 1,
					
					    //Boolean - Whether to show horizontal lines (except X axis)
					    scaleShowHorizontalLines: true,
					
					    //Boolean - Whether to show vertical lines (except Y axis)
					    scaleShowVerticalLines: true,
					
					    //Boolean - If there is a stroke on each bar
					    barShowStroke : true,
					
					    //Number - Pixel width of the bar stroke
					    barStrokeWidth : 2,
					
					    //Number - Spacing between each of the X value sets
					    barValueSpacing : 5,
					
					    //Number - Spacing between data sets within X values
					    barDatasetSpacing : 1
					};
					
					
					
					
					var times = [];
					var temperatures_indoor = [];
					
					
					//$("#tablecaption").empty().append(climate_data.results)
					
					$.each(climate_data.info, function(key, value) {

							times.push(value.time);
							temperatures_indoor.push(value.tempC);
				
					});
					
					console.log(times);
					console.log(temperatures_indoor);
					
					temperatures_outdoor = [28, 48, 40, 19, 86, 27, 90];
										
					var data = {
							    labels: times,
							    datasets: [
							        {
							            label: "My First dataset",
							            fillColor: "rgba(220,220,220,0.2)",
							            strokeColor: "rgba(220,220,220,1)",
							            pointColor: "rgba(220,220,220,1)",
							            pointStrokeColor: "#fff",
							            pointHighlightFill: "#fff",
							            pointHighlightStroke: "rgba(220,220,220,1)",
							            data: temperatures_indoor
							        },
							        {
							            label: "My Second dataset",
							            fillColor: "rgba(151,187,205,0.2)",
							            strokeColor: "rgba(151,187,205,1)",
							            pointColor: "rgba(151,187,205,1)",
							            pointStrokeColor: "#fff",
							            pointHighlightFill: "#fff",
							            pointHighlightStroke: "rgba(151,187,205,1)",
							            data: temperatures_outdoor
							        }
							    ]
							};
					
					
					var ctx = document.getElementById('myCart').getContext('2d');
			
					var LineCart = new Chart(ctx).Line(data, option);
					
					
					
					
					
					
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