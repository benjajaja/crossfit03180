<?php
	include '../connect/mysql_connect.php';

	$tipo = addslashes(htmlspecialchars($_POST["tipo"]));
	
	if($tipo=="insert_user"){
		$nombre = addslashes(htmlspecialchars($_POST["nombre"]));
		$apellidos = addslashes(htmlspecialchars($_POST["apellidos"]));
		$pass = addslashes(htmlspecialchars($_POST["pass"]));
		$email = addslashes(htmlspecialchars($_POST["email"]));
		$telefono = addslashes(htmlspecialchars($_POST["telefono"]));
		$dni = addslashes(htmlspecialchars($_POST["dni"]));
		insert_user($nombre,$apellidos,$pass,$email,$telefono,$dni);
	}

	/**********************
	***********************
	*******FUNCIONES*******
	***********************
	***********************
	**********************/

	function insert_user($nombre,$apellidos,$pass,$email,$telefono,$dni){
		$sql = mysql_query("INSERT INTO usuarios (nombre,apellidos,pass,email,telefono,dni) 
						VALUES ('$nombre','$apellidos','$pass','$email','$telefono','$dni')");
		if(!$sql){
			echo "<p style='color:red'>Charly, algo ha fallado al insertar el usuario...</p><p>
			INSERT INTO usuarios (nombre,apellidos,pass,email,telefono,dni) 
						VALUES ('$nombre','$apellidos','$pass','$email','$telefono','$dni')";
		}
		else{
			echo "<p style='color:blue'>Usuario insertado correctamente.</p>";
		}
	}
?>