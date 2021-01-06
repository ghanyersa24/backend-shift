<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Events extends CI_Controller
{
	protected $table = "events";
	public function __construct()
	{
		parent::__construct();
		AUTHORIZATION::User();
	}
	public function create()
	{
		$data = array(
			"event" => post('event', 'required'),
			"description" => post('description'),
			"img" => UPLOAD_FILE::img('img'),
			"caption" => post('caption', 'required'),
// 			"start_time" => post('start_time', 'date'),
// 			"end_time" => post('end_time', 'date'),
			"event_type" => post('event_type', 'enum:bootcamp&mastering&awarness'),
// 			"price" => post('price', 'rupiah|numeric'),
		);

		$do = DB_MODEL::insert($this->table, $data, false);
		if (!$do->error) {
// 			$act = $this->categories($do->data['id']);
// 			if (!$act->error)
				success("data " . $this->table . " berhasil ditambahkan", $do->data);
// 			error("terjadi kesalahan saat menambahkan kategori");
		} else
			error("data " . $this->table . " gagal ditambahkan");
	}

	private function categories($event_id)
	{
		DB_MODEL::delete('event_category', ['event_id' => $event_id]);
		$categories = post('categories');
		$data = [];
		foreach ($categories as  $category) {
			array_push($data, ['event_id' => $event_id, 'category_id' => $category]);
		}
		$do = DB_MODEL::insert_any('event_category', $data);
		return $do->error;
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
			"event" => post('event', 'required'),
			"description" => post('description'),
			"caption" => post('caption', 'required'),
// 			"start_time" => post('start_time', 'date'),
// 			"end_time" => post('end_time', 'date'),
			"event_type" => post('event_type', 'enum:bootcamp&mastering&awarness'),
// 			"price" => post('price', 'rupiah|numeric'),
		);
		if (isset($_FILES['img']))
			$data['img'] = UPLOAD_FILE::update('img', 'img');

		$where = array(
			"id" => post('id'),
		);

		$do = DB_MODEL::update($this->table, $where, $data);
		if (!$do->error) {
// 			$act = $this->categories($do->data['id']);
// 			if (!$act->error)
				success("data " . $this->table . " berhasil diubah", $do->data);
// 			error("terjadi kesalahan saat menambahkan kategori");
		} else
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
