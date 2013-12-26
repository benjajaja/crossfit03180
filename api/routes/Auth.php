<?php

class Auth {
	public function post($req, $res) {
		$res->add('hola: ' . $req->data['user']);
		$res->send(201, 'text');
	}
}

?>