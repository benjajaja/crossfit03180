<?php
	include '../connect/mysql_connect.php';

	$tipo = addslashes(htmlspecialchars($_POST["tipo"]));
	$id_evento = addslashes(htmlspecialchars($_POST["idEvento"]));
	$mi_id = addslashes(htmlspecialchars($_POST["mi_id"]));
	$x_post = addslashes(htmlspecialchars($_POST["x_post"]));
	$y_post = addslashes(htmlspecialchars($_POST["y_post"]));
	$x_ant = addslashes(htmlspecialchars($_POST["x_ant"]));
	$y_ant = addslashes(htmlspecialchars($_POST["y_ant"]));
	$nombre_evento = addslashes(htmlspecialchars($_POST["evento"]));
	$max_usuarios = addslashes(htmlspecialchars($_POST["max_usuarios"]));
	
	if($tipo=="select_eventos"){
		select_eventos();
	}
	else if($tipo=="insert_evento"){
		insert_evento($nombre_evento, $max_usuarios);
	}
	else if($tipo=="select_eventos_calendario"){
		select_eventos_calendario();
	}
	else if($tipo=="insert_evento_calendario"){
		insert_evento_calendario($id_evento,$x_post,$y_post);
	}
	else if($tipo=="update_evento_calendario"){
		update_evento_calendario($id_evento,$x_post,$y_post,$x_ant,$y_ant);
	}
	else if($tipo=="delete_evento_calendario"){
		delete_evento_calendario($mi_id);
	}

	function select_eventos(){
		$array[][]="";

		$sql=mysql_query("SELECT * FROM eventos");

		$i=0;

		while($file=mysql_fetch_array($sql)){
			$array[$i][0]=$file['id'];
			$array[$i][1]=$file['nombre'];
			$array[$i][2]=$file['max_usuarios'];
			$i++;
		}
		echo json_encode($array);
	}

	function insert_evento($nombre_evento, $max_usuarios){
		$sql = mysql_query("INSERT INTO eventos (nombre, max_usuarios) 
						VALUES ('$nombre_evento','$max_usuarios')");
		if(!$sql){
			echo "<p style='color:red'>Charly, algo ha fallado al crear el evento...</p>";
		}
		else{
			$array[][]="";
			$sql=mysql_query("SELECT id, nombre, max_usuarios FROM eventos WHERE nombre='$nombre_evento' ");

			if($file=mysql_fetch_array($sql)){
				$array[0][0]=$file['id'];
				$array[0][1]=$file['nombre'];
				$array[0][2]=$file['max_usuarios'];
				$array[0][3]="<p style='color:blue'>Evento creado correctamente.</p>";
				echo json_encode($array);
			}
			else{
				echo "<p style='color:red'>Charly, algo ha fallado al crear el evento...</p>";
			}
		}
	}

	function select_eventos_calendario(){
		$array[][]="";

		$sql=mysql_query("SELECT eventos.id, eventos.nombre, eventos.max_usuarios, evento_calendario.id AS mi_id, evento_calendario.x, evento_calendario.y, evento_calendario.estado FROM eventos, evento_calendario WHERE eventos.id=evento_calendario.id_evento");

		$i=0;

		while($file=mysql_fetch_array($sql)){
			$array[$i][0]=$file['id'];
			$array[$i][1]=$file['nombre'];
			$array[$i][2]=$file['max_usuarios'];
			$array[$i][3]=$file['mi_id'];
			$array[$i][4]=$file['x'];
			$array[$i][5]=$file['y'];
			$array[$i][6]=$file['estado'];
			$i++;
		}
		echo json_encode($array);
	}

	function insert_evento_calendario($id_evento,$x_post,$y_post){
		$sql = mysql_query("INSERT INTO evento_calendario (id_evento,x,y,estado) 
						VALUES ('$id_evento','$x_post','$y_post',1)");
	}

	function update_evento_calendario($id_evento, $x_post, $y_post, $x_ant, $y_ant){
		$sql = mysql_query("SELECT id FROM evento_calendario WHERE id_evento='$id_evento' AND x='$x_ant' AND y='$y_ant' ");
		
		$file=mysql_fetch_array($sql);
		$id=$file['id'];
		$sql=mysql_query("UPDATE evento_calendario SET x='$x_post', y='$y_post' WHERE id='$id'");
	}

	function delete_evento_calendario($mi_id){
		$sql = mysql_query("DELETE FROM evento_calendario WHERE id='$mi_id' ");
		
		if(!$sql){
			echo "0";
		}
		else{
			echo "1";
		}
	}
	
?>