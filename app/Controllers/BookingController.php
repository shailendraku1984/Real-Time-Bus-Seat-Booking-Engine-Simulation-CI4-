<?php

namespace App\Controllers;

use Exception;

class BookingController extends BaseController
{
    public function bookSeat()
    {
        $seatId = $this->request->getPost('seat_id');

        /*
        |--------------------------------------------------------------------------
        | REQUEST ID
        |--------------------------------------------------------------------------
        */

        $requestId = uniqid('REQ-');

        /*
        |--------------------------------------------------------------------------
        | REQUEST RECEIVED LOG
        |--------------------------------------------------------------------------
        */

        booking_log([
            'request_id' => $requestId,
            'seat_id'    => $seatId,
            'event'      => 'Request received'
        ]);

        /*
        |--------------------------------------------------------------------------
        | VALIDATE SEAT ID
        |--------------------------------------------------------------------------
        */

        if (!$seatId) {

            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Seat ID missing'
            ]);
        }

        $db = \Config\Database::connect();

        try {

            /*
            |--------------------------------------------------------------------------
            | START TRANSACTION
            |--------------------------------------------------------------------------
            */

            $db->transBegin();

            booking_log([
                'request_id' => $requestId,
                'seat_id'    => $seatId,
                'event'      => 'Transaction started'
            ]);

            /*
            |--------------------------------------------------------------------------
            | WAITING FOR ROW LOCK
            |--------------------------------------------------------------------------
            */

            booking_log([
                'request_id' => $requestId,
                'seat_id'    => $seatId,
                'event'      => 'Waiting for row lock'
            ]);

            /*
            |--------------------------------------------------------------------------
            | LOCK SEAT ROW
            |--------------------------------------------------------------------------
            |
            | FOR UPDATE creates row-level lock
            |
            */

            $seat = $db->query(
                "
                SELECT *
                FROM bus_seats
                WHERE id = ?
                FOR UPDATE
                ",
                [$seatId]
            )->getRowArray();

            /*
            |--------------------------------------------------------------------------
            | ROW LOCK ACQUIRED
            |--------------------------------------------------------------------------
            */

            booking_log([
                'request_id' => $requestId,
                'seat_id'    => $seatId,
                'event'      => 'Row lock acquired'
            ]);

              

            /*
            |--------------------------------------------------------------------------
            | SEAT NOT FOUND
            |--------------------------------------------------------------------------
            */

            if (!$seat) {

                booking_log([
                    'request_id' => $requestId,
                    'seat_id'    => $seatId,
                    'event'      => 'Seat not found'
                ]);

                $db->transRollback();

                booking_log([
                    'request_id' => $requestId,
                    'seat_id'    => $seatId,
                    'event'      => 'Transaction rolled back'
                ]);

                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Seat not found'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | RE-CHECK STATUS INSIDE TRANSACTION
            |--------------------------------------------------------------------------
            */

            if ($seat['status'] === 'booked') {

                booking_log([
                    'request_id' => $requestId,
                    'seat_id'    => $seatId,
                    'event'      => 'Seat already booked'
                ]);

                $db->transRollback();

                booking_log([
                    'request_id' => $requestId,
                    'seat_id'    => $seatId,
                    'event'      => 'Transaction rolled back'
                ]);

                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Seat already booked'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE SEAT STATUS
            |--------------------------------------------------------------------------
            */

            $db->table('bus_seats')
                ->where('id', $seatId)
                ->update([
                    'status' => 'booked'
                ]);

            booking_log([
                'request_id' => $requestId,
                'seat_id'    => $seatId,
                'event'      => 'Seat marked as booked'
            ]);
			

            /*
            |--------------------------------------------------------------------------
            | CREATE BOOKING
            |--------------------------------------------------------------------------
            */

            $bookingNo = 'BOOK-' . time();

            $db->table('bookings')
                ->insert([
                    'booking_no'   => $bookingNo,
                    'customer_name'=> 'Demo Customer',
                    'status'       => 'confirmed',
                    'created_at'   => date('Y-m-d H:i:s')
                ]);

            $bookingId = $db->insertID();

            booking_log([
                'request_id' => $requestId,
                'seat_id'    => $seatId,
                'event'      => 'Booking record created'
            ]);

            /*
            |--------------------------------------------------------------------------
            | CREATE BOOKING ITEM
            |--------------------------------------------------------------------------
            */

            $db->table('booking_items')
                ->insert([
                    'booking_id' => $bookingId,
                    'seat_id'    => $seatId,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

            booking_log([
                'request_id' => $requestId,
                'seat_id'    => $seatId,
                'event'      => 'Booking item created'
            ]);

            /*
            |--------------------------------------------------------------------------
            | BOOKING SUCCESS
            |--------------------------------------------------------------------------
            */

            booking_log([
                'request_id' => $requestId,
                'seat_id'    => $seatId,
                'event'      => 'Booking successful'
            ]);

            /*
            |--------------------------------------------------------------------------
            | COMMIT TRANSACTION
            |--------------------------------------------------------------------------
            */

            $db->transCommit();

            booking_log([
                'request_id' => $requestId,
                'seat_id'    => $seatId,
                'event'      => 'Transaction committed'
            ]);

            /*
            |--------------------------------------------------------------------------
            | SUCCESS RESPONSE
            |--------------------------------------------------------------------------
            */

            return $this->response->setJSON([
                'status'      => true,
                'message'     => 'Seat booked successfully',
                'booking_no'  => $bookingNo,
                'request_id'  => $requestId
            ]);

        } catch (Exception $e) {

            /*
            |--------------------------------------------------------------------------
            | ROLLBACK TRANSACTION
            |--------------------------------------------------------------------------
            */

            $db->transRollback();

            booking_log([
                'request_id' => $requestId,
                'seat_id'    => $seatId,
                'event'      => 'Transaction rolled back'
            ]);

            booking_log([
                'request_id' => $requestId,
                'seat_id'    => $seatId,
                'event'      => 'Exception: ' . $e->getMessage()
            ]);

            /*
            |--------------------------------------------------------------------------
            | ERROR RESPONSE
            |--------------------------------------------------------------------------
            */

            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Booking failed',
                'error'   => $e->getMessage()
            ]);
        }
    }
}