<?php
	include '../connect/mysql_connect.php';

	$tipo = addslashes(htmlspecialchars($_POST["tipo"]));
	
	if($tipo==="insert_user"){
		$nombre = addslashes(htmlspecialchars($_POST["nombre"]));
		$apellidos = addslashes(htmlspecialchars($_POST["apellidos"]));
		$pass = addslashes($_POST["pass"]);
		$email = addslashes(htmlspecialchars($_POST["email"]));
		$telefono = addslashes(htmlspecialchars($_POST["telefono"]));
		$dni = addslashes(htmlspecialchars($_POST["dni"]));
		insert_user($nombre,$apellidos,$pass,$email,$telefono,$dni);
	}
	else if($tipo==="select_users"){
		$query = addslashes(htmlspecialchars($_POST["query"]));
		select_users($query);
	}
	else if($tipo==="delete_user"){
		$id = addslashes(htmlspecialchars($_POST["id"]));
		delete_user($id);
	}
	else if($tipo==="get_users"){
		$id_evento = addslashes(htmlspecialchars($_POST["id_evento"]));
		get_users($id_evento);
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
			$pass = $GLOBALS['config']['db']['salt'] . $pass;
			//echo "^".$GLOBALS['config']['db']['salt'] . "<".$pass.">";
			$sql = mysql_query("INSERT INTO usuarios (nombre,apellidos,pass,email,telefono,dni) 
						VALUES ('$nombre','$apellidos',UNHEX(SHA1('$pass')),'$email','$telefono','$dni')");
			if(!$sql){
				echo "1";
			}
			else{
				$array[][]="";
				$sql=mysql_query("SELECT * FROM usuarios WHERE id IN(SELECT MAX(id) AS id FROM usuarios)");//selecciona todo del ultimo id
				
				if($file=mysql_fetch_array($sql)){
					$array[0][0]=$file['id'];
					$array[0][1]=$file['nombre'];
					$array[0][2]=$file['apellidos'];
					$array[0][3]=$file['pass'];
					$array[0][4]=$file['email'];
					$array[0][5]=$file['telefono'];
					$array[0][6]=$file['dni'];
					$array[0][7]="<p style='color:blue'>Usuario insertado</p>";
					echo json_encode($array);
				}
				else{
					echo "1";
				}
			}
		}
		else{
			echo "0";
		}
	}

	function select_users($query){
		$sql=mysql_query("SELECT * FROM usuarios ".$query);

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
		
		if($result === '[[""]]'){
			echo "0";
		}
		else{
			echo $result;
		}
	}

	function get_users($id_evento){
		$sql=mysql_query("SELECT usuarios.nombre, usuarios.apellidos FROM usuarios, usuario_evento WHERE usuario_evento.id_evento='$id_evento' AND usuario_evento.id_usuario=usuarios.id");

		$array[][]="";
		$i=0;
		while($file=mysql_fetch_array($sql)){
			$array[$i][0]=$file['nombre'];
			$array[$i][1]=$file['apellidos'];
			$i++;
		}
		$result = json_encode($array);
		
		if($result === '[[""]]'){
			echo "0";
		}
		else{
			echo $result;
		}
	}

	function delete_user($id){
		$sql = mysql_query("DELETE FROM usuarios WHERE id=".$id);

		if(!$sql){
			echo "0";
		}
		else{
			echo select_users("");
		}
	}
?>