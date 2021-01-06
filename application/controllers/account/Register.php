<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Register extends CI_Controller
{
	public function index()
	{
		$data = array(
			"name" => post('name', 'required'),
			"address" => post('address'),
			"email" => post('email', 'required|unique:customer'),
			"telp" => post('telp', 'required|numeric|min_char:11|numeric|unique:customer'),
			"business_name" => post('business_name', 'max_char:100'),
			"business_type" => post('business_type', 'max_char:30'),
			"latitude" => post('latitude', 'required'),
			"longitude" => post('longitude', 'required'),
			"password" => password_hash(post('password', 'required'), PASSWORD_DEFAULT, array('cost' => 10)),
			"role" => 'mitra',
			"status" => 'activated',
			"point" => 0
		);
		post('password_confirmation', 'same:password');
		$do = DB_MASTER::insert('customer', $data);
		if (!$do->error) {
			success("data berhasil ditambahkan", $do->data);
		} else {
			error("data gagal ditambahkan");
		}
	}
}
