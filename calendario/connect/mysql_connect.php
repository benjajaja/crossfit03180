<?php
	$config = require('../../config.php');

	$db = mysql_connect($config['db']['host'], $config['db']['user'], $config['db']['password']) or die('Ha fallado la conexión: '.mysql_error());
	mysql_select_db($config['db']['database'], $db)or die ('Error al seleccionar la BD: '.mysql_error());
	mysql_query ("SET NAMES 'utf8'");
?>

