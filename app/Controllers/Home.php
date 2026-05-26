<?php

namespace App\Controllers;

use App\Models\BusSeatModel;

class Home extends BaseController
{
    public function index()
    {
        $seatModel = new BusSeatModel();

        $seats = $seatModel
            ->orderBy('seat_no', 'ASC')
            ->findAll();

        return view('home', [
            'title' => 'Bus Seat Booking',
            'seats' => $seats
        ]);
    }
}