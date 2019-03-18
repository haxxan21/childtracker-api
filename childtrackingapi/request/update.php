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

if($parameters[1] == 'password' && count($_GET) > 0 ):
  $email = $_GET['email'];
  $pass_new = $_GET['pass_new'];
  $pass_conf = $_GET['pass_conf'];

  if(strlen($pass_new) < 5){
		$error = true;
		$output['error_message'] ="Minimum cherecters for password is 5";
		$output['error'] = 1;
		echo json_encode($output);
		exit;
	}

  if ($pass_new != $pass_conf){
    $error = true;
  	$output['error_message'] ="Conflicting Passwords";
  	$output['error'] = 1;
  	echo json_encode($output);
  }
 else{
  $datas = $database->update("users", [
	"pass" => md5($pass_new),
], [
	"email" => $email,
]);
 $output['success'] = "Password successfully updated";
 $output['error'] = 0;
 echo json_encode($output);
}
  exit;
endif;


// $pass_old = md5($pass);
// $data = $database->select("users", [
//  "pass",
// ], [
//   "id" => $id,
// ]);
// var_dump($database->log());
// if ($pass_old != $data){
//   $error = true;
//   $output['error_message'] ="Invalid Password...!";
//   $output['error'] = 1;
//   echo json_encode($output);
// }
//
