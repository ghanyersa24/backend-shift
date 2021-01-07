<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Register extends CI_Controller
{
	public function index()
	{
		$data = array(
			"name" => post('name', 'required'),
			"email" => post('email', 'required|email'),
			"hpuser" => post('hpuser', 'required|numeric'),
			"gender" => post('gender', 'enum:laki-laki&perempuan'),
			"jobcurrent" => post('jobcurrent', 'required'),
			"role_id" => 1,
			"password" => password_hash("peopleshift-member", PASSWORD_DEFAULT, array('cost' => 10)),
		);
		$do = DB_MASTER::insert('u1002933_lms.users', $data, false);
		if (!$do->error) {
			success("data employee berhasil ditambahkan", $do->data);
		} else {
			error("data employee gagal ditambahkan");
		}
	}
}
