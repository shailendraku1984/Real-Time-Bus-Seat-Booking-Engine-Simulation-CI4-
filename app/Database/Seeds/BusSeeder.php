<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BusSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('buses')->insert([
            'bus_name' => 'Delhi Express'
        ]);

        $busId = $this->db->insertID();

        $seats = [
            'A1', 'A2',
            'B1', 'B2',
            'C1', 'C2',
            'D1', 'D2'
        ];

        foreach ($seats as $seat) {

            $this->db->table('bus_seats')->insert([
                'bus_id' => $busId,
                'seat_no' => $seat,
                'status' => 'available'
            ]);
        }
    }
}
