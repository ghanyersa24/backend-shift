<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
	protected $table = "users";
	public function __construct()
	{
		parent::__construct();
		// additional library
	}
	public function create()
	{
		$data = array(
			"name" => post('name', 'required'),
			"phone" => post('phone', 'required|unique:users'),
			"email" => post('email', 'required|email|unique:users'),
		);

		$do = DB_MASTER::insert($this->table, $data, false);
		if (!$do->error)
			success("data " . $this->table . " berhasil ditambahkan", $do->data);
		else
			error("data " . $this->table . " gagal ditambahkan");
	}

	public function get($id = null)
	{
		if (is_null($id)) {
			$do = DB_MODEL::all($this->table);
		} else {
			$do = DB_MODEL::find($this->table, array("id" => $id));
		}

		if (!$do->error)
			success("data " . $this->table . " berhasil ditemukan", $do->data);
		else
			error("data " . $this->table . " gagal ditemukan");
	}

	public function update()
	{
		$data = array(
			"name" => post('name', 'required'),
			"phone" => post('phone', 'required|unique:users'),
			"email" => post('email', 'required|email|unique:users'),
		);

		$where = array(
			"id" => post('id', 'required'),
		);

		$do = DB_MASTER::update($this->table, $where, $data);
		if (!$do->error)
			success("data " . $this->table . " berhasil diubah", $do->data);
		else
			error("data " . $this->table . " gagal diubah");
	}

	public function delete()
	{
		$where = array(
			"id" => post('id')
		);

		$do = DB_MODEL::delete($this->table, $where);
		if (!$do->error)
			success("data " . $this->table . " berhasil dihapus", $do->data);
		else
			error("data " . $this->table . " gagal dihapus");
	}
}
