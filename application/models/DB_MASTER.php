<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DB_MASTER extends CI_Model
{

	public static function all($table, $select = '*')
	{
		$CI = &get_instance();
		return true($CI->db->select($select)->order_by("created_at", 'DESC')->get($table)->result());
	}

	public static function where($table, $where, $select = '*')
	{
		$CI = &get_instance();
		$query = $CI->db->select($select)->where($where)->order_by("created_at", 'DESC')->get($table);
		if ($query)
			return true($query->result());
		else
			return false();
	}

	public static function like($table, $where, $like, $select = '*')
	{
		$CI = &get_instance();
		$query = $CI->db->select($select)->where($where)->like($like)->order_by("created_at", 'DESC')->get($table);
		if ($query)
			return true($query->result());
		else
			return false();
	}

	public static function limit($table, $limit, $select = '*')
	{
		$CI = &get_instance();
		return true($CI->db->select($select)->limit($limit)->order_by("created_at", 'DESC')->get($table)->result());
	}

	public static function find($table, $where, $select = '*')
	{
		$CI = &get_instance();
		$query = $CI->db->select($select)->where($where)->limit(1)->order_by("created_at", 'DESC')->get($table);
		if ($CI->db->affected_rows() !== 0)
			return true($query->row());
		else
			return false();
	}

	public static function insert($table, $data, $UUID = true)
	{
		$CI = &get_instance();
		if ($UUID)
			$CI->db->set('id', 'UUID()', FALSE);
		$data['created_at'] = date('Y-m-d H:i:s');
		$query = $CI->db->insert($table, $data);
		if ($query) {
			$id = $CI->db->insert_id();
			if ($id != 0)
				$data['id'] = $id;
			return true($data);
		} else
			return false();
	}

	public static function insert_any($table, $data)
	{
		$CI = &get_instance();
		$query = $CI->db->insert_batch($table, $data);
		if ($query)
			return true($query);
		else
			return false();
	}

	public static function update($table, $where, $data)
	{
		$CI = &get_instance();
		if (is_array($where))
			return true(array_merge($where, $data));
		else
			return true($data);
	}

	public static function update_straight($table, $where, $data)
	{
		$CI = &get_instance();
		$query = $CI->db->where($where)->update($table, $data);
		if ($CI->db->affected_rows() !== 0)
			if (is_array($where))
				return true(array_merge($where, $data));
			else
				return true($data);
		else
			return false();
	}

	public static function delete($table, $where)
	{
		$CI = &get_instance();
		$query = $CI->db->where($where)->delete($table);
		if ($CI->db->affected_rows() !== 0)
			return true($query);
		else
			return false();
	}

	public static function join($table_join, $to_table,  $on = null, $type = 'inner', $where = [], $select = '*')
	{
		$CI = &get_instance();
		if (is_null($on))
			$query = $CI->db->select($select)->from($to_table)->where($where)->join($table_join, $table_join . '.' . $to_table . '_id = ' . $to_table . '.id', $type)->get();
		else
			$query = $CI->db->select($select)->from($to_table)->where($where)->join($table_join, $on, $type)->get();
		if ($query)
			return true($query->result());
		else
			return false();
	}
}

/* End of file db_model.php */
/* Location: ./application/models/db_model.php */
