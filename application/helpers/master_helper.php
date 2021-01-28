<?php
defined('BASEPATH') or exit('No direct script access allowed');

function r($value)
{
	$search = array("'", '"');
	$replace = array(" ");
	if (is_null($value)) {
		return "";
	}
	return str_replace($search, $replace, $value);
}

// --------------- helper tanggal
function tgl_indo($tanggal)
{
	$bulan = array(
		1 =>
		'Jan',
		'Feb',
		'Mar',
		'Apr',
		'Mei',
		'Jun',
		'Jul',
		'Agt',
		'Sep',
		'Okt',
		'Nov',
		'Des'
	);
	$tgl = date('d-m-Y', strtotime($tanggal));
	$pecahkan = explode('-', $tgl);
	$waktu = array(
		'tanggal' => $pecahkan[2],
		'bulan' => $bulan[(int) $pecahkan[1]],
		'tahun' => $pecahkan[0],
		'jam' => date('H:i', strtotime($tanggal))
	);
	return $waktu;
}
function date_valid($params, $value)
{
	$params = underline($params);
	$check = strtotime($value);
	if ($check != false) {
		return date("Y-m-d H:i:s", strtotime($value));
	} else
		error("$params tidak sesuai dengan ketentuan YYYY-mm-dd");
}

function date_now($params, $value)
{
	$check = strtotime($value);
	$params = underline($params);
	if ($check != false) {
		if ($check > strtotime(date("Y-m-d")))
			return date("Y-m-d H:i:s", $check);
		else {
			error("waktu $params tidak bisa menggunakan waktu lampau");
		}
	} else
		error("$params tidak sesuai dengan ketentuan YYYY-mm-dd");
}

// --------------- helper inputan
function get($params, $constrains = null)
{
	$CI = &get_instance();
	return $CI->input->get($params);
}

function input_json($key)
{
	$_POST = json_decode(file_get_contents('php://input'), true);
	return $_POST[$key];
}

function post($params, $constrains = null)
{
	// $_POST= json_decode( file_get_contents('php://input'),true);
	if (isset($_POST[$params])) {
		$value = $_POST[$params];
		$value = str_replace('%3A', ':', $value);
		$value = str_replace('%2F', '/', $value);
		if (!is_null($constrains) && !empty($constrains)) {
			if (strpos($constrains, 'allow_html') !== 0)
				$value = strip_tags(r($value));
			$constrains = explode('|', $constrains);
			foreach ($constrains as $method) {
				if (strpos($method, ':')) {
					$tmp = explode(':', $method);
					$value = $tmp[0]($params, $value, $tmp[1]);
				} else
					$value = $method($params, $value);
			}
			return $value;
		} else
			return strip_tags(r($value));
	} else {
		$params = underline($params);
		error("data $params tidak ditemukan.");
	}
}

// --------------- constrains inputan
function enum($params, $value, $check)
{
	$message = underline($params);
	$check = explode("&", $check);
	if (in_array($value, $check))
		return $value;
	else
		error("data $message tidak sesuai ketentuan");
}

function same($params, $value, $check)
{
	if (post($check) !== $value) {
		$params = underline($params);
		$check = underline($check);
		error("$params tidak cocok dengan $check");
	}
	return $value;
}
function greater($params, $value, $check)
{
	$message = underline($params);
	$a = post($check);
	$b = $value;
	if (strtotime($a) != false || strtotime($b) != false) {
		$a = strtotime($a);
		$b = strtotime($b);
	}
	if ($b <= $a) {
		$check = underline($check);
		error("$message harus lebih besar dari $check");
	}
	return $value;
}

function max_char($params, $value, $check)
{
	$message = underline($params);
	if (strlen($value) > $check)
		error("$message tidak boleh lebih dari $check karakter");
	return $value;
}

function min_char($params, $value, $check)
{
	$message = underline($params);
	if (strlen($value) < $check)
		error("$message tidak boleh kurang dari $check karakter");
	return $value;
}

function max_value($params, $value, $check)
{
	$message = underline($params);
	if (is_numeric($value) && is_numeric($check)) {
		if ($value > $check)
			error("$message tidak boleh lebih dari $check");
	} else
		error("$message harus berisi angka");
	return $value;
}

function min_value($params, $value, $check)
{
	$message = underline($params);
	if (is_numeric($value) && is_numeric($check)) {
		if ($value < $check)
			error("$message tidak boleh kurang dari $check");
	} else
		error("$message harus berisi angka");
	return $value;
}

function unique($params, $value, $table)
{
	$message = underline($params);
	$found = DB_MASTER::find($table, array($params => $value));
	if (!$found->error)
		error("$message sudah digunakan");
	return $value;
}
function update_unique($params, $value, $table)
{
	$temp = explode("&", $table);
	$found = DB_MODEL::find($temp[0], array($params => $value, 'id !=' => $temp[1]));
	if (!$found->error)
		error("$params sudah digunakan");
	return $value;
}

function email($params, $value)
{
	$value = str_replace('%40', '@', $value);
	$message = underline($params);
	if (!filter_var($value, FILTER_VALIDATE_EMAIL))
		error("$message tidak valid sebagai email");
	return $value;
}

function required($params, $value)
{
	$message = underline($params);
	if (empty($value)) {
		$params = underline($params);
		error("data input $message tidak boleh kosong");
	}
	return $value;
}

function session($value)
{
	if (empty($value)) {
		error("pastikan kamu sudah login ya.");
	}
	return $value;
}
function rupiah($params, $value)
{
	$message = underline($params);
	if (substr($value, 0, 3) == "Rp.") {
		return str_replace(["Rp. ", "."], "", $value);
	} else
		error("data input $message harus berupa nilai rupiah.");
}
function set_rupiah($value)
{
	$hasil_rupiah = "Rp. " . number_format($value, 0, ',', '.');
	return $hasil_rupiah;
}

function numeric($params, $value)
{
	$message = underline($params);
	$value = str_replace(".", "", $value);
	if (!is_numeric($value)) {
		error("$message harus berupa angka");
	}
	return $value;
}
function allow_html($params, $value)
{
	return $value;
}

function underline($params)
{
	return str_replace("_", " ", $params);
}

// --------------- helper message json
function success($msg, $data)
{
	header('Content-Type: application/json');
	echo json_encode(
		array(
			"error" => false,
			"message" => $msg,
			"data" => $data
		)
	);
	exit;
}

function error($msg, $data = [])
{
	header('Content-Type: application/json');
	echo json_encode(
		array(
			"error" => true,
			"message" => $msg,
			"data" => $data
		)
	);
	exit;
}

// --------------- helper model
function true($data)
{
	return (object) array(
		"error" => false,
		"data" => $data
	);
}

function false()
{
	return (object) array(
		"error" => true
	);
}
