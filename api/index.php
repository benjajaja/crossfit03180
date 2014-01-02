<?php

$config = require('../config.php');

require('adodb_lite/adodb.inc.php');
$db = &ADONewConnection('mysql');

if (!$db->Connect($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database'])) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service not available', true, 503);
	header('Content-Type: text/plain');
	die('service not available. please try again later');
}

$db->_salt = $config['db']['salt']; // sal para encriptar bien

$GLOBALS['db'] = $db; // esto es una mierda, las cosas no se deben meter variables globales NUNCA, pero me la suda pa esta web :D

require(__DIR__ . '/zaphpa/zaphpa.lib.php');
$router = new Zaphpa_Router();

// cookie monster
session_name('crossfit');
session_start();

$addRoute = function($path, $class, $methods, $handlers = null) use ($db, $router) {
	$route = array(
		'path' => '/api' . $path, // la ruta de la URL, ej. /api/auth
		'file' => 'routes/' . $class . '.php' // el fichero de la clase
	); 

	foreach($methods as $method) {
		$route[$method] = array($class, $method); // metodo de la clase, ej. post, get, etc.
	}

	$router->addRoute($route);
};

$addRoute('/auth', 'Auth', ['post', 'delete']);
/* ej.: $addRoute(
	'/calendar/week/{startDay}',
	'Week',
	['get'],
	array(
		'startDay' => Zaphpa_Template::regex('\d{4}-\d{2}-\d{2}')
	)
);*/


try {
  $router->route();
} catch (Zaphpa_InvalidPathException $ex) {      
  header("Content-Type: application/json;", TRUE, 404);
  die(json_encode(array("error" => "not found")));
  // cualquier otra ruta que no hemos definido: 404 not found
}

?>