<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // additional library
    }
    public function report()
    {
        $this->load->model("DB_DASHBOARD");
        $do = DB_DASHBOARD::report();
        if ($do->error)
            error("terjadi kesalahan pada sistem");
        $data = $this->parse_report_dashboard($do->data);
        success("data dashboard berhasil diterima", $data);
    }
    private function parse_report_dashboard($data)
    {
        $json = [];
        $total_leads = 0;
        $total_revenue = 0;
        foreach ($data as $row) {
            $total_leads += $row->leads;
            $total_revenue += $row->revenue;
            $idx_month = date("m", strtotime($row->bulan)) - 1;

            // NAME MONTH
            $json[$idx_month]["month"] = date("F", strtotime($row->bulan));
            $json[$idx_month]["month_idx"] = date("m", strtotime($row->bulan))-1;
            // REVENUE MONTH
            if (!isset($json[$idx_month]["revenue"]))
                $json[$idx_month]["revenue"] = $row->revenue;
            else
                $json[$idx_month]["revenue"] += $row->revenue;

            // LEADS MONTH
            if (!isset($json[$idx_month]["leads"]))
                $json[$idx_month]["leads"] = $row->leads;
            else
                $json[$idx_month]["leads"] += $row->leads;

            $json[$idx_month]["data"][] = $row;
        }
        return ["total_leads" => $total_leads, "total_revenue" => set_rupiah($total_revenue), "report" => (array)$json];
    }
}
