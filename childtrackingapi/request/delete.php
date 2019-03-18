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

if($parameters[0] = 'delete' && count($_GET) > 0 ):
	 $id = $_GET['id'];
	 $data = $database->select('users' ,
	  [
			"parent",
		],
	[
		"id" => $id,
	]);
	if ($data[0]['parent'] == 0) {
		$output['error_message'] = "error! cannot delete parent";
 	 $output['error'] = 1;
 	 echo json_encode($output);
	}
	else{	 $delete = $database->delete('users' ,
	 ["AND"=>[
		 'id' => $id,
		 'parent[>]' => 0,
	 ],
	 ]);
	 $output['success'] = "Successfully deleted";
	 $output['error'] = 0;
	 echo json_encode($output);
 }
	 exit;
endif;
