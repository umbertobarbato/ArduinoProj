<!DOCTYPE html>
<!-- saved from url=(0054)https://getbootstrap.com/docs/4.1/examples/dashboard/# -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="https://getbootstrap.com/favicon.ico">

    <title>Dashboard Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="./bootstrap.min.css" rel="stylesheet">
    <link href="./new-Style.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./dashboard.css" rel="stylesheet">
  <style type="text/css">/* Chart.js */
@-webkit-keyframes chartjs-render-animation{from{opacity:0.99}to{opacity:1}}@keyframes chartjs-render-animation{from{opacity:0.99}to{opacity:1}}.chartjs-render-monitor{-webkit-animation:chartjs-render-animation 0.001s;animation:chartjs-render-animation 0.001s;}</style>

  </head>

  <body>
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
      <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="">Company name</a>
    </nav>

    <div class="container-fluid">
      <div class="row">


        <main role="main" class="main-yd">
         <div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
         		<div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
         				<div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
         		</div>
         		<div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
         			<div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
         		</div>
         	</div>
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Dashboard</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
              <div class="btn-group mr-2">
				  <button class="btn btn-sm btn-outline-secondary" onclick="location.href = '<?php
																			echo "/?";
																			error_reporting(E_ERROR | E_PARSE);
																			if(isset($_GET['data']))
																			{
																				echo "data=".  $_GET['data']. "&";
																			}
																			if(isset($_GET['session']))
																			{
																				if($_GET['session']==0)
																				{
																					echo "session=0";
																				}else{
																					echo "session=".($_GET['session']-1);
																				}
																			}else{
																				echo "session=0";
																			}
																		?>'"
				  ><b>&#8249;</b></button>
				  <button class="btn btn-sm btn-outline-secondary"><b>&#8250;</b></button>
              </div>

                            <input type="text" id="datepicker"/>
            </div>
          </div>

          <canvas class="my-4 w-100 chartjs-render-monitor" id="myChart" width="1004" height="423" style="display: block; width: 1004px; height: 423px;"></canvas>

		  <!--Titolo tabella-->
          <h2>Sommario eventi</h2>
          <div class="table-responsive">
            <table class="table table-striped table-sm">
              <thead>
                <tr>
					<th style="width:15%">Ora</th>
					<th></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="width:15%">1,001</td>
                  <td>Lorem</td>

                </tr>

                <!--Inserire codice php per la generazione della tabella -->
              </tbody>
            </table>
          </div>
        </main>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./jquery-3.3.1.slim.min.js.download" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="./popper.min.js.download"></script>
    <script src="./bootstrap.min.js.download"></script>

    <!-- Icons -->
    <script src="./feather.min.js.download"></script>
    <script>
      feather.replace()
    </script>

    <!-- Graphs -->
    <script src="./Chart.min.js.download"></script>
    <script>
      var ctx = document.getElementById("myChart");
      var myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
          datasets: [{
            data: [15339, 21345, 18483, 24003, 23489, 24092, 12034,25],
            lineTension: 0,
            backgroundColor: 'transparent',
            borderColor: '#007bff',
            borderWidth: 4,
            pointBackgroundColor: '#007bff'
          }]
        },
        options: {
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: false
              }
            }]
          },
          legend: {
            display: false,
          }
        }
      });
    </script>
  	<link rel="stylesheet" href="./jquery-ui-1.12.1.custom/jquery-ui-1.12.1.custom/jquery-ui.css"/>

	<script type="text/javascript" src="./jquery-ui-1.12.1.custom/jquery-ui-1.12.1.custom/external/jquery/jquery.js"></script>

	<script type="text/javascript" src="./jquery-ui-1.12.1.custom/jquery-ui-1.12.1.custom/jquery-ui.js"></script>
  	<script type="text/javascript">
		$("document").ready(function(){
			$("#datepicker").datepicker();

			<?php
				if(!isset($_GET['data']))
				{
					echo "$(\"#datepicker\").datepicker(\"setDate\",\"" . date("m/d/Y") . "\"); \n";
				}else{
					echo "$(\"#datepicker\").datepicker(\"setDate\",\"" . $_GET['data'] . "\"); \n";

				}

			?>
		});
		function cambiadata( dateText){
			if(dateText!="")//FARE CONFRONTO CON DATA!
			{
				location.href = '/?data=' + dateText ;
			}
		};
		$("#datepicker").datepicker({
			onClose: cambiadata
		});

	</script>


</body></html>
