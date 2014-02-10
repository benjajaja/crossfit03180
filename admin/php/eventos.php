<?php
	include '../connect/mysql_connect.php';

	$tipo = addslashes(htmlspecialchars($_POST["tipo"]));
	
	if($tipo=="select_eventos"){
		select_eventos();
	}
	else if($tipo=="select_evento_eliminado"){
		$id_evento = addslashes(htmlspecialchars($_POST["idEvento"]));
		select_evento_eliminado($id_evento);
	}
	else if($tipo=="insert_evento"){
		$nombre_evento = addslashes(htmlspecialchars($_POST["evento"]));
		$max_usuarios = addslashes(htmlspecialchars($_POST["max_usuarios"]));
		insert_evento($nombre_evento, $max_usuarios);
	}
	else if($tipo=="delete_evento"){
		$id_evento = addslashes(htmlspecialchars($_POST["idEvento"]));
		delete_evento($id_evento);
	}
	else if($tipo=="select_eventos_calendario"){
		$fecha_inicio = addslashes(htmlspecialchars($_POST["fecha_inicio"]));
		$fecha_fin = addslashes(htmlspecialchars($_POST["fecha_fin"]));
		select_eventos_calendario($fecha_inicio,$fecha_fin);
	}
	else if($tipo=="insert_evento_calendario"){
		$id_evento = addslashes(htmlspecialchars($_POST["idEvento"]));
		$fecha = addslashes(htmlspecialchars($_POST["fecha"]));
		$hora = addslashes(htmlspecialchars($_POST["hora"]));
		insert_evento_calendario($id_evento,$fecha,$hora);
	}
	else if($tipo=="update_evento_calendario"){
		$id_evento = addslashes(htmlspecialchars($_POST["idEvento"]));
		$fecha_post = addslashes(htmlspecialchars($_POST["fecha_post"]));
		$hora_post = addslashes(htmlspecialchars($_POST["hora_post"]));
		$fecha_ant = addslashes(htmlspecialchars($_POST["fecha_ant"]));
		$hora_ant = addslashes(htmlspecialchars($_POST["hora_ant"]));
		update_evento_calendario($id_evento,$fecha_post,$hora_post,$fecha_ant,$hora_ant);
	}
	else if($tipo=="delete_evento_calendario"){
		$id_evento_calendario = addslashes(htmlspecialchars($_POST["id_evento_calendario"]));
		delete_evento_calendario($id_evento_calendario);
	}

	/**********************
	***********************
	*******FUNCIONES*******
	***********************
	***********************
	**********************/

	function select_eventos(){
		$sql=mysql_query("SELECT * FROM eventos WHERE NOT EXISTS (SELECT id_evento FROM evento_calendario WHERE eventos.id = evento_calendario.id_evento)");
		
		$array[][]="";
		$i=0;
		while($file=mysql_fetch_array($sql)){
			$array[$i][0]=$file['id'];
			$array[$i][1]=$file['nombre'];
			$array[$i][2]=$file['max_usuarios'];
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

	function select_evento_eliminado($id_evento){
		$sql=mysql_query("SELECT * FROM eventos WHERE id='$id_evento'");
		
		$array[][]="";
		if($file=mysql_fetch_array($sql)){
			$array[0][0]=$file['id'];
			$array[0][1]=$file['nombre'];
			$array[0][2]=$file['max_usuarios'];
		}
		$result = json_encode($array);
		
		if($result == '[[""]]'){
			echo "0";
		}
		else{
			echo $result;
		}
	}

	function insert_evento($nombre_evento, $max_usuarios){
		$sql = mysql_query("INSERT INTO eventos (nombre, max_usuarios) 
						VALUES ('$nombre_evento','$max_usuarios')");
		if(!$sql){
			echo "<p style='color:red'>Charly, algo ha fallado al crear el evento...</p>";
		}
		else{
			$array[][]="";
			$sql=mysql_query("SELECT * FROM eventos WHERE id IN(SELECT MAX(id) AS id FROM eventos)");//selecciona todo donde el id sea el ultimo
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

	function delete_evento($id_evento){
		mysql_query("DELETE FROM eventos WHERE id='$id_evento' ");
	}

	function select_eventos_calendario($fecha_inicio,$fecha_fin){
		$array[][]="";
		$sql=mysql_query("SELECT eventos.id, eventos.nombre, eventos.max_usuarios, evento_calendario.id AS mi_id, evento_calendario.fecha, evento_calendario.hora, evento_calendario.estado FROM eventos, evento_calendario WHERE eventos.id=evento_calendario.id_evento AND evento_calendario.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' ");
		$i=0;
		while($file=mysql_fetch_array($sql)){
			$array[$i][0]=$file['id'];
			$id_evento=$array[$i][0];
			$array[$i][1]=$file['nombre'];
			$array[$i][2]=$file['max_usuarios'];
			$array[$i][3]=$file['mi_id'];
			$array[$i][4]=$file['fecha'];
			$array[$i][5]=$file['hora'];
			$array[$i][6]=$file['estado'];
			
			$sql_apuntados=mysql_query("SELECT COUNT(usuario_evento.id) AS apuntados FROM usuario_evento WHERE '$id_evento'=usuario_evento.id_evento");
			
			if($file_apuntados=mysql_fetch_array($sql_apuntados)){
				$array[$i][7]=$file_apuntados['apuntados'];
			}
			else{
				$array[$i][7]='0';
			}

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

	function insert_evento_calendario($id_evento,$fecha,$hora){
		mysql_query("INSERT INTO evento_calendario (id_evento,fecha,hora,estado) VALUES ('$id_evento','$fecha','$hora',1)");
		$sql = mysql_query("SELECT MAX(id) AS id FROM evento_calendario");
		$file = mysql_fetch_array($sql);
		$id = $file['id'];
		$id = json_encode($id);
		echo $id;
	}

	function update_evento_calendario($id_evento,$fecha_post,$hora_post,$fecha_ant,$hora_ant){
		$sql = mysql_query("SELECT id FROM evento_calendario WHERE id_evento = '$id_evento' AND fecha = '$fecha_ant' AND hora = '$hora_ant' ");
		$file = mysql_fetch_array($sql);
		$id = $file['id'];
		mysql_query("UPDATE evento_calendario SET fecha = '$fecha_post', hora = '$hora_post' WHERE id = '$id'");
		delete_usuario_evento($id_evento);
	}

	function delete_evento_calendario($id_evento_calendario){
		$sql = mysql_query("SELECT id_evento FROM evento_calendario WHERE id = '$id_evento_calendario'");
		$file = mysql_fetch_array($sql);
		$id = $file['id_evento'];

		$sql = mysql_query("DELETE FROM evento_calendario WHERE id='$id_evento_calendario' ");
		if(!$sql){
			echo "0";
		}
		else{
			delete_usuario_evento($id);
			echo "1";
		}
	}

	function delete_usuario_evento($id_evento){
		mysql_query("DELETE FROM usuario_evento WHERE id_evento = '$id_evento' ");
	}

?>