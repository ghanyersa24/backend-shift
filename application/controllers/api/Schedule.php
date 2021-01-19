<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Schedule extends CI_Controller
{
	protected $table = "schedules";
	public function __construct()
	{
		parent::__construct();
		// additional library
	}
	public function get()
	{
		$do = DB_MODEL::join("events", "schedules", "events.id=schedules.events_id", "inner", [], "schedules.*, events.event, events.color");
		if (!$do->error)
			success("data " . $this->table . " berhasil ditemukan", $do->data);
		else
			error("data " . $this->table . " gagal ditemukan");
	}


	public function update()
	{
		$data = array(
			"name" => post('name', "required"),
			"start" => post('start', "required"),
			"end" => post('end', "required"),
		);
		$event = post("event", "required");
		$where = array(
			"id" => post('id', "required"),
		);

		$do = DB_MODEL::update($this->table, $where, $data);
		if (!$do->error)
			success("Jadwal $event berhasil diubah", $do->data);
		else
			error("Jadwal $event gagal diubah");
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
