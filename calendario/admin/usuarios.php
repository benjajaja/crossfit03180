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
	else if($tipo=="select_users"){
		select_users();
	}

	/**********************
	***********************
	*******FUNCIONES*******
	***********************
	***********************
	**********************/

	function insert_user($nombre,$apellidos,$pass,$email,$telefono,$dni){
		$sql=mysql_query("SELECT id FROM usuarios WHERE dni='$dni'");

		if(!$row = mysql_fetch_array($sql)){//si no existe el dni
			$sql = mysql_query("INSERT INTO usuarios (nombre,apellidos,pass,email,telefono,dni) 
						VALUES ('$nombre','$apellidos','$pass','$email','$telefono','$dni')");
			if(!$sql){
				echo "<p style='color:red'>Charly, algo ha fallado al insertar el usuario...</p>";
			}
			else{
				echo "<p style='color:blue'>Usuario insertado</p>";
			}
		}
		else{
			echo "<p style='color:red'>Charly, ese DNI ya existe en la base de datos</p>";
		}
	}

	function select_users(){
		$sql=mysql_query("SELECT * FROM usuarios");
		
		$array[][]="";
		$i=0;
		while($file=mysql_fetch_array($sql)){
			$array[$i][0]=$file['id'];
			$array[$i][1]=$file['nombre'];
			$array[$i][2]=$file['apellidos'];
			$array[$i][3]=$file['pass'];
			$array[$i][4]=$file['email'];
			$array[$i][5]=$file['telefono'];
			$array[$i][6]=$file['dni'];
			$i++;
		}
		$result = json_encode($array);
		
		if($result == '[[""]]'){
			echo "0";
		}
		else{
			echo $result;
		}
	}
?>