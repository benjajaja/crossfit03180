<?php

class Auth {
	public function get($req, $res) {
		$res->add('hola!');
		$res->send(201, 'text');
	}
}

?>