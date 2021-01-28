<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Events extends CI_Controller
{
	protected $table = "events";
	public function __construct()
	{
		parent::__construct();
		// AUTHORIZATION::User();
	}
	private function random_color_part()
	{
		return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
	}

	private function random_color()
	{
		return "#" . $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
	}
	public function create()
	{
		$data = array(
			"event" => post('event', 'required'),
			"description" => post('description'),
			"caption" => post('caption', 'required'),
			"start_time" => post('start_time', 'date_valid'),
			"end_time" => post('end_time', 'date_valid'),
			"event_type" => post('event_type', 'enum:Bootcamp&Mastering&SA Talk'),
			"price" => post('price', 'rupiah|numeric'),
			"color" => $this->random_color(),
			"img" => UPLOAD_FILE::img('img'),
		);

		$do = DB_MASTER::insert($this->table, $data, false);
		if (!$do->error) {
			$act = $this->categories($do->data['id']);
			if (!$act->error)
				success("data " . $this->table . " berhasil ditambahkan", $do->data);
			error("terjadi kesalahan saat menambahkan kategori");
		} else
			error("data " . $this->table . " gagal ditambahkan");
	}

	private function categories($event_id)
	{
		DB_MODEL::delete('events_has_categories', ['events_id' => $event_id]);
		$categories = post('categories');
		$categories = explode(",", $categories);
		$data = [];
		foreach ($categories as  $category) {
			array_push($data, [
				'id' => UUID::v4(),
				'events_id' => $event_id,
				'categories_id' => $category,
				'created_at' => date('Y-m-d H:i:s'),
				'created_by' => AUTHORIZATION::User()->id
			]);
		}
		$do = DB_MODEL::insert_any('events_has_categories', $data);
		return $do;
	}

	public function get($id = null)
	{
		$select = "events.*,(
			SELECT
				GROUP_CONCAT(`events_has_categories`.`categories_id`)
			FROM
				`events_has_categories`
			WHERE
				`events_has_categories`.`events_id` = `events`.`id` AND `events_has_categories`.`deleted`=0
		) categories";
		if (is_null($id)) {
			$do = DB_MODEL::all($this->table, $select);
			if (!$do->error) {
				foreach ($do->data as $data) {
					$data->categories = explode(",", $data->categories);
				}
			}
		} else {
			$do = DB_MODEL::find($this->table, array("id" => $id), $select);
			if (!$do->error) {
				$do->data->categories = explode(",", $do->data->categories);
			}
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
			"start_time" => post('start_time', 'date_valid'),
			"end_time" => post('end_time', 'date_valid'),
			"event_type" => post('event_type', 'enum:Bootcamp&Mastering&SA Talk'),
			"price" => post('price', 'rupiah|numeric'),
		);
		if (isset($_FILES['img']))
			$data['img'] = UPLOAD_FILE::update('img', 'img');

		$where = array(
			"id" => $id = post('id', "required"),
		);

		$do = DB_MODEL::update($this->table, $where, $data);
		if (!$do->error) {
			$act = $this->categories($id);
			if (!$act->error)
				success("data " . $this->table . " berhasil diubah", $do->data);
			error("terjadi kesalahan saat menambahkan kategori");
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
