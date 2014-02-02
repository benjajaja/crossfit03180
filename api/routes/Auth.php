<?php

// insert into usuarios set nombre = 'Benja', email = 'ste3ls@gmail.com', pass = UNHEX(SHA1('<SECRETO>hola'));
// alter table usuarios modify pass binary(20);

class Auth {

	public function post($req, $res) {

		$users = $GLOBALS['db']->GetAll(
			'SELECT id, nombre FROM usuarios WHERE UPPER(email) = UPPER(?) AND pass = UNHEX(SHA1(?)) LIMIT 1',
			[
				$req->data['user'],
				$GLOBALS['db']->_salt . $req->data['password']
			]
		);

		if (count($users) !== 1) {
			sleep(1); // pa que no lo peten o algo
			$res->add('Contraseña o email incorrecto');
			return $res->send(403, 'text'); // status: 403 Forbidden

		} else {
			$_SESSION['logged'] = true;
			$_SESSION['user'] = $users[0];
			$res->add('Acceso correcto'); // no importa, el jQuery solo mira el status
			return $res->send(200, 'text'); // status: 200 Ok!

		}
	}

	public function get($req, $res) {
		if ($_SESSION['logged'] === true) {
			$res->add(json_encode(array(
				'name' => $_SESSION['user']['nombre']
			))); // no importa, el jQuery solo mira el status
			return $res->send(200, 'json'); // status: 200 Ok!
		} else {
			$res->add('No registrado'); // no importa, el jQuery solo mira el status
			return $res->send(404, 'text'); // status: 200 Ok!
		}
		

	}

	public function delete($req, $res) {
		session_destroy();
		return $res->send(204);
	}
}

?>