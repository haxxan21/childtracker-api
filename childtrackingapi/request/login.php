<?php

$output = array(); //initializing output array
$error = false;
//$_POST = $_GET;
unset($parameters[0]);

$parameters = array_values($parameters);

if(count($parameters) == 0){ // checking parameters
	$error = true;
	$output['error_message'] ="Invalid Parameters...!";
	$output['error'] = 1;
	echo json_encode($output);
	exit;
}



if($parameters[0] == 'auth' && count($_POST) > 0):
	$email = $_POST['email'];
	$pass = $_POST['pass'];
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ //validating email regex method
		$error = true;
		$output['error_message'] ="Please provide a valid email";
		$output['error'] = 1;
		echo json_encode($output);
		exit;
	}
	if(!strlen($pass)){ //password length matching
		$error = true;
		$output['error_message'] ="Empty Password not allowed.";
		$output['error'] = 1;
		echo json_encode($output);
		exit;
	}
	$total = $database->count("users", [
			"id"
		], [
			"email" => $email,
			"pass" => md5($pass)
		]);
	if($total > 0){ // check existing users
		$datas = $database->select("users", [
			"id",
			"name",
			"email",
			"parent",
		], [
			"email" => $email,
			"pass" => md5($pass)
		]);

		$total = $database->count("auth_tokens", [
				"token_id"
			], [
				"user_id" => $datas[0]['id'],
				"expired"=>0 // running session
			]);
		if($total > 0){
			$output['notice'] = "All other open sessions are closed for this account.";
			$database->update("auth_tokens", ["expired" => 1], ["user_id" => $datas[0]['id'], "expired"=>0]);
		}
		$token = md5(time()); //32-bit unique hexadecimal code generator
		$database->insert("auth_tokens", [
			"token" => $token,
			"user_id" => $datas[0]['id'],
		]);
		//=========================================
		$usersdata = $database->select("users_data", [
			"mobile",
			"gender",
			"image"
		], [
			"uid" => $datas[0]['id']
		]);
		$output['id'] = $datas[0]['id'];
		$output['parent'] = $datas[0]['parent'];
		$output['name'] = $datas[0]['name'];
		$output['email'] = $datas[0]['email'];
		$output['mobile'] = count($usersdata) > 0 ? $usersdata[0]['mobile'] : '';
		$output['gender'] = count($usersdata) > 0 ? $usersdata[0]['gender'] : '';
		$output['image'] = count($usersdata) > 0 ? $usersdata[0]['image'] : '';
		$output['token'] = $token;
		$output['valid_till'] = date("Y-m-d H:i:s", strtotime("+7 days")); // valid for 7 days for later use
		// from now...!
		$output['error'] = 0;
		echo json_encode($output);
		exit;
	}
	else{
		$error = true;
//		var_dump( $database->log() );

		$output['error_message'] ="Invalid Login...!";
		$output['error'] = 1;
		echo json_encode($output);
		exit;
	}


endif; // end auth

if($parameters[0] == 'register' && count($_POST) > 0):

	$email = $_POST['email'];
	$pass = $_POST['pass'];
	$parent = $_POST['parent']; // 0 for parent, id for children.
	$userIp =  getUserIP();
	$name = $_POST['name'];
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$error = true;
		$output['error_message'] ="Please provide a valid email";
		$output['error'] = 1;
		echo json_encode($output);
		exit;
	}
	if(strlen($pass) < 5){
		$error = true;
		$output['error_message'] ="Minimum cherecters for password is 5";
		$output['error'] = 1;
		echo json_encode($output);
		exit;
	}
	if(!strlen($parent)){ //checking parent variable
		$error = true;
		$output['error_message'] ="I am confused, is this registration is for a parent or child?";
		$output['error'] = 1;
		echo json_encode($output);
		exit;
	}
	if(strlen($name) < 4){
		$error = true;
		$output['error_message'] ="Please write your name correctly, minimum length is 4.";
		$output['error'] = 1;
		echo json_encode($output);
		exit;
	}
	$total = $database->count("users", [
			"id"
		], [
			"email" => $email
		]);
	if($total > 0){ //checking if the user already exists
		$error = true;
		$output['error_message'] ="You already have an account with the given email address.";
		$output['error'] = 1;
		echo json_encode($output);
		exit;
	}
	$mobile = $_POST['mobile'];
	$gender = $_POST['gender'];
	$image = $_POST['image'];
	$age = isset($_POST['age']) ? $_POST['age'] : 0;


	$database->insert("users", [
			"email" => $email,
			"pass" => md5($pass),
			"parent" => $parent,
			"name" => $name,
			"ip_registrar" => $userIp
		]);


	$uid =  $database->id();
	$database->insert("users_data", [
			"uid" => $uid,
			"mobile" => $mobile,
			"gender" => $gender,
			"image" => $image,
			"age" => $age
		]);


	$output['id'] =  $uid;
	$output['message'] = "Successfully registered.";
	$output['success'] = 1;
	$output['error'] = 0;
	echo json_encode($output);
	exit;

endif; // end register

if($parameters[0] == 'forget' && count($_POST) > 0):





endif; // end forget password


if($parameters[0] == 'childrens' && count($_REQUEST) > 0):
	$user_id = $_REQUEST['user'];
	$token = $_REQUEST['token'];

	$total = $database->count("auth_tokens", [
				"token_id"
			], [
				"user_id" => $user_id,
				"expired"=>0,
				"token" => $token
			]);
	if($total == 0){  //checking token existence
		$output['error_message'] = "Invalid Token..!";
		$output['error'] = 1;
		echo json_encode($output);
		exit;
	}

	$data = $database->select("users", [
		"[><]users_data" => ["users.id" => "uid"]
	],
	[
		"users.id",
		"users.name",
		"users.email",
		"users.parent",
		"users_data.mobile",
		"users_data.age",
		"users_data.image",
		"users_data.gender"
	],
	[
		"users.parent" => $user_id
	]
);

	if(count($data)){
		$output['results'] = $data;
		$output['error'] = 0;
		echo json_encode($output);
		exit;
	}

	$output['error_message'] = "Sorry, there are no childrens added.";
	$output['error'] = 1;
	echo json_encode($output);
	exit;


endif; // end childrens

	$output['error']=1; //checking parameters
	$output['error_message'] ="There is something wrong with your request. Please check if you had correctly used the parameters?";
	echo json_encode($output);
	exit;
