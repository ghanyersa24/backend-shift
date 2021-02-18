<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DB_CUSTOM extends CI_Model
{
	public static function leads($event_id)
	{
		$CI = &get_instance();
		$query = $CI->db->select('leads.id, leads.users_id,users.name, users.email, users.phone, leads.execute_by, leads.events_id, leads.status status_action')
			->from('users')
			->join('leads', 'users.id=leads.users_id', 'right')
			->where(['events_id' => $event_id])
			->order_by("status_action", "asc")
			->get();
		if ($query)
			return true($query->result());
		else
			return false();
	}
	public static function users($where)
	{
		$CI = &get_instance();
		$query = $CI->db->select("users.*")
			->from("users")
			->join("leads", "users.id=leads.users_id", "left")
			->join("events", "leads.events_id=events.id", "left")
			->join("events_has_categories", "events.id=events_has_categories.events_id", "left")
			->group_by("users.id");

		if (isset($where['event_type'])) {
			$types = explode(",", $where['event_type']);
			for ($i = 0; $i < count($types); $i++) {
				$query = $query->where("events.event_type =", $types[$i]);
			}
		}

		if (isset($where['event'])) {
			$events = explode(",", $where['event']);
			for ($i = 0; $i < count($events); $i++) {
				$query = $query->or_where("events.id =", $events[$i]);
			}
		}

		if (isset($where['category'])) {
			$categories = explode(",", $where['category']);
			for ($i = 0; $i < count($categories); $i++) {
				$query = $query->or_where("events_has_categories.categories_id =", $categories[$i]);
			}
		}
		if (isset($where['event_id'])) {
			$event_id = $where["event_id"];
			$query = $query->where("users.id not in (select users_id from leads where events_id=$event_id)");
		}

		$query = $query->get();
		if ($query)
			return true($query->result());
		else
			return false();
	}

	public static function allHistory($user_id)
	{
		$CI = &get_instance();
		$query = $CI->db->select("leads.*,events.event,events.start_time,events.end_time,progress.nominal,progress.reason,progress.status status_progress")
			->from("progress")
			->join("leads", "progress.leads_id=leads.id")
			->join("events", "leads.events_id=events.id")
			->where(["leads.users_id" => $user_id])
			->get();
		if ($query)
			return true($query->result());
		else
			return false();
	}

	public static function is_paid($leads_id)
	{
		$CI = &get_instance();
		$query = $CI->db->select("COALESCE(SUM(nominal),0) as paid")->where(["leads_id" => $leads_id])->get("progress");
		return true($query->result()[0]->paid);
	}

	public static function report_event($event_id)
	{
		$CI = &get_instance();
		$query = $CI->db->select("status, COALESCE(count(id),0) as value")
			->where(["events_id" => $event_id])
			->group_by("status")
			->get("leads");
		return true($query->result());
	}

	public static function report_event_money($event_id)
	{
		$CI = &get_instance();
		$query = $CI->db->select("price*(select count(id) from leads where events_id=$event_id) total, sum(nominal) achive")
			->where(["leads.events_id" => $event_id])
			->from("progress")
			->join("leads", "progress.leads_id=leads.id")
			->join("events", "leads.events_id=events.id")
			->get();
		return true($query->row());
	}
}
