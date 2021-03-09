<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Patterns extends CI_Controller
{
	protected $table = "patterns";
	public function __construct()
	{
		parent::__construct();
		// additional library
	}
	public function create()
	{
		$events_id = input_json("events_id");
		$patterns = input_json("patterns");
		$session = input_json("session");
		if (!$session)
			error("sesi tidak boleh kosong atau 0");
		// $events_id = 6;
		DB_MODEL::force_delete($this->table, ["events_id" => $events_id]);
		DB_MODEL::force_delete("schedules", ["events_id" => $events_id]);
		DB_MODEL::update("events", ["id" => $events_id], ["session" => $session]);
		$event = DB_MODEL::find("events", ["id" => $events_id]);
		if ($event->error)
			error("event tidak ditemukan");
		$data = [];
		foreach ($patterns as  $pattern) {
			$pattern["id"] = UUID::v4();
			$pattern["created_at"] = date('Y-m-d H:i:s');
			$pattern["created_by"] = AUTHORIZATION::User()->id;
			$pattern["updated_at"] = date('Y-m-d H:i:s');
			$pattern["updated_by"] = AUTHORIZATION::User()->id;
			$data[] = $pattern;
		}
		$do = DB_MODEL::insert_any($this->table, $data);
		if (!$do->error) {
			$this->generate_schedules($event->data, (object)$patterns, $session);
			success("Jadwal " . $event->data->event . " berhasil dibuat", $do->data);
		} else
			error("data " . $this->table . " gagal ditambahkan");
	}

	private function generate_schedules($event, $patterns, $session)
	{
		$this->load->helper("schedule");
		$month = date("m", strtotime($event->start_time));
		$year = date("Y", strtotime($event->start_time));
		$sesi = 1;
		$schedules = [];
		while ($sesi <= $session) {
			$date_list = SCHEDULE::DATE_LIST($month, $year);
			$report = SCHEDULE::GENERATE($schedules, $sesi, $date_list, $event, $patterns);
			$schedules = $report->list;
			$sesi = $report->sesi;
			$month++;
			if ($month == 13) {
				$month = 1;
				$year++;
			}
		}
		$do = DB_MODEL::insert_any("schedules", $schedules);
		if ($do->error)
			error("terjadi kesalahan saat menyusun jadwal");
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
			"events_id" => post('events_id', "required"),
			"day" => post('day', "required"),
			"start" => post('start', "required"),
			"end" => post('end', "required"),
		);

		$where = array(
			"id" => post('id'),
		);

		$do = DB_MODEL::update($this->table, $where, $data);
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
