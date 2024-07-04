<?php

namespace App\Handler;

use App\Models\Absensi;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

//The class extends "ProcessWebhookJob" class as that is the class 
//that will handle the job of processing our webhook before we have 
//access to it.

class ProcessWebhook extends ProcessWebhookJob
{
    public function handle()
    {
        $dat = json_decode($this->webhookCall, true);
        $data = $dat['payload'];

        $original_data  = file_get_contents('php://input');
        $decoded_data   = json_decode($original_data, true);
        $encoded_data   = json_encode($decoded_data);

        if (isset($decoded_data['type']) and isset($decoded_data['cloud_id'])) {
            $type       = $decoded_data['type'];
            $cloud_id   = $decoded_data['cloud_id'];
            $created_at = date('Y-m-d H:i:s');

            $sql    = "INSERT INTO t_log (cloud_id,type,created_at,original_data) VALUES ('" . $cloud_id . "', '" . $type . "', '" . $created_at . "', '" . $encoded_data . "')";
            $result = mysqli_query($conn, $sql);


            Absensi::create([
                'pin' => $decoded_data->data->pin,
                'scan_date' => $decoded_data->data->scan_date,
                'jam_masuk' => $decoded_data->data->status_scan == 0 ? $decoded_data->data->scan_date : null,
                'jam_pulang' => $decoded_data->data->status_scan == 1 ? $decoded_data->data->scan_date : null,
                'verify' => $decoded_data->data->verify,
                'status_scan' => $decoded_data->data->status_scan,
                'tanggal_data' => date('Y-m-d')
            ]);
        }

        if ($data['event'] == 'charge.success') {
            // take action since the charge was success
            // Create order
            // Sed email
            // Whatever you want
            Log::info($data);
        }

        //Acknowledge you received the response
        http_response_code(200);
    }
}
