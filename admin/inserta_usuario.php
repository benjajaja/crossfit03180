<?php

	include '../connect/mysql_connect.php';



	$nombre = addslashes(htmlspecialchars($_POST["usuario"]));

	$pwd = addslashes(htmlspecialchars($_POST["pwd_1"]));

	$email = addslashes(htmlspecialchars($_POST["email"]));

	$bonos = addslashes(htmlspecialchars($_POST["bonos"]));



	$sql = mysql_query("INSERT INTO usuarios (nombre,pass,email,bonos) 

						VALUES ('$nombre','$pwd','$email','$bonos')");



	if(!$sql){

		echo "<p style='color:red'>Charly, algo ha fallado al insertar el usuario...</p>";

	}

	else{

		echo "<p style='color:blue'>Usuario insertado correctamente.</p>";

	}

?>