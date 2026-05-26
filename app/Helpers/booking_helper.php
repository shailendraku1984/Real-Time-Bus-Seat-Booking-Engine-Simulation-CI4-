<?php

if (!function_exists('booking_log')) {

    function booking_log($data)
    {
        $db = \Config\Database::connect();

        $db->table('booking_logs')->insert([

            'request_id' => $data['request_id'] ?? null,

            'seat_id' => $data['seat_id'] ?? null,

            'event' => $data['event'] ?? null,

            'log_time' => date('Y-m-d H:i:s.u'),

            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}