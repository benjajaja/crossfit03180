$(function(){

			var dirEventos = "php/eventos.php";

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
													    url: dirEventos,
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
													    url: dirEventos,
													    data: dato
													});
												}
												$(ui.draggable)
													.attr({
														x: $(this).attr('x'),
														y: $(this).attr('y')
													});
												propiedadesEventoCalendario(ui.draggable);
												$(this).append(ui.draggable);
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

			var propiedadesEventoContador = function(evento){
				$(evento).css({
					'margin-bottom': '0.5em',
					'background-color': '#BCF5A9'
				});
			}

			var propiedadesEventoCalendario = function(evento){
				$(evento).css({
					'margin-bottom': '0',
					'background-color': '#f4f4f4'
				});
			}

			//eventosBD es un array bidimensional el cual devuelve las siguientes posiciones
			//eventosBD[i][0] = id,
			//eventosBD[i][1] = nombre,
			//eventosBD[i][2] = max_usuarios
			$.ajax({
				type: "POST",
				url: dirEventos,
				data: "tipo=select_eventos",
				success: function(data){
				   	if(data !== '0'){
				    	var eventosBD = jQuery.parseJSON(data);
					    for(var i in eventosBD){
							var evento = creaEvento(i, eventosBD);
							propiedadesEventoContador(evento);
					      	$("#cont-eventos").append(evento);
					    }
				    }
				}
			});

			//eventosBD es un array bidimensional el cual devuelve las siguientes posiciones
			//eventosBD[i][0] = id,
			//eventosBD[i][1] = nombre,
			//eventosBD[i][2] = max_usuarios,
			//eventosBD[i][3] = id(evento_calendario),
			//eventosBD[i][4] = x,
			//eventosBD[i][5] = y,
			//eventosBD[i][6] = estado
			$.ajax({
			    type: "POST",
			    url: dirEventos,
			    data: "tipo=select_eventos_calendario",
			    success: function(data){
			    	if(data !== '0'){
			    		var eventosBD = jQuery.parseJSON(data);
				      	for(var i in eventosBD){
							var evento = creaEvento(i, eventosBD, "evento_calendario");
							$(evento)
								.attr({
									'x': eventosBD[i][4],
									'y': eventosBD[i][5]
								});
							propiedadesEventoCalendario(evento);
				      		$("#clase_"+eventosBD[i][4]+"_"+eventosBD[i][5]).append(evento);
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
									url: dirEventos,
									data: dato,
									success: 	function(data){
											 		if(data!=="0"){
											 			var dato = "tipo=select_evento_eliminado&idEvento="+$(div).attr('id_evento');
														$.ajax({
															type: "POST",
															url: dirEventos,
															data: dato,
															success: 	function(data){
																	 		if(data !== '0'){
																		    	var eventosBD = jQuery.parseJSON(data);
																				var evento = creaEvento(0, eventosBD);
																				propiedadesEventoContador(evento);
																			    $("#cont-eventos").append(evento);
																		    }
																	 	}
														});
														$(div).remove();
											 		}
											 	}
								});
							}
							else{
								var dato = "tipo=delete_evento&idEvento="+$(div).attr('id_evento');
								$.ajax({
									type: "POST",
									url: dirEventos,
									data: dato,
									success: function(data){
												 $(div).remove();
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

				return div;
			}

			$("#btn_crea_evento").click(function(){
				$('#respuesta_evento').hide();
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
			           	url: dirEventos,
			           	data: dato, 
			           	success: function(data){
			               	if(data !== '[[""]]'){
					    		var eventosBD = jQuery.parseJSON(data);
						      	for(var i in eventosBD){
									var evento = creaEvento(i, eventosBD);
									propiedadesEventoContador(evento);
						      		$("#cont-eventos").append(evento);
						      	}
						      	$('#respuesta_evento').show();
			               		$("#respuesta_evento").html(eventosBD[0][3]); // Mostrar la respuestas del script PHP.
					    	}
			           	}
			    });
			    clearInputs("#form_evento");
			    return false; // Evitar ejecutar el submit del formulario.
			});

			function clearInputs(selector){
				$(selector+" :input").each(function(){
					$(this).val('');
				});
				$('.error').hide();
			}
		});