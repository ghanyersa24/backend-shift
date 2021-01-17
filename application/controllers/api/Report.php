<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report extends CI_Controller
{
    public function get($event_id)
    {
        $do = DB_CUSTOM::report_event($event_id);
        if (!$do->error) {
            $moneytory = DB_CUSTOM::report_event_money($event_id);
            $data = [
                "reports" => $do->data,
                "moneytory" => $moneytory->data,
            ];
            success("data berhasil ditemukan", $data);
        } else
            error("data gagal ditemukan");
    }
}
