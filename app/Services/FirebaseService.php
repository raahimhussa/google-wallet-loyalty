<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    private $client;
    private $serverKey;
    private $projectId;

    public function __construct()
    {
        $this->client = new Client();
        $this->serverKey = config('services.firebase.server_key');
        $this->projectId = config('services.firebase.project_id');
    }

    public function sendPushNotification($deviceTokens, $title, $message, $data = [])
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        
        $notification = [
            'title' => $title,
            'body' => $message,
            'sound' => 'default',
            'badge' => 1
        ];

        $fields = [
            'registration_ids' => is_array($deviceTokens) ? $deviceTokens : [$deviceTokens],
            'notification' => $notification,
            'data' => $data
        ];

        $headers = [
            'Authorization' => 'key=' . $this->serverKey,
            'Content-Type' => 'application/json'
        ];

        try {
            $response = $this->client->post($url, [
                'headers' => $headers,
                'json' => $fields
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            Log::info('FCM Response: ', $result);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('FCM Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendGeoNotification($deviceTokens, $title, $message, $latitude, $longitude, $radius = 100)
    {
        $data = [
            'type' => 'geo_notification',
            'latitude' => (string)$latitude,
            'longitude' => (string)$longitude,
            'radius' => (string)$radius,
            'trigger_on_entry' => 'true'
        ];

        return $this->sendPushNotification($deviceTokens, $title, $message, $data);
    }
}