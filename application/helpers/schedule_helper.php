<?php
class SCHEDULE
{
    public static function DATE_LIST($month = 1, $year = 2020)
    {
        $list = array();
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month)
                $list[] = (object)[
                    "date" => date('Y-m-d', $time),
                    "day" => date('D', $time)
                ];
        }
        return $list;
    }

    public static function GENERATE($list, $sesi, $date_list, $event, $patterns)
    {
        $session = input_json("session");
        foreach ($date_list as $date) {
            if ($date->date >= date("Y-m-d", strtotime($event->start_time)) && $sesi <= $session) {
                foreach ($patterns as  $pattern) {
                    if ($sesi <= $session && $pattern["day"] == $date->day) {
                        $list[] = [
                            "id" => UUID::v4(),
                            "name" => "sesi " . $sesi,
                            "events_id" => $event->id,
                            "start" => $date->date . " " . $pattern["start"],
                            "end" => $date->date . " " . $pattern["end"],
                            "created_at" => date('Y-m-d H:i:s'),
                            "created_by" => AUTHORIZATION::User()->id,
                            "updated_at" => date('Y-m-d H:i:s'),
                            "updated_by" => AUTHORIZATION::User()->id,
                        ];
                        $sesi++;
                    }
                }
            }
        }
        return (object)["list" => $list, "sesi" => $sesi];
    }
}
