<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Employee extends CI_Controller
{
	protected $table = "u1002933_lms.users";
	public function __construct()
	{
		parent::__construct();
		// additional library
	}
	public function create()
	{
		$data = array(
			"name" => post('name', 'required'),
			"email" => post('email', 'required|email|unique:u1002933_lms.users'),
			"hpuser" => post('hpuser', 'required|numeric|unique:u1002933_lms.users'),
			"gender" => post('gender', 'enum:laki-laki&perempuan'),
			"jobcurrent" => post('jobcurrent', 'required'),
			"role_id" => 1,
			"password" => password_hash("peopleshift-member", PASSWORD_DEFAULT, array('cost' => 10)),
		);
		$do = DB_MASTER::insert($this->table, $data);
		if (!$do->error) {
			success("data employee berhasil ditambahkan", $do->data);
		} else {
			error("data employee gagal ditambahkan");
		}
	}

	public function get($id = null)
	{
		if (is_null($id)) {
			$do = DB_MASTER::where($this->table, ['role_id' => 1]);
		} else {
			$do = DB_MASTER::find($this->table, array('role_id' => 1, "id" => $id));
		}

		if (!$do->error)
			success("data employee berhasil ditemukan", $do->data);
		else
			error("data employee gagal ditemukan");
	}

	public function update()
	{
		$data = array(
			"name" => post('name', 'required'),
			"email" => post('email', 'required|email'),
			"hpuser" => post('hpuser', 'required|numeric'),
			"gender" => post('gender', 'enum:laki-laki&perempuan'),
			"jobcurrent" => post('jobcurrent', 'required'),
		);

		$where = array(
			"id" => post('id', 'required'),
		);

		$do = DB_MASTER::update($this->table, $where, $data);
		if (!$do->error)
			success("data employee berhasil diubah", $do->data);
		else
			error("data employee gagal diubah");
	}

	public function delete()
	{
		$where = array(
			"id" => post('id',"required")
		);

		$do = DB_MASTER::delete($this->table, $where);
		if (!$do->error)
			success("data employee berhasil dihapus", $do->data);
		else
			error("data employee gagal dihapus");
	}
}
