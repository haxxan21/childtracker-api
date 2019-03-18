<?php require_once('lib/Medoo.php');
//error_reporting(0);
//error_reporting(0);

$url = 'http://localhost/childtracking/childtrackingapi/';

use Medoo\Medoo;

$database = new Medoo([
	// required
	'database_type' => 'mysql',
	'database_name' => 'os_childtracking',
	'server' => 'localhost',
	'username' => 'root',
	'password' => '',
	"logging" => true,
	// [optional]
	'charset' => 'utf8',
	'port' => 3306,

	// [optional] Table prefix
	'prefix' => '',

	// [optional] Enable logging (Logging is disabled by default for better performance)
	'logging' => true,

	// [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
	'option' => [
		PDO::ATTR_CASE => PDO::CASE_NATURAL
	],

	// [optional] Medoo will execute those commands after connected to the database for initialization
	'command' => [
		'SET SQL_MODE=ANSI_QUOTES'
	]
]);

$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$folder = str_replace($url, '', $actual_link);
$folderArray = explode("/",$folder);
$parameters = array();
foreach($folderArray as $_folder){
	if(strlen($_folder) > 0)
		$parameters[] = strtolower($_folder);
}

if(@!is_file('request/'.$parameters[1].'.php')){
	$error = true;
	$output['error_message'] ="Invalid Request...!". '--request/'.$parameters[0].'.php';
	$output['error'] = 1;
	echo json_encode($output);
	exit;
}

function getUserIP()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}



require_once('request/'.$parameters[1].'.php');
