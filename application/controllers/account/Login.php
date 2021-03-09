<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Login extends CI_Controller
{
	public function index()
	{
		$_POST = json_decode(file_get_contents('php://input'), true);
		$email = $_POST['email'];
		$password = $_POST['password'];
		if (!$email || !$password)
			error("email dan password tidak boleh kosong");
		$data = Auth::login('u1002933_lms.users', ['email' => $email], $password);
		$data->token = AUTHORIZATION::generateToken($data);
		success("Welcome to Administrator's system", $data);
	}
}
