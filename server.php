<!-- WeaPred Server -->
<?php
	
	$servername = "localhost";
	$username ="root";
	$password = "";
	$dbname = "WeaPred";

	// connecting to mysql db
	$conn = new mysqli($servername, $username, $password, $dbname);

	// check connection
	if($conn->connect_error)
	{
		die("could not connect to db: ".$conn->connect_error);
	}

	// start point
	$str = isset($_POST['str']) ? $_POST['str'] : "NULL";

	// destination
	$des = isset($_POST['des']) ? $_POST['des'] : "NULL";

	// Just noting down my API keys
	$GoogleAPIKey = "";
	$WeatherAPIKey = "";

	function get_latlon()
	{
		global $str, $des, $GoogleAPIKey, $WeatherAPIKey, $conn;

		$sql = "SELECT str, des FROM arr";
		$result = $conn->query($sql);

		$flag = 0;

		if($result->num_rows > 0)
		{
			while($row = $result->fetch_assoc())
			{
				if(($row["str"] == $str) && ($row["des"] == $des))
				{
					$flag = 1;
					break;
				}
			}
		}	

		if($flag==1)
		{
			$sql = "SELECT array FROM arr WHERE str = '$str' AND des = '$des';";
			$result = $conn->query($sql)  or die($conn->error);
			if($result->num_rows > 0)
			{
				while($row = $result->fetch_assoc())
				{
					$lmao = $row["array"];	
				}
			}
			else
			{
				echo "<br> Empty sql data <br>";
			}
			
			$conn->close();
			echo "\nusing sql data";
			$arr = json_decode($lmao);
			$flag = 0;
			weather_data($arr);
		}
		else
		{
			$url = 'https://maps.googleapis.com/maps/api/directions/xml?';
			$parameters = http_build_query(array("origin" => $str, "destination" =>  $des, "key" => $GoogleAPIKey));
			$curl = curl_init($url.$parameters);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$curl_response = curl_exec($curl);

			if ($curl_response === false) 
			{
			    $info = curl_getinfo($curl);
			    curl_close($curl);
			    die('error occured during curl exec. Additioanl info: ' . var_export($info));
			}

			curl_close($curl);
			$decoded = new SimpleXMLElement($curl_response);
			$steps = $decoded->route[0]->leg[0]->step;
			$max = sizeof($steps);
			$arr = array();
			for($i=0; $i < $max; $i++)
			{
				$arr[] = $steps[$i]->start_location;
			}

			$lmao = json_encode($arr);
			$sql = "INSERT INTO arr (str, des, array) VALUES ('$str', '$des', '$lmao');";
			if($conn->query($sql) == FALSE)
			{
				echo "Error: ".$sql."\t".$conn->error;
			}
			
			$conn->close();
			echo "\nusing new data";
			weather_data($arr);
		}
		
	}

	function weather_data($arr)
	{
		global $str, $des, $GoogleAPIKey, $WeatherAPIKey;
		echo '<tr><td>City</td><td>Temperature</td><td>Weather</td><td>Humidity</td>';
		$max = sizeof($arr);
		for($i=0; $i < $max; $i++)
		{
			$lat = $arr[$i]->lat;
			$lon = $arr[$i]->lng;
			$url = "api.openweathermap.org/data/2.5/weather?";
			$url = $url."lat=".$lat."&lon=".$lon."&";
			$parameters = http_build_query(array("units" => "imperial", "APPID" => $WeatherAPIKey));

			$curl = curl_init($url.$parameters);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$curl_response = curl_exec($curl);

			if ($curl_response === false) 
			{
			    $info = curl_getinfo($curl);
			    curl_close($curl);
			    die('error occured during curl exec. Additional info: ' . var_export($info));
			}

			curl_close($curl);
			$decoded = json_decode($curl_response, true);
			echo '<tr><td>'.$decoded["name"].'</td><td>'.$decoded["main"]["temp"].'</td><td>'.$decoded["weather"][0]["main"].'</td><td>'.$decoded["main"]["humidity"].'%</td></tr>';
		}
	}

?>

<html>

<head>
	<title> WeaPred </title>
	<style>
      #map {
        height: 80%;
      }
      
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>
	<div id="map"></div>
    <script>
            function initMap() {
	      var start;
	      var end;
		  var directionsService = new google.maps.DirectionsService();
		  var directionsDisplay = new google.maps.DirectionsRenderer();

		  var geocoder = new google.maps.Geocoder();
	      	
			// getting LatLon for start point
	      geocoder.geocode({'address': '<? echo $str; ?>'}, function(results, status){
	      		if(status == 'OK'){
	      			start = results[0].geometry.location;
	      			// alert("start: "+start);
	      		}
	      		else {
			        alert('Geocode was not successful for the following reason: ' + status);
		        }
	      	});
			
			// Calculate Route
		  var request = {
		    origin: '<? echo $str; ?>',
		    destination: '<? echo $des; ?>',
		    travelMode: 'DRIVING'
		  };

		  directionsService.route(request, function(result, status) {
		    if (status == 'OK') {
		      directionsDisplay.setDirections(result);
		    }
		    else {
			        alert('Not successful for the following reason: ' + status);
		        }
		  });

		}
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<? echo $GoogleAPIKey;?>&callback=initMap"
    async defer></script>

    <table>
    	<? get_latlon() ?>
    </table>
    <form action="client.php" method="post">
    	<input type="submit" class="btn btn-danger" value = "Go back">
    </form>
</body>

</html>
