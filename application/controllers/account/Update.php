<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Update extends CI_Controller
{
	public function index()
	{
		$where = array(
			"id" => AUTHORIZATION::User()->id,
		);
		$data = array(
			"name" => post('name', 'required'),
			"address" => post('address'),
			"email" => post('email', 'required'),
			"telp" => post('telp', 'required|numberic|min_char:12|numberic'),
		);
		$do = DB_MODEL::update('customer', $where, $data);
		if (!$do->error) {
			success("data berhasil diubah", $do->data);
		} else {
			error("data gagal diubah");
		}
	}
}
