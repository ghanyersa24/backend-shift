<?php
defined('BASEPATH') or exit('No direct script access allowed');
class UPLOAD_FILE
{
	public static function img($post_name, $location = null, $file_name = null, $max_size = 2048)
	{
		return self::uploads('gif|jpg|png|JPG|jpeg', $post_name, $location, $file_name, $max_size);
	}

	public static function pdf($post_name, $location = null, $file_name = null, $max_size = 10000)
	{
		return self::uploads('pdf', $post_name, $location, $file_name, $max_size);
	}

	public static function word($post_name, $location = null, $file_name = null, $max_size = 10000)
	{
		return self::uploads('doc|docx', $post_name, $location, $file_name, $max_size);
	}

	public static function excel($post_name, $location = null, $file_name = null, $max_size = 10000)
	{
		return self::uploads('xls|xlsx', $post_name, $location, $file_name, $max_size);
	}

	public static function ppt($post_name, $location = null, $file_name = null, $max_size = 10000)
	{
		return self::uploads('ppt|pptx', $post_name, $location, $file_name, $max_size);
	}

	public static function doc($post_name, $location = null, $file_name = null, $max_size = 10000)
	{
		return self::uploads('pdf|doc|docx|xlx|xlxs|ppt|pptx', $post_name, $location, $file_name, $max_size);
	}

	public static function rar($post_name, $location = null, $file_name = null, $max_size = 10000)
	{
		return self::uploads('rar|zip', $post_name, $location, $file_name, $max_size);
	}
	public static function delete($input_name)
	{
		$location_old = post($input_name);
		$location_old = str_replace('%2F', '/', $location_old);
		$location_old = str_replace('%3A', ':', $location_old);
		if ($location_old != "" && !is_null($location_old))
			unlink(getcwd() . '/' . str_replace(base_url(), '/', $location_old));
	}

	public static function update($type, $post_name, $location = null,  $file_name = null, $max_size = 2048)
	{
		self::delete($post_name, 'required');
		return self::$type($post_name . '_new', $location,  $file_name);
	}

	private static function uploads($type, $post_name, $location = null,  $file_name = null, $max_size = 2048)
	{
		$CI = &get_instance();
		if (!is_null($location)) {
			$config['upload_path'] = "./uploads/$location";
			if (!file_exists("./uploads/$location")) {
				mkdir("./uploads/$location", 0777, true);
			}
		} else {
			$location = 'untitles';
			$config['upload_path'] = "./uploads/untitles";
		}
		if (!is_null($file_name))
			$config['file_name'] = $file_name . '_' . date('Y-m-d H i');
		else
			$config['encrypt_name'] = true;

		$config['allowed_types'] = $type;
		$config['max_size'] = $max_size;

		$CI->load->library('upload', $config);
		$CI->upload->initialize($config);

		$upload_status = $CI->upload->do_upload($post_name);
		$upload_message = strip_tags($CI->upload->display_errors());
		$upload_location = base_url() . "uploads/$location/" . $CI->upload->data("file_name");
		if ($upload_status)
			return $upload_location;
		else {
			$name = str_replace("_", " ", str_replace("new", "", $post_name));
			switch ($upload_message) {
				case 'The filetype you are attempting to upload is not allowed.':
					error($name . " hanya bisa menerima format " . str_replace("|", " .", $type));
					break;
				case 'The file you are attempting to upload is larger than the permitted size.':
					error($name . " hanya bisa menerima file dengan ukuran " . number_format($max_size / 1000) . ' MB');
					break;
				case 'You did not select a file to upload.':
					error($name . " wajib menyertakan file.");
					break;
				default:
					error("$name yang kamu kirim tidak sesuai dengan ketentuan. $upload_message");
					break;
			}
		}
	}
}
