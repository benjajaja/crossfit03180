<?php
	$db = mysql_connect("mysql51-97.perso", "zoneproybase", "Adminextremo") or die('Ha fallado la conexión: '.mysql_error());
	mysql_select_db("zoneproybase", $db)or die ('Error al seleccionar la BD: '.mysql_error());
	mysql_query ("SET NAMES 'utf8'");
?>

