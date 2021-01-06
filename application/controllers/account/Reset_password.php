<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reset_password extends CI_Controller
{
	public function index()
	{
		$where = array(
			"id" => AUTHORIZATION::User()->id,
		);
		$new_password = post("new_password", 'required');
		post("password_confirmation", "required|same:new_password");

		if (password_verify(post("password", 'required'), AUTHORIZATION::User()->password)) {
			$data = array(
				'password' => password_hash($new_password, PASSWORD_DEFAULT, array('cost' => 10))
			);
			$do = DB_MODEL::update('customer', $where, $data);
			if (!$do->error) {
				success("password berhasil diubah", $do->data);
			} else {
				error("password gagal diubah");
			}
		} else
			error("password lama salah.");
	}
}
