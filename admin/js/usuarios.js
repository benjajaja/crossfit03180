$(function(){
	var dirUsuarios = "php/usuarios.php";
			
	//impide que se pueda seleccionar texto en el lugar indicado
	$('#tabla_users, #cont-eventos').attr('unselectable', 'on');
	$('#tabla_users, #cont-eventos').css('MozUserSelect', 'none');//mozilla y derivados
	$('#tabla_users, #cont-eventos').css('KhtmlUserSelect', 'none');//el safari por ejemplo	
	
	var imgOrden = function(td, clase){
		var img = $('<img>')
			.attr({
				'src': 'img/asc.png',
				'width': '30px',
				'height': '30px',
				'orden': 'asc'
			})
			.css({
				'float': 'right'
			})
			.addClass(clase)
			.mousedown(function(){
				if($(this).attr("orden")==="asc"){
					$(this).attr({
						'src': 'img/desc.png',
						'orden': 'desc'
					});
					var dato = "tipo=select_users&query=ORDER BY "+clase+" DESC";
					$.ajax({
						type: "POST",
						url: dirUsuarios,
						data: dato,
						success: 	function(data){
										$(".datos").remove();
										var usersBD = jQuery.parseJSON(data);
										actualizaUsers(usersBD);
									}
					});
				}
				else{
					$(this).attr({
						'src': 'img/asc.png',
						'orden': 'asc'
					});
					var dato = "tipo=select_users&query=ORDER BY "+clase+" ASC";
					$.ajax({
						type: "POST",
						url: dirUsuarios,
						data: dato,
						success: 	function(data){
										$(".datos").remove();
										var usersBD = jQuery.parseJSON(data);
										actualizaUsers(usersBD);
									}
					});
				}
			});
		td.append(img);
	}
	var tr = $('<tr>');
	
	var td = $('<td align="center">').addClass('titulo nombre').text("Nombre");
	imgOrden(td,"nombre");
	tr.append(td);
			
	var td = $('<td align="center">').addClass('titulo apellidos').text("Apellidos");
	imgOrden(td,"apellidos");
	tr.append(td);
			
	var td = $('<td align="center">').addClass('titulo email').text("Email");
	imgOrden(td,"email");
	tr.append(td);
			
	var td = $('<td align="center">').addClass('titulo telefono').text("Teléfono");
	imgOrden(td,"telefono");
	tr.append(td);
			
	var td = $('<td align="center">').addClass('titulo dni').text("DNI");
	imgOrden(td,"dni");
	tr.append(td);

	var td = $('<td align="center">').addClass('titulo eliminar');
	var imgEliminar = $('<img src="img/eliminar.png" widht="30" height="30">');
	td.append(imgEliminar);
	tr.append(td);

	$('#tabla_users').append(tr);//inserta la fila creada a la tabla
			
	//usersBD es un array bidimensional el cual devuelve las siguientes posiciones
	//usersBD[i][0] = id,
	//usersBD[i][1] = nombre,
	//usersBD[i][2] = apellidos,
	//usersBD[i][3] = pass,
	//usersBD[i][4] = email,
	//usersBD[i][5] = telefono,
	//usersBD[i][6] = dni
	var dato = "tipo=select_users&query=";
	$.ajax({
		type: "POST",
		url: dirUsuarios,
		data: dato,
		success: function(data){
		  	if(data !== '0'){
		   		var usersBD = jQuery.parseJSON(data);
		      	actualizaUsers(usersBD);
		   	}
		}
	});
	
	function actualizaUsers(usersBD){
		for(var i in usersBD){
			var tr = $('<tr>');
				var tdNombre = $('<td align="center">')
						.addClass('datos nombre')
						.text(usersBD[i][1]);
			tr.append(tdNombre);
			
			var tdApellidos = $('<td align="center">')
						.addClass('datos apellidos')
						.text(usersBD[i][2]);
			tr.append(tdApellidos);
							
			var tdEmail = $('<td align="center">')
						.addClass('datos email')
						.text(usersBD[i][4]);
			tr.append(tdEmail);

			var tdTelefono = $('<td align="center">')
						.addClass('datos telefono')
						.text(usersBD[i][5]);
			tr.append(tdTelefono);

			var tdDni = $('<td align="center">')
						.addClass('datos dni')
						.text(usersBD[i][6]);
			tr.append(tdDni);

			var tdEliminar = $('<td align="center">')
						.addClass('datos eliminar');
			var img_eliminar = 
					$('<img>')
						.attr({
							'id': usersBD[i][0],
							'src': 'img/eliminar.png',
							'width': '30px',
							'height': '30px'
						})
						.mousedown(function() {
							var dato = "tipo=delete_user&id="+$(this).attr('id');
							$.ajax({
								type: "POST",
								url: dirUsuarios,
								data: dato,
								success: function(data){
								  	if(data !== '0'){
								  		$(".datos").remove();
								   		var usersBD = jQuery.parseJSON(data);
								      	actualizaUsers(usersBD);
								   	}
								}
							});
						});
			tdEliminar.append(img_eliminar);
			tr.append(tdEliminar);

			$('#tabla_users').append(tr);//inserta la fila creada a la tabla
      	}
	}

	function errorInput(input){
		$(input).focus()
			.css({
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

	function showPlaceholder(input,texto){
		$(input)
			.attr({
				'placeholder': texto
			});
	}

	function resetPlaceholder(input,texto){
		$(input)
			.attr({
				'placeholder': texto
			});
	}

	$("#btn_crea_usuario").click(function(){
		$('#respuesta_usuario').hide();
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
			errorInput('input#nombre');
			showPlaceholder('input#nombre', 'Inserta el nombre');
			return false;
		}
		else{
			okInput('input#nombre');
			resetPlaceholder('input#nombre', 'Nombre');
			nombre = nombre.substring(0,20);
		}
		if (apellidos === "") {
			errorInput('input#apellidos');
			showPlaceholder('input#apellidos', 'Inserta los apellidos');
			return false;
		}
		else{
			okInput('input#apellidos');
			resetPlaceholder('input#apellidos', 'Apellidos');
			apellidos = apellidos.substring(0,30);
		}
		if (dni === "") {
			errorInput('input#dni');
			showPlaceholder('input#dni', 'Inserta el DNI');
			return false;
		}
		else{
			okInput('input#dni');
			resetPlaceholder('input#dni', 'DNI');
			if(dni.length!==9){
				errorInput('input#dni');
				showPlaceholder('input#dni', 'DNI incorrecto');
				$('input#dni').val('');
				return false;
			}
			else{
				okInput('input#dni');
				resetPlaceholder('input#dni', 'DNI');
			}
		}
		if(pwd1 === ""){
			errorInput('input#pwd1');
			showPlaceholder('input#pwd1', 'Introduce una contraseña');
			return false;
		}
		else{
			okInput('input#pwd1');
			resetPlaceholder('input#pwd1', 'Password');
		}
		if(pwd2 === ""){
			errorInput('input#pwd2');
			showPlaceholder('input#pwd2', 'Repite password');
			return false;
		}
		else{
			okInput('input#pwd2');
			resetPlaceholder('input#pwd2', 'Repite password');
		}
		if(pwd1 !== pwd2){
			errorInput('input#pwd1');
			errorInput('input#pwd2');
			showPlaceholder('input#pwd1,input#pwd2', 'Las contraseñas no coinciden');
			$('input#pwd1,input#pwd2').val('');
			return false;
		}
		else{
			okInput('input#pwd1');
			okInput('input#pwd2');
			resetPlaceholder('input#pwd1', 'Password');
			resetPlaceholder('input#pwd2', 'Repite password');
		}
		var dato = "tipo=insert_user&nombre="+nombre+"&apellidos="+apellidos+"&pass="+pwd1+"&email="+email+"&telefono="+telefono+"&dni="+dni;
	    $.ajax({
	           	type: "POST",
	           	url: dirUsuarios,
	           	data: dato,
	           	success: function(data){
	           		$('#respuesta_usuario').show();
	           		var usersBD = jQuery.parseJSON(data);
	           		if(usersBD === 0){
	           			$("#respuesta_usuario").html("Charly, ese DNI ya existe.")
	           			.css({
		               		'color': 'red'
		               	});
	           		}
	           		else if(usersBD === 1){
	           			$("#respuesta_usuario").html("Charly, algo ha fallado al insertar el usuario...")
	           			.css({
		               		'color': 'red'
		               	});
	           		}
	           		else if(usersBD !== 0 && usersBD !== 1){
		               	$("#respuesta_usuario").html(usersBD[0][7])
		               	.css({
		               		'color': 'blue'
		               	}); 
		               	actualizaUsers(usersBD);
		            }
	           	}
	    });
		clearInputs("#form_usuario");
	    return false; // Evitar ejecutar el submit del formulario.
	});

	$('#btn_crea_pass').mousedown(function() {
		$('#pass').html(password(8));
	});

	function password(length, special) {
	  	var iteration = 0;
	  	var password = "";
	  	var randomNumber;
	  	if(special == undefined){
	      	var special = false;
	  	}
	  	while(iteration < length){
	    	randomNumber = (Math.floor((Math.random() * 100)) % 94) + 33;
	    	if(!special){
	      		if ((randomNumber >=33) && (randomNumber <=47)) { continue; }
	      		if ((randomNumber >=58) && (randomNumber <=64)) { continue; }
	      		if ((randomNumber >=91) && (randomNumber <=96)) { continue; }
	      		if ((randomNumber >=123) && (randomNumber <=126)) { continue; }
	    	}
	    	iteration++;
	    	password += String.fromCharCode(randomNumber);
	  	}
	  	return password;
	}
});