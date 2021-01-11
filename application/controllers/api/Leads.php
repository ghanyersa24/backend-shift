<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Leads extends CI_Controller
{
	protected $table = "leads";
	public function __construct()
	{
		parent::__construct();
		// additional library
	}
	public function create()
	{
		$do = DB_MODEL::find($this->table, [
			"events_id" => post('events_id', 'required|numeric'),
			"users_id" => post("users_id", "required|numeric")
		]);
		if (!$do->error)
			error("data sudah tersedia");
		$data = [
			"events_id" => post('events_id', 'required|numeric'),
			"users_id" => post("users_id", "required|numeric"),
			"status" => "menunggu",
			"execute_by" => post("name", "required")
		];
		$do = DB_MODEL::insert($this->table, $data);
		if (!$do->error) {
			$data = [
				"leads_id" => $do->data['id'],
				"status" => "menghubungi",
			];
			$progress = DB_MODEL::insert('progress', $data);
			if (!$progress->error)
				success("data " . $this->table . " berhasil ditemukan", $do->data);
			error("something error in progress");
		} else
			error("data " . $this->table . " gagal ditemukan");
	}

	public function progress()
	{
		$status = post("status", "enum:menunggu&ditolak&dibayar");
		if ($status == "dibayar") {
			$data = [
				"file" => UPLOAD_FILE::img('file'),
				"execute_by" => post("name", "required"),
				"status" => $status
			];
			$do = DB_MODEL::update_straight($this->table, ["id" => post("id", "required")], $data);
			if (!$do->error) {
				$data = [
					"leads_id" => post("id", "required"),
					"status" => $status,
				];
				$do = DB_MODEL::insert('progress', $data);
				if (!$do->error)
					success("data " . $this->table . " berhasil ditemukan", $do->data);
				error("something error in progress");
			}
		} else {
			if ($status == "ditolak") {
				$data = [
					"execute_by" => post("name", "required"),
					"status" => $status
				];
				$do = DB_MODEL::update($this->table, ["id" => post("id", "required")], $data);
				if ($do->error)
					error("something error in progress");
			}
			$data = [
				"leads_id" => post("id", "required"),
				"status" => $status,
				"reason" => post("reason")
			];
			$do = DB_MODEL::insert('progress', $data);
			if (!$do->error)
				success("data " . $this->table . " berhasil ditemukan", $do->data);
			error("something error in progress");
		}
	}

	public function get($eventId)
	{
		$do = DB_CUSTOM::leads($eventId);
		if (!$do->error)
			success("data " . $this->table . " berhasil ditemukan", $do->data);
		else
			error("data " . $this->table . " gagal ditemukan");
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
