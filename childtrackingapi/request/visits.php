<?php
$output = array();
$error = false;
$_POST = $_GET;
unset($parameters[0]);

$parameters = array_values($parameters);

if(count($parameters) == 0){
	$error = true;
	$output['error_message'] ="Invalid Parameters...!";
	$output['error'] = 1;
	echo json_encode($output);
	exit;
}

if($parameters[0] == 'register' && count($_GET) > 0): //registering user
	$user_id = $_GET['user'];
	$lat = $_GET['lat']; //getting lattitude
	$long = $_GET['long']; //getting longitude
	$visit_time = date("Y-m-d H:i:s", $_GET['last_visit']);
	$token = $_GET['token'];

	$total = $database->count("places_visits", [
			"place_id"
		], [
			"visit_time" => $visit_time,
			"user_id" => $user_id
		]);
	if($total > 0){ //checking multiple entries
		$error = true;
		$output['error_message'] ="Duplicate Entry, skipped...!";
		$output['error'] = 1;
		echo json_encode($output);
		exit;
	}
	else{ //
		$total = $database->count("auth_tokens", [
				"token_id"
			], [
				"user_id" => $user_id,
				"expired"=>0,
				"token" => $token
			]);
		if($total == 0){
			$output['error_message'] = "Invalid Token..!";
			$output['error'] = 1;
			echo json_encode($output);
			exit;
		}

		$database->insert("places_visits", [
			"lat" => $lat,
			"user_id" => $user_id,
			'long' => $long,
			'visit_time' => $visit_time
		]);

		$output['id'] = $database->id();
		$output['status'] = 1;
		$output['message'] = 'Succesfully logged..!';
		$output['error'] = 0;
		echo json_encode($output);
		exit;
	}


endif; // end register




 // ----------------------------------------------------------------------------------

// if($parameters[1] == 'get' && count($_GET) > 0 ):


if($parameters[1] == 'get' && count($_REQUEST) > 0):
	$user_id = $_REQUEST['user'];
	$token = $_REQUEST['token'];
	$from_time = date("Y-m-d H:i:s", strtotime($_REQUEST['from_time']));
	$to_time = date("Y-m-d H:i:s", strtotime($_REQUEST['to_time']));

	$total = $database->count("auth_tokens", [  				"token_id"
			], [
				"user_id" => $user_id,
				"expired"=>0,
				"token" => $token
			]);
	if($total == 0){
		$output['error_message'] = "Invalid Token..!";
		$output['error'] = 1;
		echo json_encode($output);
		exit;
	}

	// if ($from_time > date("Y-m-d ", strtotime("-1 days"))) {
 //    	$output['error_message']="invalid time";
 //    	$output['error']=1;
 //    	echo json_encode($output);
 //    	exit;
 //    }



		$datas = $database->select("places_visits", [
			"place_id",
			"lat",
			"long",
			"visit_time",
			"user_id",
			"push_time",
		], [
			"visit_time[<>]" => [$from_time, $to_time],
			"user_id" => $user_id,
		]);

	//	var_dump( $database->log() );
if(!empty($datas)){
	$output['results']=$datas;
	$output['message'] = "success.";
	$output['success'] = 1;
	$output['error'] = 0;
	echo json_encode($output);
}
else{
	$output['error_message']="no data exists";
    	$output['error']=1;
    	echo json_encode($output);
}
//echo json_encode($datas);
endif;

// end-----------------------------------------------------------------
