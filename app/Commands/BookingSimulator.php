<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BookingSimulator extends BaseCommand
{
    protected $group = 'Simulation';

    protected $name = 'simulate:booking';

    protected $description =
        'Simulate concurrent seat booking requests';

    public function run(array $params)
    {
        /*
        |--------------------------------------------------------------------------
        | PARAMETERS
        |--------------------------------------------------------------------------
        */

        $seatId = $params[0] ?? null;

        $totalRequests = $params[1] ?? 10;

        if (!$seatId) {

            CLI::error(
                'Seat ID required'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | API URL
        |--------------------------------------------------------------------------
        */

        $url =
            'http://localhost/bus_booking/public/book-seat';

        CLI::write('');
        CLI::write(
            'Starting concurrent booking simulation...',
            'yellow'
        );

        CLI::write(
            "Seat ID: {$seatId}",
            'green'
        );

        CLI::write(
            "Concurrent Requests: {$totalRequests}",
            'green'
        );

        CLI::write('');

        /*
        |--------------------------------------------------------------------------
        | MULTI CURL
        |--------------------------------------------------------------------------
        */

        $multiHandle = curl_multi_init();

        $curlHandles = [];

        for ($i = 1; $i <= $totalRequests; $i++) {

            $ch = curl_init();

            curl_setopt_array($ch, [

                CURLOPT_URL => $url,

                CURLOPT_POST => true,

                CURLOPT_POSTFIELDS => http_build_query([
                    'seat_id' => $seatId
                ]),

                CURLOPT_RETURNTRANSFER => true,

                CURLOPT_TIMEOUT => 30

            ]);

            curl_multi_add_handle(
                $multiHandle,
                $ch
            );

            $curlHandles[$i] = $ch;
        }

        /*
        |--------------------------------------------------------------------------
        | EXECUTE CONCURRENTLY
        |--------------------------------------------------------------------------
        */

        $running = null;

        do {

            curl_multi_exec(
                $multiHandle,
                $running
            );

        } while ($running);

        /*
        |--------------------------------------------------------------------------
        | RESULTS
        |--------------------------------------------------------------------------
        */

        $success = 0;
        $failed = 0;

        CLI::write('');
        CLI::write(
            '========= RESULTS =========',
            'yellow'
        );

        foreach ($curlHandles as $index => $ch) {

            $response =
                curl_multi_getcontent($ch);

            $data =
                json_decode($response, true);

            if (
                isset($data['status'])
                &&
                $data['status']
            ) {

                $success++;

                CLI::write(
                    "Request {$index} => SUCCESS",
                    'green'
                );

            } else {

                $failed++;

                $message =
                    $data['message']
                    ?? 'Failed';

                CLI::write(
                    "Request {$index} => FAILED ({$message})",
                    'red'
                );
            }

            curl_multi_remove_handle(
                $multiHandle,
                $ch
            );

            curl_close($ch);
        }

        curl_multi_close($multiHandle);

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        CLI::write('');
        CLI::write(
            '========= SUMMARY =========',
            'yellow'
        );

        CLI::write(
            "Success: {$success}",
            'green'
        );

        CLI::write(
            "Failed: {$failed}",
            'red'
        );

        CLI::write('');
        CLI::write(
            'Simulation completed.',
            'yellow'
        );
    }
}