<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<title>Crossfit03180</title>
	<link rel="stylesheet" href="../css/style_calendario.css"/>
	<link rel="stylesheet" href="../css/bootstrap.min_calendar.css"/>
	<script src="../js/jquery-1.9.1.js"></script>
	<script src="../js/jquery-ui.js"></script>
	<script src="../js/moment.min.js"></script>
	<script src="../js/bootstrap.min_calendar.js"></script>
	
	<script type="text/javascript">
		$(function(){
			//impide que se pueda seleccionar texto en el lugar indicado
			$('#tabla, #cont-eventos').attr('unselectable', 'on');
			$('#tabla, #cont-eventos').css('MozUserSelect', 'none');//mozilla y derivados
			$('#tabla, #cont-eventos').css('KhtmlUserSelect', 'none');//el safari por ejemplo	

			var array_horas = ['7:00','8:00','9:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00'];
			var array_dias = ['Lun','Mar','Mie','Jue','Vie','Sab','Dom'];

			//recoge el dia y el mes actual
			var d=new Date();
			var diaNum= d.getDay();
			var mesNum= d.getMonth()+1;
			if(diaNum===0)
				diaNum=7;

			//crea la primera celda con el texto horario
			var tr = $("<tr align='center'>")
			var td = $("<td>").addClass('horario');
			tr.append(td);

			//recorre de 1 a 7 (días de la semana) y crea una celda para cada día con el nombre del día, el número de día y mes
			var numCeldaHoy;
			for(var i=1; i<8;i++){
				var td = $("<td>")
						.addClass('dias')
						.attr('id',array_dias[i-1])
						.text(function(){
							if(i===diaNum){
								numCeldaHoy = i;
								$(this).css({
									'background-color': 'rgba(0, 0, 0, 0)'
								});
								return array_dias[i-1]+" "+moment().date()+"/"+mesNum;
							}
							else{
								if(i<diaNum){
									var dif = diaNum-i;
									return array_dias[i-1]+" "+moment().subtract('day', dif).date()+"/"+mesNum;
								}
								else if(i>diaNum){
									var dif = i-diaNum;
									return array_dias[i-1]+" "+moment().add('day', dif).date()+"/"+mesNum;
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
													ui.draggable.attr({
														x: $(this).attr('x'),
														y: $(this).attr('y')
													});
													$.ajax({
													    type: "POST",
													    url: "admin/eventos.php",
													    data: dato
													});
													$(ui.draggable)
														.clone()
														.appendTo('#cont-eventos')
														.draggable({
															drag: function(event, ui) {
																$('body').css({
																	'cursor': 'pointer'
																});
																$('div#cont-eliminar').css({
																	'visibility': 'visible'
																});
															},
															stop: function(event, ui) {
																$('div#cont-eliminar').css({
																	'visibility': 'hidden'
																});
															},
															opacity: 0.75,
															helper: "clone"
														})
														.sortable();
												}
												else{
													var dato = "tipo=update_evento_calendario&idEvento="+$(ui.draggable).attr('id_evento')+"&x_ant="+$(ui.draggable).attr('x')+"&y_ant="+$(ui.draggable).attr('y')+"&x_post="+$(this).attr('x')+"&y_post="+$(this).attr('y');
													ui.draggable.attr({
														x: $(this).attr('x'),
														y: $(this).attr('y')
													});
													$.ajax({
													    type: "POST",
													    url: "admin/eventos.php",
													    data: dato
													});
												}
												$(this).append(ui.draggable);
											}
										}
							});
					tr.append(td);
				}
				$('#tabla').append(tr);//inserta la fila creada a la tabla
			}

			//eventosBD es un array bidimensional el cual devuelve las siguientes posiciones
			//eventosBD[i][0] = id,
			//eventosBD[i][1] = nombre,
			//eventosBD[i][2] = max_usuarios
			$.ajax({
			    type: "POST",
			    url: "admin/eventos.php",
			    data: "tipo=select_eventos",
			    success: function(data){
			    	if(data !== '[[""]]'){
			    		var eventosBD = jQuery.parseJSON(data);
				      	for(var i in eventosBD){
							var evento = creaEvento(i, eventosBD);
				      		$("#cont-eventos").append(evento);
				      	}
			    	}
			    }
			});
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
			    	if(data !== '[[""]]'){
			    		var eventosBD = jQuery.parseJSON(data);
				      	for(var i in eventosBD){
							var objeto = creaEvento(i, eventosBD);
							$(objeto).attr({
								'x': eventosBD[i][4],
								'y': eventosBD[i][5]
							});
				      		$("#clase_"+eventosBD[i][4]+"_"+eventosBD[i][5]).append(objeto);
				      	}
			    	}
			    }
			});

			function creaEvento(i, eventosBD){
				return $("<div>")
					.addClass('evento')
					.attr('id','evento')
					.text(eventosBD[i][1])
					.draggable({
						drag: function(event, ui) {
							$('body').css({
								'cursor': 'pointer'
							});
							$('div#cont-eliminar').css({
								visibility: 'visible'
							});
						},
						stop: function(event, ui) {
							$('div#cont-eliminar').css({
								visibility: 'hidden'
							});
						},
						opacity: 0.75,
						helper: "clone"
					})
					.sortable()
					.attr({
						'mi_id': eventosBD[i][3],
						'id_evento': eventosBD[i][0],
						'maxUsuarios': eventosBD[i][2]
					});
			}

			$("#cont-eliminar")
				.droppable({
					accept: '.evento',
					drop: 	function(event, ui) {
								if($(ui.draggable).parent().attr('id') !== 'cont-eventos'){
									var dato = "tipo=delete_evento_calendario&mi_id="+$(ui.draggable).attr('mi_id');
									$.ajax({
									    type: "POST",
									    url: "admin/eventos.php",
									    data: dato,
									    success: function(data){
									 		if(data!=="0"){
									 			$(ui.draggable).hide();
									 		}
									    }
									});
								}
								else{
									var dato = "tipo=delete_evento&idEvento="+$(ui.draggable).attr('id_evento');
									$.ajax({
									    type: "POST",
									    url: "admin/eventos.php",
									    data: dato,
									    success: function(data){
									 	
									    }
									});
								}
							}
				});

			$("#btn_crea_evento").click(function(){
				$('.error').hide();
				$('#respuesta_evento').hide();
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
						return false;
					}
					else{
						$("label#max_numeric_error").hide();
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
						      		$("#cont-eventos").append(evento);
						      	}
						      	$('#respuesta_evento').show();
			               		$("#respuesta_evento").html(eventosBD[0][3]); // Mostrar la respuestas del script PHP.
					    	}
			           	}
			    });
				$("input").val("");
			    return false; // Evitar ejecutar el submit del formulario.
			});

			/*$("#btn_crea_usuario").click(function(){
				$('.error').hide();
				$('#respuesta_usuario').hide();
			});
			$("#btn_inserta_usuario").click(function(){
				//Obtenemos el valor de los campos
				var usuario = $("input#usuario").val();
				var pwd_1 = $("input#pwd_1").val();
				var pwd_2 = $("input#pwd_2").val();
				var email = $("input#email").val();

				//Validamos el campo nombre, simplemente miramos que no esté vacío
				if (usuario === "") {
					$("label#usuario_error").show();
					$("input#usuario").focus();
					return false;
				}
				if(pwd_1 === ""){
					$("label#pwd1_error").show();
					$("input#pwd1").focus();
					return false;
				}
				if(pwd_2 === ""){
					$("label#pwd2_error").show();
					$("input#pwd2").focus();
					return false;
				}

				if(pwd_1 !== pwd_2){
					$("label#pwd_error").show();
					$("input#pwd1,input#pwd2").focus();
					return false;
				}

			    $.ajax({
			           	type: "POST",
			           	url: "admin/inserta_usuario.php",
			           	data: $("#form_usuario").serialize(), // Adjuntar los campos del formulario enviado.
			           	success: function(data){
			               	$("#respuesta_usuario").html(data); // Mostrar la respuestas del script PHP.
			           	}
			    });
				$("input").val("");
			    return true; // Evitar ejecutar el submit del formulario.
			});*/
		});
	</script>

</head>
<body>
	<div class="cuerpo_principal">
		<table id="tabla">
			<div id="cont-eliminar">
				<img src="img/borrar.png"><p>
				<span>Arrastra el evento aquí para eliminarlo</span>
			</div>
			<div id="cont-usuarios">
				<!-- Button trigger modal -->
				<button id="btn_crea_usuario" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal_2">
					Crear usuario
				</button><p>
			</div>
			<div id="cont-eventos">
				<!-- Button trigger modal -->
				<button id="btn_crea_evento" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal_1">
					Crear evento
				</button><p>
			</div>
		</table>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="myModal_1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        		<h4 class="modal-title" id="myModalLabel">Registrar evento</h4>
	      		</div>
		      	<div class="modal-body">
			        <form id="form_evento" action="" method="post">
						<p>
							<label for="evento">Nombre de evento
								<input name="evento" type="text" id="evento" size=30>
							</label><p>
							<label class="error" for="evento" id="evento_error">Introduce el nombre del evento.</label>
						</p>
						<p>
							<label for="maximo">Máximo de usuarios
								<input name="max_usuarios" type="number" id="max_usuarios" size=10 min=0 maxlength="3">
							</label><p>
							<label class="error" for="maximo" id="max_error">Introduce el numero máximo de usuarios</label>
							<label class="error" for="maximo" id="max_numeric_error">Introduce un número</label>
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
	<!-- Modal -->
	<div class="modal fade" id="myModal_2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        		<h4 class="modal-title" id="myModalLabel">Registrar usuario</h4>
	      		</div>
		      	<div class="modal-body">
			        <form id="form_usuario" action="" method="post">
						<p>
							<label for="usuario">Nombre de usuario
								<input name="usuario" type="text" id="usuario" size=30>
							</label><p>
							<label class="error" for="usuario" id="usuario_error">Introduce el nombre del evento.</label>
						</p>
						<p>
							<label for="pwd_1">Password
								<input name="pwd_1" type="password" id="pwd_1" size=30>
							</label><p>
							<label class="error" for="pwd_1" id="pwd_1_error">Introduce el nombre del evento.</label>
							<label class="error" for="pwd_1" id="pwd_error">Las contraseñas no coinciden.</label>
						</p>
						<p>
							<label for="pwd_2">Repite password 
								<input name="pwd_2" type="password" id="pwd_2" size=30>
							</label><p>
							<label class="error" for="pwd_2" id="pwd_2_error">Introduce el nombre del evento.</label>
							<label class="error" for="pwd_2" id="pwd_error">Las contraseñas no coinciden.</label>
						</p>
						<p>
							<label for="email">E-mail
								<input name="email" type="text" id="email" size=30>
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