$(function(){
	var dirUsuarios = "php/usuarios.php";
			
	//impide que se pueda seleccionar texto en el lugar indicado
	$('#tabla_users, #cont-eventos').attr('unselectable', 'on');
	$('#tabla_users, #cont-eventos').css('MozUserSelect', 'none');//mozilla y derivados
	$('#tabla_users, #cont-eventos').css('KhtmlUserSelect', 'none');//el safari por ejemplo	
	
	var tr = $('<tr>');
	
	var td = $('<td align="center">').addClass('titulo editar');
	var imgEditar = $('<img src="img/editar.png" widht="30" height="30">');
	td.append(imgEditar);
	tr.append(td);

	var td = $('<td align="center">').addClass('titulo nombre').text("Nombre");
	ordenar(td,"nombre");
	tr.append(td);
			
	var td = $('<td align="center">').addClass('titulo apellidos').text("Apellidos");
	ordenar(td,"apellidos");
	tr.append(td);
			
	var td = $('<td align="center">').addClass('titulo email').text("Email");
	ordenar(td,"email");
	tr.append(td);
			
	var td = $('<td align="center">').addClass('titulo telefono').text("Teléfono");
	ordenar(td,"telefono");
	tr.append(td);
			
	var td = $('<td align="center">').addClass('titulo dni').text("DNI");
	ordenar(td,"dni");
	tr.append(td);

	var td = $('<td align="center">').addClass('titulo bonos').text("Bonos");
	ordenar(td,"bonos");
	tr.append(td);

	var td = $('<td align="center">').addClass('titulo eliminar');
	var imgEliminar = $('<img src="img/eliminar.png" widht="30" height="30">');
	td.append(imgEliminar);
	tr.append(td);

	$('#tabla_users').append(tr);//inserta la fila creada a la tabla
			
	/****************************************

		La llamada a insertUsers() devuelve
		usersBD[0][0] = id,
		usersBD[0][1] = nombre,
		usersBD[0][2] = apellidos,
		usersBD[0][4] = email,
		usersBD[0][5] = telefono,
		usersBD[0][6] = dni,
		usersBD[0][7] = mensaje,
		usersBD[0][8] = bonos

		------------------------------------
		
		La llamada a selectUsers() devuelve
		usersBD[i][0] = id,
		usersBD[i][1] = nombre,
		usersBD[i][2] = apellidos,
		usersBD[i][4] = email,
		usersBD[i][5] = telefono,
		usersBD[i][6] = dni,
		usersBD[i][8] = bonos

	*****************************************/

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
		var bonos = $("input#bonos").val();

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
		if (!/^([0-9])*$/.test(telefono)){
			errorInput('input#telefono');
			showPlaceholder('input#telefono', 'El teléfono no es un número...');
			$('input#telefono').val('');
			return false;
		}
		else{
			okInput('input#telefono');
			resetPlaceholder('input#telefono', 'Teléfono');
		}

		if(bonos === ""){
			errorInput('input#bonos');
			showPlaceholder('input#bonos', 'Introduce los bonos');
			return false;
		}
		else{
			if (!/^([0-9])*$/.test(bonos)){
				errorInput('input#bonos');
				showPlaceholder('input#bonos', 'No has introducido un número...');
				$('input#bonos').val('');
				return false;
			}
			else{
				okInput('input#bonos');
				resetPlaceholder('input#bonos', 'Bonos');
			}
		}

		var dato = "tipo=insert_user&nombre="+nombre+"&apellidos="+apellidos+"&pass="+pwd1+"&email="+email+"&telefono="+telefono+"&dni="+dni+"&bonos="+bonos;
	    $.ajax({
	           	type: "POST",
	           	url: dirUsuarios,
	           	data: dato,
	           	success: function(data){
	           		$('#respuesta_usuario').show();
	           		var usersBD = jQuery.parseJSON(data);
	           		if(usersBD === 0){
	           			$("#respuesta_usuario")
		           			.html("Charly, ese DNI ya existe.")
		           			.css({
			               		'color': 'red'
		               	});
	           		}
	           		else if(usersBD === 1){
	           			$("#respuesta_usuario")
		           			.html("Charly, algo ha fallado al insertar el usuario...")
		           			.css({
			               		'color': 'red'
		               	});
	           		}
	           		else if(usersBD !== 0 && usersBD !== 1){
		               	$("#respuesta_usuario")
			               	.html(usersBD[0][8])
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
		$('#pass').html(passwordAleatorio(8));
	});

	$("#btn_add_bonos").click(function(){
		var addbonos = $("input#addbonos").val();
		if (!/^([0-9])*$/.test(addbonos)){
			errorInput('input#addbonos');
			showPlaceholder('input#addbonos', 'No has introducido un número...');
			$('input#addbonos').val('');
			return false;
		}
		else{
			okInput('input#addbonos');
			resetPlaceholder('input#addbonos', 'Bonos');
		}

		var dato = "tipo=edit_user&id="+$('.user').attr('id')+"&bonos="+addbonos;
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
		clearInputs("#form_edit");
	    return false; // Evitar ejecutar el submit del formulario.
	});

	function ordenar(td, clase){
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

	function actualizaUsers(usersBD){
		for(var i in usersBD){
			
			var tr = $('<tr>');

			var tdEditar = $('<td align="center">')
						.addClass('datos editar');
			var img_editar = 
					$('<img>')
						.attr({
							'id': usersBD[i][0],
							'nombre': usersBD[i][1],
							'apellidos': usersBD[i][2],
							'src': 'img/editar.png',
							'width': '30px',
							'height': '30px'
						})
						.mousedown(function() {
							$("#myModal_edit").modal('show');
							$('.user').html($(this).attr('nombre')+" "+$(this).attr('apellidos'));
							$('.user').attr({
								'id': $(this).attr('id')
							});
						});
			tdEditar.append(img_editar);
			tr.append(tdEditar);

			
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

			var tdBonos = $('<td align="center">')
						.addClass('datos bonos')
						.text(usersBD[i][7]);
			tr.append(tdBonos);
			
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

	function passwordAleatorio(length, special) {
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

	// Recibe el 'id' del elemento HTML para proceder a la validación, si es correcta devuelve 'true' y sino saca un alert y devuelve 'false'
	//Requiere del framework jQuery
	function valida_dni(a) {
	    var resul = true;
	    var temp = trim(jQuery('#'+a).val()).toUpperCase();
	    var cadenadni = "TRWAGMYFPDXBNJZSQVHLCKE";
	    if (temp !== '') {
	        //algoritmo para comprobacion de codigos tipo CIF
	        suma = parseInt(temp[2]) + parseInt(temp[4]) + parseInt(temp[6]);
	        for (i = 1; i < 8; i += 2) {
	            temp1 = 2 * parseInt(temp[i]);
	            temp1 += '';
	            temp1 = temp1.substring(0,1);
	            temp2 = 2 * parseInt(temp[i]);
	            temp2 += '';
	            temp2 = temp2.substring(1,2);
	            if (temp2 == '') {
	                temp2 = '0';
	            }
	            suma += (parseInt(temp1) + parseInt(temp2));
	        }
	        suma += '';
	        n = 10 - parseInt(suma.substring(suma.length-1, suma.length));
	        //si no tiene un formato valido devuelve error
	        if ((!/^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$/.test(temp) && !/^[T]{1}[A-Z0-9]{8}$/.test(temp)) && !/^[0-9]{8}[A-Z]{1}$/.test(temp)) {
	            if ((temp.length == 9) && (/^[0-9]{9}$/.test(temp))) {
	                var posicion = temp.substring(8,0) % 23;
	                var letra = cadenadni.charAt(posicion);
	                var letradni = temp.charAt(8);
	                alert("La letra del NIF no es correcta. " + letradni + " es diferente a " + letra);
	                jQuery('#'+a).val(temp.substr(0,8) + letra);
	                resul = false;
	            } else if (temp.length == 8) {
	                if (/^[0-9]{1}/.test(temp)) {
	                    var posicion = temp.substring(8,0) % 23;
	                    var letra = cadenadni.charAt(posicion);
	                    var tipo = 'NIF';
	                } else if (/^[KLM]{1}/.test(temp)) {
	                    var letra = String.fromCharCode(64 + n);
	                    var tipo = 'NIF';
	                } else if (/^[ABCDEFGHJNPQRSUVW]{1}/.test(temp)) {
	                    var letra = String.fromCharCode(64 + n);
	                    var tipo = 'CIF';
	                } else if (/^[T]{1}/.test(temp)) {
	                    var letra = String.fromCharCode(64 + n);
	                    var tipo = 'NIE';
	                } else if (/^[XYZ]{1}/.test(temp)) {
	                    var pos = str_replace(['X', 'Y', 'Z'], ['0','1','2'], temp).substring(0, 8) % 23;
	                    var letra = cadenadni.substring(pos, pos + 1);
	                    var tipo = 'NIE';
	                }
	                if (letra !== '') {
	                    alert("Añadido la letra del " + tipo + ": " + letra);
	                    jQuery('#'+a).val(temp + letra);
	                } else {
	                    alert ("El CIF/NIF/NIE tiene que tener 9 caracteres");
	                    jQuery('#'+a).val(temp);
	                }
	                resul = false;
	            } else if (temp.length < 8) {
	                alert ("El CIF/NIF/NIE tiene que tener 9 caracteres");
	                jQuery('#'+a).val(temp);
	                resul = false;
	            } else {
	                alert ("CIF/NIF/NIE incorrecto");
	                jQuery('#'+a).val(temp);
	                resul = false;
	            }
	        }
	        //comprobacion de NIFs estandar
	        else if (/^[0-9]{8}[A-Z]{1}$/.test(temp)) {
	            var posicion = temp.substring(8,0) % 23;
	            var letra = cadenadni.charAt(posicion);
	            var letradni = temp.charAt(8);
	            if (letra == letradni) {
	                return 1;
	            } else if (letra != letradni) {
	                alert("La letra del NIF no es correcta. " + letradni + " es diferente a " + letra);
	                jQuery('#'+a).val(temp.substr(0,8) + letra);
	                resul = false;
	            } else {
	                alert ("NIF incorrecto");
	                jQuery('#'+a).val(temp);
	                resul = false;
	            }
	        }
	        //comprobacion de NIFs especiales (se calculan como CIFs)
	        else if (/^[KLM]{1}/.test(temp)) {
	            if (temp[8] == String.fromCharCode(64 + n)) {
	                return 1;
	            } else if (temp[8] != String.fromCharCode(64 + n)) {
	                alert("La letra del NIF no es correcta. " + temp[8] + " es diferente a " + String.fromCharCode(64 + n));
	                jQuery('#'+a).val(temp.substr(0,8) + String.fromCharCode(64 + n));
	                resul = false;
	            } else {
	                alert ("NIF incorrecto");
	                jQuery('#'+a).val(temp);
	                resul = false;
	            }
	        }
	        //comprobacion de CIFs
	        else if (/^[ABCDEFGHJNPQRSUVW]{1}/.test(temp)) {
	            var temp_n = n + '';
	            if (temp[8] == String.fromCharCode(64 + n) || temp[8] == parseInt(temp_n.substring(temp_n.length-1, temp_n.length))) {
	                return 2;
	            } else if (temp[8] != String.fromCharCode(64 + n)) {
	                alert("La letra del CIF no es correcta. " + temp[8] + " es diferente a " + String.fromCharCode(64 + n));
	                jQuery('#'+a).val(temp.substr(0,8) + String.fromCharCode(64 + n));
	                resul = false;
	            } else if (temp[8] != parseInt(temp_n.substring(temp_n.length-1, temp_n.length))) {
	                alert("La letra del CIF no es correcta. " + temp[8] + " es diferente a " + parseInt(temp_n.substring(temp_n.length-1, temp_n.length)));
	                jQuery('#'+a).val(temp.substr(0,8) + parseInt(temp_n.substring(temp_n.length-1, temp_n.length)));
	                resul = false;
	            } else {
	                alert ("CIF incorrecto");
	                jQuery('#'+a).val(temp);
	                resul = false;
	            }
	        }
	        //comprobacion de NIEs
	        //T
	        else if (/^[T]{1}/.test(temp)) {
	            if (temp[8] == /^[T]{1}[A-Z0-9]{8}$/.test(temp)) {
	                return 3;
	            } else if (temp[8] != /^[T]{1}[A-Z0-9]{8}$/.test(temp)) {
	                var letra = String.fromCharCode(64 + n);
	                var letranie = temp.charAt(8);
	                if (letra != letranie) {
	                    alert("La letra del NIE no es correcta. " + letranie + " es diferente a " + letra);
	                    jQuery('#'+a).val(temp.substr(0,8) + letra);
	                    resul = false;
	                } else {
	                    alert ("NIE incorrecto");
	                    jQuery('#'+a).val(temp);
	                    resul = false;
	                }
	            }
	        }
	        //XYZ
	        else if (/^[XYZ]{1}/.test(temp)) {
	            var pos = str_replace(['X', 'Y', 'Z'], ['0','1','2'], temp).substring(0, 8) % 23;
	            var letra = cadenadni.substring(pos, pos + 1);
	            var letranie = temp.charAt(8);
	            if (letranie == letra) {
	                return 3;
	            } else if (letranie != letra) {
	                alert("La letra del NIE no es correcta. " + letranie + " es diferente a " + letra);
	                jQuery('#'+a).val(temp.substr(0,8) + letra);
	                resul = false;
	            } else {
	                alert ("NIE incorrecto");
	                jQuery('#'+a).val(temp);
	                resul = false;
	            }
	        }
	    }
	    if (!resul) {      
	        jQuery('#'+a).focus();
	        return resul;
	    }
	}
});