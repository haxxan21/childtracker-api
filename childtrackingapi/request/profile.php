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

if($parameters[0] == 'profile' && count($_POST) > 0):
	$id = $_POST['id'];
	$name = isset($_POST['name']) ? $_POST['name'] : 0;
	$image = isset($_POST['image']) ? $_POST['image'] : 0;


	if($image !== 0 && strlen($name) > 2){
	$data = $database->update("users_data" , [
		"image" => $image,
	] , [
		"uid" => $id,
	]);
	$datas = $database->update("users", [
	"name" => $name,
], [
	"id" => $id,
]);

$output['id'] =  $id;
$output['name'] =  $name;
$output['image'] = $image;
$output['success'] = "name and image updated successfully";
$output['error'] = 0;
echo json_encode($output);
exit;
}

if($image == 0){
	if(strlen($name) < 3){
		$error = true;
			$output['error_message'] ="Please write your name correctly, minimum length is 4.";
			$output['error'] = 1;
			echo json_encode($output);
}
else{
	$datas = $database->update("users", [
	"name" => $name,
], [
	"id" => $id,
]);

$output['id'] =  $id;
$output['name'] =  $name;
$output['success'] = "name updated successfully";
$output['error'] = 0;
echo json_encode($output);
}
exit;
}

if($name == 0){
	$datas = $database->update("users_data", [
	"image" => $image,
], [
	"uid" => $id,
]);
$output['id'] =  $id;
$output['image'] =  $image;
$output['success'] = "image updated successfully";
$output['error'] = 0;
echo json_encode($output);
exit;
}

	exit;
endif;
