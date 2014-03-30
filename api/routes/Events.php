<?php

class Events {

	public function post($req, $res) {
		if (!$_SESSION['logged']) {
			$res->add('ACCESO DENEGADO');
			return $res->send(403, 'text');
		}

		$fecha = $req->params['year'].'-'.$req->params['month'].'-'.$req->params['day'];
		$hora = (int) $req->params['hour'];

		$db = $GLOBALS['db'];

		$event = $db->GetArray('SELECT eventos.id, eventos.max_usuarios FROM evento_calendario LEFT JOIN eventos ON eventos.id = evento_calendario.id_evento WHERE evento_calendario.fecha = ? AND evento_calendario.hora = ?', [$fecha, $hora])[0];

		$enroledUsers = $db->GetArray('SELECT COUNT(id_usuario) FROM usuario_evento WHERE id_evento = ?', [$event['id']])[0][0];


		if ($enroledUsers >= $event['max_usuarios']) {
			$res->add('Lo sentimos, todas las plazas están ocupadas en ese día y hora.');
			return $res->send(403, 'text');
		}

		$isUserEnroled = $db->GetArray('SELECT COUNT(id_usuario) FROM usuario_evento WHERE id_evento = ? AND id_usuario = ?', [$event['id'], $_SESSION['user']['id']])[0][0];

		if ($isUserEnroled > 1) {
			$res->add('No puedes inscribirte más veces');
			return $res->send(400, 'text');
		}

		if (((int)$_SESSION['user']['bonos']) <= 0) {
			$res->add('No tienes bonos suficientes');
			return $res->send(403, 'text');
		}


		if ($db->Execute('INSERT INTO usuario_evento SET id_usuario = ?, id_evento = ?', [$_SESSION['user']['id'], $event['id']])) {
			if ($db->Execute('UPDATE usuarios SET bonos = bonos - 1 WHERE id = ?', [$_SESSION['user']['id']])) {
				$_SESSION['user']['bonos'] = ((int)$_SESSION['user']['bonos']) - 1;
				return $res->send(204, 'text');

			} else {
				$res->add('Error interno');
				$res->send(500, 'text');

				$db->Execute('DELETE FROM usuario_evento WHERE id_usuario = ?, id_evento = ?', [$_SESSION['user']['id'], $event['id']]);

				return;
			}
		} else {
			$res->add('Error interno');
			return $res->send(500, 'text');
		}
	}

	public function delete($req, $res) {
		if (!$_SESSION['logged']) {
			$res->add('ACCESO DENEGADO');
			return $res->send(403, 'text');
		}

		$fecha = $req->params['year'].'-'.$req->params['month'].'-'.$req->params['day'];
		$hora = (int) $req->params['hour'];

		$db = $GLOBALS['db'];

		$event = $db->GetArray('SELECT eventos.id, eventos.max_usuarios FROM evento_calendario LEFT JOIN eventos ON eventos.id = evento_calendario.id_evento WHERE evento_calendario.fecha = ? AND evento_calendario.hora = ?', [$fecha, $hora])[0];

		$isUserEnroled = $db->GetArray('SELECT COUNT(id_usuario) FROM usuario_evento WHERE id_evento = ? AND id_usuario = ?', [$event['id'], $_SESSION['user']['id']])[0][0];

		if (!$isUserEnroled) {
			$res->add('No puedes desinscribirte más veces');
			return $res->send(400, 'text');
		}

		if ($db->Execute('DELETE FROM usuario_evento WHERE id_usuario = ? AND id_evento = ?', [$_SESSION['user']['id'], $event['id']])) {
			return $res->send(204, 'text');
		} else {
			$res->add('Error interno');
			return $res->send(500, 'text');
		}
	}
}

?>