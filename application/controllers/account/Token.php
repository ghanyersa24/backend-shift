<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Token extends CI_Controller
{
	public function index()
	{
		$where = array(
			'username' => post('username', 'required'),
			'password' => post('password', 'required'),
		);
		$do = DB_MODEL::find('customer', $where);
		if (!$do->error) {
			$do->data->token = AUTHORIZATION::generateToken($do->data);
			success("token baru nih ye.", $do->data);
		} else
			error("aplikasi tidak dalam kondisi baik, silah kontak customer service kami.");
	}
}
