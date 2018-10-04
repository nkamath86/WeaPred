<!-- WeaPred Server -->
<?php
	
	// start point
	$str = isset($_POST['str']) ? $_POST['str'] : "NULL";

	// destination
	$des = isset($_POST['des']) ? $_POST['des'] : "NULL";

	// Just noting down my API keys
	$GoogleAPIKey = "";
	$WeatherAPIKey = "";

	function get_latlon()
	{
		global $str, $des, $GoogleAPIKey, $WeatherAPIKey;
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
		weather_data($arr);
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
			    die('error occured during curl exec. Additioanl info: ' . var_export($info));
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
	      		}
	      		else {
			        alert('Geocode was not successful for the following reason: ' + status);
		        }
	      	});
            
		  var mapOptions = {
		    zoom:7,
		    center: start
		  }

		  var map = new google.maps.Map(document.getElementById('map'), mapOptions);
		  directionsDisplay.setMap(map);
          
          // calculate route
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
    	<!-- <? //weather_data(); ?> -->
    	<? get_latlon() ?>
    </table>

</body>

</html>