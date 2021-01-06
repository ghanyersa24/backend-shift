<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Profile extends CI_Controller
{
	public function customer()
	{
		$where = array(
			'id' => AUTHORIZATION::User()->id,
		);
		$do = DB_MODEL::find('customer', $where);
		if (!$do->error)
			success("data profil diterima", $do->data);
		else
			error("data profil tidak ditemukan");
	}
}
