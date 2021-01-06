<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Login extends CI_Controller
{
	public function index()
	{
		$email = post('email', 'required');
		$data = Auth::login('users', ['email'=>$email], post('password', 'required'));
			$data->token = AUTHORIZATION::generateToken($data);
			success("Welcome to Administrator's system", $data);
	}
}
