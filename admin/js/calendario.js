$(function(){

	var dirEventos = "php/eventos.php";

	//impide que se pueda seleccionar texto en el lugar indicado
	$('#tabla, #cont-eventos').attr('unselectable', 'on');
	$('#tabla, #cont-eventos').css('MozUserSelect', 'none');//mozilla y derivados
	$('#tabla, #cont-eventos').css('KhtmlUserSelect', 'none');//el safari por ejemplo	

	var array_horas = ['8:00','9:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00'];
	var array_texto_horas = ['8','9','10','11','12','13','14','15','16','17','18','19','20','21','22'];
	var array_nombre_dias = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
	var array_fecha = [];

	var d=new Date();
	
	//recoge el dia y lo adapta de 1 a 7
	var diaNum = d.getDay();
	if(diaNum === 0)
		diaNum = 7;

	//recoge el mes, lo adapta de 1 a 12 y pone 0 a la izquierda si es menor a 10
	var mesNum = d.getMonth() + 1;
	mesNum = ceroIzquierda(mesNum);

	//recoge el año
	var anyoNum = d.getFullYear();

	//recoge el ultimo dia del mes(28, 29, 30 ó 31)
	var ultimoDiaDelMes = daysInMonth(mesNum,anyoNum);

	//crea la primera celda con el texto horario y lo agrega a la celda
	var tr = $("<tr align='center'>")	
	var td = $("<td>")
				.addClass('horario');
	tr.append(td);

	//recorre de 1 a 7 (días de la semana) y crea una celda para cada día, con el nombre del día, el número de día, mes y año
	for(var i=1; i<8;i++){
		var td = $("<td>")
					.addClass('dias')
					.attr('id',array_nombre_dias[i-1])
					.text(function(){
						//si es el dia actual
						if(i===diaNum){
							//pone 0 a la izquierda si el dia es menor a 10
							var num = ceroIzquierda(moment().date());

							//guarda la fecha para la query DATE e identificar la celda
							array_fecha.push(anyoNum+"-"+mesNum+"-"+num);
							
							//muestra la fecha actual
							return array_nombre_dias[i-1]+" "+num+"/"+mesNum+"/"+anyoNum;
						}
						//si no es el dia actual
						else{
							//si es menor al dia actual
							if(i<diaNum){
								var dif = diaNum-i;
								
								var num = ceroIzquierda(moment().subtract('day', dif).date());

								array_fecha.push(anyoNum+"-"+mesNum+"-"+num);
								
								//muestra la fecha anterior al dia actual con moment.subtract
								return array_nombre_dias[i-1]+" "+num+"/"+mesNum+"/"+anyoNum;
							}
							//si es mayor al dia actual
							else if(i>diaNum){
								var dif = i-diaNum;
								
								var num = ceroIzquierda(moment().add('day', dif).date());

								array_fecha.push(anyoNum+"-"+mesNum+"-"+num);
								
								//muestra la fecha posterior con moment.add
								return array_nombre_dias[i-1]+" "+num+"/"+mesNum+"/"+anyoNum;
							}
						}
				});
		tr.append(td);
	}

	//agrega todas las celdas creadas a la tabla
	$("#tabla").append(tr);

	//inserta una fila por cada hora
	for(var i=0;i<array_horas.length;i++){
		var tr = $("<tr>");
		var td = $("<td align='center'>")
					.addClass('hora')
					.attr('id',array_texto_horas[i])
					.text(array_horas[i]);
		tr.append(td);
		//inserta una clase por cada dia
		for(var j = 1; j < 8; j++){
			var td = $("<td>")
				.addClass('clase')
				.attr({
					'id': 'clase_'+array_fecha[j-1]+'_'+array_texto_horas[i],
					'fecha': array_fecha[j-1],
					'hora': array_texto_horas[i]
				})
				.droppable({
					accept: '.evento',
					drop: 	function(event, ui) {
								if($(this).children("div").size() === 0){
									if($(ui.draggable).parent().attr('id') === 'cont-eventos'){
										var dato = "tipo=insert_evento_calendario&idEvento="+$(ui.draggable).attr('id_evento')+"&fecha="+$(this).attr('fecha')+"&hora="+$(this).attr('hora');								
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
										//asigna la nueva fecha y hora
										$(ui.draggable)
											.attr({
												'fecha': $(this).attr('fecha'),
												'hora': $(this).attr('hora')
										});
									}
									else{
										
										var dato = "tipo=update_evento_calendario&idEvento="+$(ui.draggable).attr('id_evento')+"&fecha_post="+$(this).attr('fecha')+"&hora_post="+$(this).attr('hora')+"&fecha_ant="+$(ui.draggable).attr('fecha')+"&hora_ant="+$(ui.draggable).attr('hora');
										$.ajax({
										    type: "POST",
										    url: dirEventos,
										    data: dato
										});
									}
									//asigna la nueva fecha y hora
									$(ui.draggable)
										.attr({
											'fecha': $(this).attr('fecha'),
											'hora': $(this).attr('hora')
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
							'background-color': 'rgb(48, 48, 48)',
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
	//eventosBD[i][4] = fecha,
	//eventosBD[i][5] = hora,
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
							'fecha': eventosBD[i][4],
							'hora': eventosBD[i][5]
						});
					propiedadesEventoCalendario(evento);
		      		$("#clase_"+eventosBD[i][4]+"_"+eventosBD[i][5]).append(evento);
		      	}
	    	}
	    }
	});

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
			errorInput("input#evento");
			showPlaceholder('input#evento', 'Inserta el nombre del evento');
			return false;
		}
		else{
			okInput('input#evento');
			resetPlaceholder('input#evento', 'Nombre');
			evento = evento.substring(0,10);
		}
		if(max === ""){
			errorInput("input#max_usuarios");
			showPlaceholder('input#max_usuarios', 'Inserta el número de usuarios');
			return false;
		}
		else{
			okInput('input#max_usuarios');
			resetPlaceholder('input#max_usuarios', 'Límite usuarios');
			if(!($.isNumeric(max))){
				errorInput("input#max_usuarios");
				showPlaceholder('input#max_usuarios', 'Inserta un número');
				$('input#max_usuarios').val('');
				return false;
			}
			else{
				okInput('input#max_usuarios');
				resetPlaceholder('input#max_usuarios', 'Límite usuarios');
				max = max.substring(0,3);
				if(parseInt(max)===0){
					errorInput("input#max_usuarios");
					showPlaceholder('input#max_usuarios', 'Inserta un número válido');
					$('input#max_usuarios').val('');
					return false;
				}
				else{
					okInput('input#max_usuarios');
					resetPlaceholder('input#max_usuarios', 'Límite usuarios');
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

	function daysInMonth(humanMonth, year) {
		return new Date(year || new Date().getFullYear(), humanMonth, 0).getDate();
	}

	function ceroIzquierda(num){
		if(num <= 9){
			num = "0"+num;
		}
		return num
	}

	function propiedadesEventoContador(evento){
		$(evento).css({
			'margin-bottom': '0.5em',
			'width': '152px',
			'max-width': '152px'
		});
	}

	function propiedadesEventoCalendario(evento){
		$(evento).css({
			'margin-bottom': '0',
			'width': '137px',
			'max-width': '137px'
		});
	}

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
		var img_eliminar = 
			$('<img>')
				.addClass('div_eliminar')
				.attr({
					'src': 'img/eliminar.png',
					'width': '15px',
					'height': '15px'
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
							success: 	function(data){
									 		$(div).remove();
										}
						});
					}
				});
		div_barra_admin.append(img_eliminar);
		div.append(div_barra_admin);
		var div_texto = $('<div>')
							.addClass('texto')
							.text(eventosBD[i][1]);
		div.append(div_texto);
		var div_max_usuarios = $('<div>')
								.addClass('max_usuarios')
								.text("[Rs "+eventosBD[i][2]+"]");
		div.append(div_max_usuarios);
		return div;
	}
	
	function errorInput(input){
		$(input).focus().css({
				'background-color': 'rgb(242, 222, 222)',
				'border-color': 'rgb(235, 204, 209)'
			});
	}

	function okInput(input){
		$(input).css({
				'background-color': 'rgb(255, 255, 255)',
				'border-color': 'rgb(204, 204, 204)'
			});
	}

	function clearInputs(selector){
		$(selector+" :input").each(function(){
			$(this).val('');
		});
		okInput('input');
	}

	function showPlaceholder(input,text){
		$(input)
			.attr({
				'placeholder': text
			});
	}

	function resetPlaceholder(input,text){
		$(input)
			.attr({
				'placeholder': text
			});
	}
});