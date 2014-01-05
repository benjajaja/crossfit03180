<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<title>Panel admin - Usuarios</title>
	<link rel="stylesheet" href="css/main.css"/>
	<link rel="stylesheet" href="css/bootstrap.min.css"/>
	<link rel="stylesheet" href="css/jquery-ui.css"/>
    <script src="js/jquery-1.9.1.js"></script>
	<script src="js/jquery-ui.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<script type="text/javascript">
		$(function(){
			//impide que se pueda seleccionar texto en el lugar indicado
			$('#tabla, #cont-eventos').attr('unselectable', 'on');
			$('#tabla, #cont-eventos').css('MozUserSelect', 'none');//mozilla y derivados
			$('#tabla, #cont-eventos').css('KhtmlUserSelect', 'none');//el safari por ejemplo	

			var tr = $('<tr>');
			var td = $('<td>').addClass('datos').text("Nombre");
			tr.append(td);
			var td = $('<td>').addClass('datos').text("Apellidos");
			tr.append(td);
			var td = $('<td>').addClass('datos').text("Email");
			tr.append(td);
			var td = $('<td>').addClass('datos').text("Teléfono");
			tr.append(td);
			var td = $('<td>').addClass('datos').text("DNI");
			tr.append(td);

			$('#tabla').append(tr);//inserta la fila creada a la tabla

			//usersBD es un array bidimensional el cual devuelve las siguientes posiciones
			//usersBD[i][0] = id,
			//usersBD[i][1] = nombre,
			//usersBD[i][2] = apellidos,
			//usersBD[i][3] = pass,
			//usersBD[i][4] = email,
			//usersBD[i][5] = telefono,
			//usersBD[i][6] = dni
			$.ajax({
			    type: "POST",
			    url: "admin/usuarios.php",
			    data: "tipo=select_users",
			    success: function(data){
			    	if(data !== '0'){
			    		var usersBD = jQuery.parseJSON(data);
				      	for(var i in usersBD){
							var tr = $('<tr>');
							var tdNombre = $('<td>')
										.addClass('datos nombre')
										.text(usersBD[i][1]);
							tr.append(tdNombre);
							
							var tdApellidos = $('<td>')
										.addClass('datos apellidos')
										.text(usersBD[i][2]);
							tr.append(tdApellidos);
							
							var tdEmail = $('<td>')
										.addClass('datos email')
										.text(usersBD[i][4]);
							tr.append(tdEmail);

							var tdTelefono = $('<td>')
										.addClass('datos telefono')
										.text(usersBD[i][5]);
							tr.append(tdTelefono);

							var tdDni = $('<td>')
										.addClass('datos dni')
										.text(usersBD[i][6]);
							tr.append(tdDni);

							$('#tabla').append(tr);//inserta la fila creada a la tabla
				      	}

			    	}
			    }
			});

			$("#btn_crea_usuario").click(function(){
				clearInputs("#form_usuario");
			});

			$("#btn_inserta_usuario").click(function(){
				//Obtenemos el valor de los campos
				var nombre = $("input#nombre").val();
				var apellidos = $("input#apellidos").val();
				var dni = $("input#dni").val();
				var pwd1 = $("input#pwd1").val();
				var pwd2 = $("input#pwd2").val();
				var email = $("input#email").val();
				var telefono = $("input#telefono").val();


				//Validamos el campo nombre, simplemente miramos que no esté vacío
				if (nombre === "") {
					$("label#nombre_error").show();
					$("input#nombre").focus();
					return false;
				}
				else{
					$('#nombre_error').hide();
					nombre = nombre.substring(0,20);
				}
				if (apellidos === "") {
					$("label#apellidos_error").show();
					$("input#apellidos").focus();
					return false;
				}
				else{
					$('#apellidos_error').hide();
					apellidos = apellidos.substring(0,30);
				}
				if (dni === "") {
					$("label#dni_error").show();
					$("input#dni").focus();
					return false;
				}
				else{
					$('#dni_error').hide();
					if(dni.length!==9){
						$("label#dni_tam_error").show();
						$("input#dni").focus();
						return false;
					}
					else{
						$('#dni_tam_error').hide();
					}
				}
				if(pwd1 === ""){
					$("label#pwd1_error").show();
					$("input#pwd1").focus();
					return false;
				}
				else{
					$('#pwd1_error').hide();
				}
				if(pwd2 === ""){
					$("label#pwd2_error").show();
					$("input#pwd2").focus();
					return false;
				}
				else{
					$('#pwd2_error').hide();
				}
				if(pwd1 !== pwd2){
					$("label#pwd_error").show();
					$("input#pwd1,input#pwd2").focus();
					return false;
				}
				else{
					$('#pwd_error').hide();
				}
				var dato="tipo=insert_user&nombre="+nombre+"&apellidos="+apellidos+"&pass="+pwd1+"&email="+email+"&telefono="+telefono+"&dni="+dni;
			    $.ajax({
			           	type: "POST",
			           	url: "admin/usuarios.php",
			           	data: dato, // Adjuntar los campos del formulario enviado.
			           	success: function(data){
			           		$('#respuesta_usuario').show();
			               	$("#respuesta_usuario").html(data); // Mostrar la respuestas del script PHP.
			           	}
			    });
				clearInputs("#form_usuario");
			    $('#respuesta_evento').hide();
			    return false; // Evitar ejecutar el submit del formulario.
			});

			function clearInputs(selector){
				$(selector+" :input").each(function(){
					$(this).val('');
				});
				$('.error').hide();
			}
		});
	</script>
	
</head>
<body>
	<div class="cuerpo_principal">
		<div id="cont-usuarios">
			<!-- Button trigger modal -->
			<button id="btn_crea_usuario" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal_2">
				Crear usuario
			</button><p>
		</div>
		<!--Aquí se crea la tabla de usuarios-->
		<table id="tabla"></table>
	</div>
	<div class="modal fade" id="myModal_2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        		<h4 class="modal-title" id="myModalLabel">Registrar usuario</h4>
	      		</div>
		      	<div class="modal-body">
			        <form id="form_usuario" method="post">
						<p>
							<label for="nombre">Nombre
								<input name="nombre" type="text" id="nombre" size="20" maxlength="20" autocomplete="off">
							</label><p>
							<label class="error" for="nombre" id="nombre_error">Introduce el nombre</label>
						</p>
						<p>
							<label for="apellidos">Apellidos
								<input name="apellidos" type="text" id="apellidos" size="20" maxlength="30" autocomplete="off">
							</label><p>
							<label class="error" for="apellidos" id="apellidos_error">Introduce los apellidos</label>
						</p>
						<p>
							<label for="dni">DNI
								<input name="dni" type="text" id="dni" size="20" maxlength="9" autocomplete="off">
							</label><p>
							<label class="error" for="dni" id="dni_error">Introduce el DNI</label>
							<label class="error" for="dni" id="dni_tam_error">DNI incorrecto</label>
						</p>
						<p>
							<label for="pwd1">Password
								<input name="pwd1" type="password" id="pwd1" size="20" maxlength="20" autocomplete="off">
							</label><p>
							<label class="error" for="pwd1" id="pwd1_error">Introduce la contraseña</label>
							<label class="error" for="pwd1" id="pwd_error">Las contraseñas no coinciden</label>
						</p>
						<p>
							<label for="pwd2">Repite password 
								<input name="pwd2" type="password" id="pwd2" size="20" maxlength="20" autocomplete="off">
							</label><p>
							<label class="error" for="pwd2" id="pwd2_error">Introduce la contraseña</label>
							<label class="error" for="pwd2" id="pwd_error">Las contraseñas no coinciden</label>
						</p>
						<p>
							<label for="email">E-mail
								<input name="email" type="text" id="email" size="20" maxlength="50" autocomplete="off">
							</label>
						</p>
						<p>
							<label for="telefono">Teléfono
								<input name="telefono" type="number" id="telefono" size="20" maxlength="9" autocomplete="off">
							</label>
						</p>
						<p>
							<span id="respuesta_usuario"></span>
						</p>
						<div class="modal-footer">
				        	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				        	<button type="submit" class="btn btn-primary" id="btn_inserta_usuario" name="Submit" value="Enviar">Guardar</button>
				      	</div>
					</form>	 
		      	</div>
	    	</div><!-- /.modal-content -->
	  	</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</body>
</html>