<html lang="en">
<?php
if (isset($this->session->userdata['logged_in'])) {
$username = ($this->session->userdata['logged_in']['username']);
$email = ($this->session->userdata['logged_in']['email']);
} else {
header("location: login");
}
?>
<head>
	<title>Bebras-review</title>

	<!-- Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,300|Source+Sans+Pro|Open+Sans+Condensed:300|Raleway' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
	<link href="<?php echo base_url(); ?>assets/style/default/angular-toggle-switch.css" media="all" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url(); ?>assets/style/stylesheet.css" media="all" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url(); ?>assets/style/dropdown.css" media="all" rel="stylesheet" type="text/css">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
	
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
	
	<!-- Angular JS -->
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.2/angular.min.js"></script>  
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.2/angular-route.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.2/angular-sanitize.min.js"></script>

	<script src="<?php echo base_url(); ?>assets/angular-toggle-switch.js"></script>

	<!-- MY App -->
	<script src="<?php echo base_url(); ?>app/routes.js.php?v=11"></script>
	<script src="<?php echo base_url(); ?>app/packages/dirPagination.js?v=11"></script>
	<script src="<?php echo base_url(); ?>app/packages/directive.js?v=11"></script>
	

	<script src="<?php echo base_url(); ?>app/services/PresentationServices.js?v=11"></script>
	<script src="<?php echo base_url(); ?>app/services/TasksServices.js?v=11"></script>
	<script src="<?php echo base_url(); ?>app/services/ReviewsServices.js?v=11"></script>
	<script src="<?php echo base_url(); ?>app/services/UsersServices.js?v=11"></script>
	<script src="<?php echo base_url(); ?>app/services/ProfileServices.js?v=11"></script>
	<script src="<?php echo base_url(); ?>app/helper/myHelper.js?v=11"></script>

	<!-- App Controller -->
	<script src="<?php echo base_url(); ?>app/controllers/PresentationController.js?v=11"></script>
	<script src="<?php echo base_url(); ?>app/controllers/TasksController.js?v=11"></script>
	<script src="<?php echo base_url(); ?>app/controllers/ReviewsController.js?v=11"></script>
	<script src="<?php echo base_url(); ?>app/controllers/UsersController.js?v=11"></script>
	<script src="<?php echo base_url(); ?>app/controllers/ProfileController.js?v=11"></script>

	<style>
		table {
			font-size: 12px;
		}
		.navbar-default .navbar-nav>li>a:hover, .navbar-default .navbar-nav>li>a:focus
		{
		  background-color:#ddd;
		}

		.rating {
		    color: #a9a9a9;
		    margin: 0;
		    padding: 0;
		}
		ul.rating {
		    display: inline-block;
		}
		.rating li {
		    list-style-type: none;
		    display: inline-block;
		    padding: 1px;
		    text-align: center;
		    font-weight: bold;
		    cursor: pointer;
		    font-size:15px;
		}
		.rating .filled {
		    color: orangered;
		}

	</style>
</head>
<body ng-app="main-App">
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#/">Bebras-review</a>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li><a href="#/">Presentation</a></li>
					<li><a href="#/Tasks">Tasks</a></li>
					<li><a href="#/Reviews">Reviews</a></li>
					<li><a href="#/Users">Users</a></li>
					<li><a href="#/Profile">Profile</a></li>
				</ul>
				<b id="logout" style="float:right;padding:10px;"><a href="/user_auth/logout">Logout</a></b>
			</div>
		</div>
	</nav>

	<div class="container">
		<ng-view></ng-view>
	</div>

</body>
</html>
