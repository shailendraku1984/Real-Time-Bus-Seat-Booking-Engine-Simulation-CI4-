<?php

namespace App\Models;

use CodeIgniter\Model;

class BusSeatModel extends Model
{
    protected $table = 'bus_seats';

    protected $allowedFields = [
        'bus_id',
        'seat_no',
        'status'
    ];
}