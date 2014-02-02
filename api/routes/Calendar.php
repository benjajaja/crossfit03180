<?php

// insert into usuarios set nombre = 'Benja', email = 'ste3ls@gmail.com', pass = UNHEX(SHA1('<SECRETO>hola'));
// alter table usuarios modify pass binary(20);

class Calendar {

	public function get($req, $res) {

		$events = array_map(function($item) {
			$start = strtotime($item['fecha'] . ' ' . $item['hora'] . ':00');
			/*$start = strtotime('previous monday');

			$start += ($item['y'] - 1) * 86400;
			$start += ($item['x'] + 8) * 3600;*/

			$end = $start + 3600;

			return array(
				//'id' => (int) $item['id'],
				'allDay' => false,
				'title' => $item['nombre'],
				'start' => $start,
				'end' => $end,
				'isEnroled' => $_SESSION['user']['id'] && $_SESSION['user']['id'] == $item['id_usuario']
			);
		}, $GLOBALS['db']->GetAll('SELECT * FROM evento_calendario LEFT JOIN eventos ON eventos.id = evento_calendario.id_evento LEFT JOIN usuario_evento ON usuario_evento.id_evento = evento_calendario.id_evento AND usuario_evento.id_usuario = ?', [$_SESSION['user']['id']]));

		if ($events === null) {
			$events = [];
		}

		$res->add(json_encode($events));

		$res->send(200, 'json');
	}
}

?>