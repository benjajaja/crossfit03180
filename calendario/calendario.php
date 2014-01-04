<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<title>Crossfit03180</title>
	<link rel="stylesheet" href="css/main.css"/>
	<link rel="stylesheet" href="css/bootstrap.min.css"/>
	<link rel="stylesheet" href="css/jquery-ui.css"/>
    <script src="js/jquery-1.9.1.js"></script>
	<script src="js/jquery-ui.js"></script>
	<script src="js/moment.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery-ui-touch-punch.js"></script>

	<script type="text/javascript">
		$(function(){
			//impide que se pueda seleccionar texto en el lugar indicado
			$('#tabla, #cont-eventos').attr('unselectable', 'on');
			$('#tabla, #cont-eventos').css('MozUserSelect', 'none');//mozilla y derivados
			$('#tabla, #cont-eventos').css('KhtmlUserSelect', 'none');//el safari por ejemplo	

			var array_horas = ['8:00','9:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00'];
			var array_dias = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];

			//recoge el dia y el mes actual
			var d=new Date();
			var diaNum = d.getDay();
			var mesNum = d.getMonth()+1;
			//var ultimoDiaDelMes = moment().daysInMonth();
			var dates = [new Date()];
			if(diaNum===0)
				diaNum=7;

			//crea la primera celda con el texto horario
			var tr = $("<tr align='center'>")
			
			var td = $("<td>")
						.addClass('horario');
			
			tr.append(td);

			//recorre de 1 a 7 (días de la semana) y crea una celda para cada día con el nombre del día, el número de día y mes
			for(var i=1; i<8;i++){
				var td = $("<td>")
						.addClass('dias')
						.attr('id',array_dias[i-1])
						.text(function(){
							if(i===diaNum){//si es el dia actual
								return array_dias[i-1]+" "+moment().date();
							}
							else{//si no es el dia actual
								if(i<diaNum){
									var dif = diaNum-i;
									return array_dias[i-1]+" "+moment().subtract('day', dif).date();
									
								}
								else if(i>diaNum){
									var dif = i-diaNum;
									return array_dias[i-1]+" "+moment().add('day', dif).date();
								}
							}
						});
				tr.append(td);
			}
			$("#tabla").append(tr);//agrega todas las celdas creadas a la tabla

			//inserta una fila por cada hora
			for(var i=0;i<array_horas.length;i++){
				var tr = $("<tr>");
				var td = $("<td>")
						.addClass('hora')
						.attr('id','hora')
						.text(array_horas[i]);
				tr.append(td);
				for(var j = 1; j < 8; j++){
					var td = $("<td>")
							.addClass('clase')
							.attr('id','clase_'+i+'_'+j)
							.attr({
								'x': i,
								'y': j
							})
							.droppable({
								accept: '.evento',
								drop: 	function(event, ui) {
											if($(this).children("div").size() === 0){
												if($(ui.draggable).parent().attr('id') === 'cont-eventos'){
													
													var dato = "tipo=insert_evento_calendario&idEvento="+$(ui.draggable).attr('id_evento')+"&x_post="+$(this).attr('x')+"&y_post="+$(this).attr('y');
													
													$.ajax({
													    type: "POST",
													    url: "admin/eventos.php",
													    data: dato,
													    success: 	function(data){
													    				var id = jQuery.parseJSON(data);
													    				$(ui.draggable)
													    					.attr({
																				'id_evento_calendario': id
																			});
													    }
													});
													//asigna las nuevas posiciones X e Y
													$(ui.draggable)
														.attr({
															x: $(this).attr('x'),
															y: $(this).attr('y')
														});
												}
												else{
													var dato = "tipo=update_evento_calendario&idEvento="+$(ui.draggable).attr('id_evento')+"&x_ant="+$(ui.draggable).attr('x')+"&y_ant="+$(ui.draggable).attr('y')+"&x_post="+$(this).attr('x')+"&y_post="+$(this).attr('y');
													$.ajax({
													    type: "POST",
													    url: "admin/eventos.php",
													    data: dato
													});
												}
												$(ui.draggable)
													.attr({
														x: $(this).attr('x'),
														y: $(this).attr('y')
													});
												$(this)
													.append(ui.draggable);
											}
										}
							});
					//resalta los horarios del día actual
					if(j===diaNum){
						$(td)
							.css({
								'background-color': 'rgba(12, 48, 73, 50)',
								'z-index': '-1'
							});
					}

					tr.append(td);
				}
				$('#tabla').append(tr);//inserta la fila creada a la tabla
			}

			//eventosBD es un array bidimensional el cual devuelve las siguientes posiciones
			//eventosBD[i][0] = id,
			//eventosBD[i][1] = nombre,
			//eventosBD[i][2] = max_usuarios,
			//eventosBD[i][3] = id,
			//eventosBD[i][4] = x,
			//eventosBD[i][5] = y,
			//eventosBD[i][6] = estado
			$.ajax({
			    type: "POST",
			    url: "admin/eventos.php",
			    data: "tipo=select_eventos_calendario",
			    success: function(data){
			    	if(data !== '0'){
			    		var eventosBD = jQuery.parseJSON(data);
				      	for(var i in eventosBD){
							var objeto = creaEvento(i, eventosBD, "evento_calendario");
							$(objeto)
								.attr({
									'x': eventosBD[i][4],
									'y': eventosBD[i][5]
								});
				      		$("#clase_"+eventosBD[i][4]+"_"+eventosBD[i][5]).append(objeto);
				      	}
			    	}
			    }
			});

			function creaEvento(i, eventosBD, tipo){

				var div = $("<div>")
							.addClass('evento');
				$(div)
					.attr({
						'id': 'evento',
						'id_evento_calendario': eventosBD[i][3],
						'id_evento': eventosBD[i][0],
						'maxUsuarios': eventosBD[i][2]
					});
				$(div)
					.mouseover(function() {
						div_barra_admin
							.css({
								'visibility': 'visible'
							});
					})
					.mouseout(function() {
						div_barra_admin
							.css({
								'visibility': 'hidden'
							});
					})
					.draggable({
						opacity: 0.75,
						helper: "clone"
					});

				var div_barra_admin = 
					$('<div>')
						.addClass('barra_admin')
						.css({
							'visibility': 'hidden'
						});

				var div_eliminar = 
					$('<img>')
						.addClass('div_eliminar')
						.attr({
							'src': 'img/eliminar.gif',
							'width': '10px',
							'height': '10px'
						})
						.css({
							'float': 'right'
						})
						.mousedown(function(){
							if($(div).parent().attr('id') !== 'cont-eventos'){
								var dato = "tipo=delete_evento_calendario&id_evento_calendario="+$(div).attr('id_evento_calendario');
								$.ajax({
									type: "POST",
									url: "admin/eventos.php",
									data: dato,
									success: 	function(data){
											 		if(data!=="0"){
											 			$(div)
											 				.remove();
											 		}
											 	}
								});
							}
							else{
								var dato = "tipo=delete_evento&idEvento="+$(div).attr('id_evento');
								$.ajax({
									type: "POST",
									url: "admin/eventos.php",
									data: dato,
									success: function(data){
												 $(div)
												 	.remove();
									}
								});
							}
						});

				div_barra_admin.append(div_eliminar);

				div.append(div_barra_admin);

				var div_texto = 
					$('<div>')
						.addClass('texto')
						.text(eventosBD[i][1]);

				div.append(div_texto);

				var div_max_usuarios = 
					$('<div>')
						.addClass('max_usuarios')
						.text("[Rs "+eventosBD[i][2]+"]");

				div.append(div_max_usuarios);

				//$(div).drag();

				return div;
			}

			$("#btn_crea_evento").click(function(){
				clearInputs("#form_evento");
			});
			
			$("#btn_inserta_evento").click(function(){
				//Obtenemos el valor de los campos
				var evento = $("input#evento").val();
				var max = $("input#max_usuarios").val();

				//Validamos el campo nombre, simplemente miramos que no esté vacío
				if (evento === "") {
					$("label#evento_error").show();
					$("input#evento").focus();
					return false;
				}
				else{
					$('#evento_error').hide();
					evento = evento.substring(0,10);
				}
				if(max === ""){
					$("label#max_error").show();
					$("input#max_usuarios").focus();
					return false;
				}
				else{
					$('#max_error').hide();
					if(!($.isNumeric(max))){
						$("label#max_numeric_error").show();
						$("input#max_usuarios").focus();
						return false;
					}
					else{
						$("label#max_numeric_error").hide();
						max = max.substring(0,3);
						if(parseInt(max)===0){
							$("label#max_cero_error").show();
							$("input#max_usuarios").focus();
							return false;
						}
						else{
							$("label#max_cero_error").hide();
						}
					}
				}
				var dato="tipo=insert_evento&evento="+evento+"&max_usuarios="+max;
			    $.ajax({
			           	type: "POST",
			           	url: "admin/eventos.php",
			           	data: dato, // Adjuntar los campos del formulario enviado.
			           	success: function(data){
			               	if(data !== '[[""]]'){
					    		var eventosBD = jQuery.parseJSON(data);
						      	for(var i in eventosBD){
									var evento = creaEvento(i, eventosBD);
						      		$("#cont-eventos").prepend(evento);
						      	}
						      	$('#respuesta_evento').show();
			               		$("#respuesta_evento").html(eventosBD[0][3]); // Mostrar la respuestas del script PHP.
					    	}
			           	}
			    });
			    clearInputs("#form_evento");
			    $('#respuesta_evento').hide();
			    return false; // Evitar ejecutar el submit del formulario.
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
		<div id="cont-btn-evento">
			<!-- Button trigger modal -->
			<button id="btn_crea_evento" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal_1">
				Crear evento
			</button><p>
			<div id="cont-eventos"></div>
		</div>
		<!--Aquí se crea el calendario-->
		<table id="tabla"></table>
	</div>
	<!-- Modal 1-->
	<div class="modal fade" id="myModal_1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        		<h4 class="modal-title" id="myModalLabel">Registrar evento</h4>
	      		</div>
		      	<div class="modal-body">
			        <form id="form_evento" method="post">
						<p>
							<label for="evento">Nombre de evento
								<input name="evento" type="text" id="evento" size="30" maxlength="10" autocomplete="off">
							</label><p>
							<label class="error" for="evento" id="evento_error">Introduce el nombre del evento.</label>
						</p>
						<p>
							<label for="maximo">Máximo de usuarios
								<input name="max_usuarios" type="number" id="max_usuarios" size="10" maxlength="3" autocomplete="off">
							</label><p>
							<label class="error" for="maximo" id="max_error">Introduce el numero máximo de usuarios</label>
							<label class="error" for="maximo" id="max_numeric_error">Introduce un número</label>
							<label class="error" for="maximo" id="max_cero_error">Introduce un máximo de usuarios</label>
							</p>
						<p>
							<span id="respuesta_evento"></span>
						</p>
						<div class="modal-footer">
				        	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				        	<button type="submit" class="btn btn-primary" id="btn_inserta_evento" name="Submit" value="Enviar">Guardar</button>
				      	</div>
					</form>	 
		      	</div>
	    	</div><!-- /.modal-content -->
	  	</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- Modal 2-->
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
	<script type="text/javascript">
     /* $.fn.dragg = function() {
        var offset = null;
        var start = function(e) {
          var orig = e.originalEvent;
          var pos = $(this).position();
          offset = {
            x: orig.changedTouches[0].pageX - pos.left,
            y: orig.changedTouches[0].pageY - pos.top
          };
        };
        var moveMe = function(e) {
          e.preventDefault();
          var orig = e.originalEvent;
          $(this).css({
            top: orig.changedTouches[0].pageY - offset.y,
            left: orig.changedTouches[0].pageX - offset.x
          });
        };
        this.bind("touchstart", start);
        this.bind("touchmove", moveMe);
      };

      $("#evento").dragg();*/
    </script>
</html>