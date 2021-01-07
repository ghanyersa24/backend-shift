<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DB_CUSTOM extends CI_Model
{
	public static function leads($event_id)
	{
		$CI = &get_instance();
		$query = $CI->db->select('users.id,users.name,users.email,users.phone,leads.execute_by,leads.events_id,leads.status status_action')
			->from('users')
			->join('leads', 'users.id=leads.users_id', 'left')
			->where(['events_id' => $event_id])
			->or_where(['events_id' => null])
			->get();
		if ($query)
			return true($query->result());
		else
			return false();
	}
}
