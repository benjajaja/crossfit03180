<?php
	$db = mysql_connect("localhost", "root", "alahin66") or die('Ha fallado la conexión: '.mysql_error());
	mysql_select_db("calendario", $db)or die ('Error al seleccionar la BD: '.mysql_error());
	mysql_query ("SET NAMES 'utf8'");
?>

