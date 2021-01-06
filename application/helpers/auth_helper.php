<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Auth
{
	public static function login($table, $where_auth, $password)
	{
		$find = DB_MODEL::find($table, $where_auth);
		if ($find->error)
			error("username and password isn't match");
		else {
			if (password_verify($password, $find->data->password)) {
				$find->data->token = AUTHORIZATION::generateToken($find->data);
				return $find->data;
			} else
				error("username and password isn't match");
		}
	}
}
