<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Leads extends CI_Controller
{
	private $table = "leads";
	private $list_status = [
		"follow up",
		"diterima",
		"ditolak",
		"dibayar",
		"hadir",
	];
	public function __construct()
	{
		parent::__construct();
		// additional library
	}
	public function create()
	{
		$this->load->helper("uuid");
		$event = DB_MODEL::find("events", ["id" => $event_id = post("events_id", "required|numeric")]);
		if ($event->error)
			error("data event tidak ditemukan");
		$leads = explode(",", post("leads", "required"));
		for ($i = 0; $i < count($leads); $i++) {
			$this->insert_lead(["events_id" => $event_id, "users_id" => $leads[$i]]);
		}
		success("data leads berhasil ditambahkan", []);
	}
	private function process($lead_id, $idx = 0, $event_id = 0)
	{
		$where = ["id" => $lead_id];
		$data = ["execute_by" => AUTHORIZATION::User()->name, "status" => $this->list_status[$idx]];
		$update_lead = DB_MODEL::update("leads", $where, $data);
		if ($update_lead->error)
			error("terjadi kesalahan saat memperbarui status leads");
		$progress = [
			"leads_id" => $lead_id,
			"status" => $this->list_status[$idx],
			"reason" => post("reason")
		];
		if ($idx == 3) {
// 			$progress["file"] = UPLOAD_FILE::img('file', $event_id);
			$progress["nominal"] = post("nominal", "required|rupiah");
			$progress["status"] = "dibayar";
		}

		$add_progress = DB_MODEL::insert("progress", $progress);
		if ($add_progress->error)
			error("terjadi kesalahan saat menambahkan data progress");

		if ($idx == 3) {
			$paid = DB_CUSTOM::is_paid($lead_id);
			return $this->payment($paid->data, $event_id);
		}
		success("data leads berhasil diperbarui", []);
	}

	private function payment($paid, $event_id)
	{
		$event = DB_MODEL::find("events", ["id" => $event_id]);
		if ($event->error)
			error("event tidak ditemukan");
		if ((int)$paid >= (int)$event->data->price) {
			return "paid";
		}
		return "installment";
	}

	public function progress()
	{
		$status = post("status", "enum:" . implode("&", $this->list_status));
		$lead_id = post("id", "required");
		$check = DB_MODEL::find("leads", ["id" => $lead_id]);
		if ($check->error)
			error("data leads tidak ditemukan, pastikan sudah terdaftar");

		if ($status == $this->list_status[0]) {
			$this->process($lead_id);
		} elseif ($status == $this->list_status[1]) {
			$this->process($lead_id, 1);
		} elseif ($status == $this->list_status[2]) {
			$this->process($lead_id, 2);
		} else {
			if ($status == $this->list_status[3]) {
				post("nominal", "required|rupiah");
				$payment = $this->process($lead_id, 3, $check->data->events_id);
				if ($payment == "paid")
					$data = [
						"execute_by" => AUTHORIZATION::User()->name,
						"status" => "lunas"
					];
				else
					$data = [
						"execute_by" => AUTHORIZATION::User()->name,
						"status" => "dicicil"
					];
				$do = DB_MODEL::update_straight($this->table, ["id" => post("id", "required")], $data);
				if ($do->error)
					error("terjadi kesalahan saat mengubah data leads");
				success("pembayaran berhasil disimpan", []);
			}
		}
	}
	public function get_progress($leads_id)
	{
		$do = DB_MODEL::where("progress", ["leads_id" => $leads_id]);
		success("data " . $this->table . " berhasil ditemukan", $do->data);
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

	public function addLeads($event_id)
	{
		$_POST = json_decode(file_get_contents('php://input'), true);
		$leads = $_POST['leads'];
		for ($i = 0; $i < count($leads); $i++) {
			$lead = $leads[$i];
			$find = DB_MODEL::find("users", ["phone" => $lead["phone"], "email" => $lead["email"]]);
			if ($find->error) {
				$this->insert_user($lead, $event_id);
			} else {
				$this->insert_lead([
					"events_id" => $event_id,
					"users_id" => $find->data->id
				]);
			}
		}
		success("data berhasil ditambahkan", []);
	}

	private function insert_user($data, $event_id)
	{
		$do = DB_MODEL::insert("users", $data, false);
		if ($do->error)
			error("gagal saat menambahkan data users");
		$data = [
			"events_id" => $event_id,
			"users_id" => $do->data["id"]
		];
		return $this->insert_lead($data);
	}

	private function insert_lead($data)
	{
		$find = DB_MODEL::find($this->table, $data);
		if ($find->error) {
			$data["status"] = "daftar";
			$data["execute_by"] = AUTHORIZATION::User()->name;
			$do = DB_MODEL::insert($this->table, $data);
			if ($do->error)
				error("terjadi kesalahan saat input users");
			return $do->data;
		}else
		    return false;
	}

	public function upload()
	{
		$url = UPLOAD_FILE::csv("csv");
		$location = UPLOAD_FILE::getFileLocation($url);
		$file = fopen($location, "r");
		$data = [];
		$i = 0;
		while (!feof($file)) {
			$read = fgetcsv($file);
			if ($read && $i) {
				array_push($data, ["name" => $read[0], "email" => $read[1], "phone" => $read[2]]);
			}
			$i++;
		}
		fclose($file);
		UPLOAD_FILE::del($url);
		success("data dari csv", $data);
	}
	
	public function migrasi()
	{
		$url = UPLOAD_FILE::csv("csv");
		$location = UPLOAD_FILE::getFileLocation($url);
		$file = fopen($location, "r");
		$data = [];
		$i = 0;
		while (!feof($file)) {
			$read = fgetcsv($file);
			if ($read && $i) {
				array_push($data, ["name" => $read[0], "email" => $read[1], "phone" => $read[2], "events_id" => $read[3]]);
			}
			$i++;
		}
		fclose($file);
		UPLOAD_FILE::del($url);
		success("data dari csv", $data);
	}
	
	public function data_migrasi()
	{
		$_POST = json_decode(file_get_contents('php://input'), true);
		$leads = $_POST['leads'];
		for ($i = 0; $i < count($leads); $i++) {
			$lead = $leads[$i];
			$user = ["phone" => $lead["phone"], "email" => $lead["email"]];
			$find = DB_MODEL::find("users", $user);
			if ($find->error) {
			    $user["name"]=$lead["name"];
				$addLead=$this->insert_user($user, $lead["events_id"]);
			    $this->progress_migrate($addLead["id"]);
			} else {
				$addLead=$this->insert_lead([
					"events_id" => $lead["events_id"],
					"users_id" => $find->data->id,
				]);
				if($addLead)
			    $this->progress_migrate($addLead["id"]);
			}
		}
		success("data berhasil ditambahkan", []);
	}
	
	private function progress_migrate($lead_id){
	   $progress = [
			"leads_id" => $lead_id,
			"status" => $this->list_status[4]
		];
		DB_MODEL::insert("progress", $progress);
	}
	
}
