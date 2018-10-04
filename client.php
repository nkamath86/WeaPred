<!-- WeaPred Client -->
<html>
<head>

	<title> WeaPred </title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <style>
    
    .center_div{
    	margin: 20% auto;
    	width:80%
    }

    body, html {
    height: 100%;
	}

	.bg { 
	    background: linear-gradient(rgba(0,0,0,.7), rgba(0,0,0,.7)), url("gates_of_the_valley_yosemite_valley.jpg");
		height: 50%; 
		background-position: center;
	    background-repeat: no-repeat;
	    background-size: cover;
	    color: #fff;
	    
	}
	input{
		color: black;
	}

    </style>

</head>

<body class="bg"> 

	<div class="container center_div">

		<div class = "col-mid-4 col-md-offset-4">

			<form action="server.php" method="post">

			  <div class="form-group">

			    <label for="str">Start:</label>
			    <input type="text" class="input-group col-sm-4" name="str" placeholder="Enter starting point">

			  </div>

			  <div class="form-group">

			    <label for="des">Destination:</label>
			    <input type="text" class="input-group col-sm-4" name="des" placeholder="Enter destination">

			  </div>

			  <input type="submit" class="btn btn-danger">

			</form>

		</div>

	</div>

</body>

</html>
