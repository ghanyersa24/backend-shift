<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DB_DASHBOARD extends CI_Model
{
	public static function report()
	{
		$CI = &get_instance();
		$query = $CI->db->select("	events.id,
									events.event, 
									date_format(events.start_time,'%D') as start_time,
									date_format(events.start_time,'%M') as bulan, 
									events.price,
									events.price*(select get_leads(events.id)) as revenue,
									(select get_leads(events.id)) leads",)
			->from("events")
			->group_by("events.id")
			->order_by("events.created_at desc")
			->get();
		if ($query)
			return true($query->result());
		else
			return false();
	}
}
