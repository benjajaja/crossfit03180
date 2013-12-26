<?php

include('../config.php');

/*function __autoload($class) {
	return require('routes/' . $class . '.php');
}*/

require_once(__DIR__ . '/zaphpa/zaphpa.lib.php');

$router = new Zaphpa_Router();

$addRoute = function($path, $class, $method) use ($router) {
	$route = array(
		'path' => '/api' . $path,
		'file' => 'routes/' . $class . '.php'
	);
	$route[$method] = array($class, $method);
	$router->addRoute($route);
};

$addRoute('/auth', 'Auth', 'get');


try {
  $router->route();
} catch (Zaphpa_InvalidPathException $ex) {      
  header("Content-Type: application/json;", TRUE, 404);
  $out = array("error" => "not found");        
  die(json_encode($out));
}

?>