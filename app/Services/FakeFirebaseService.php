<?php

namespace App\Services;

/**
 * A fake Firebase service for testing purposes.
 * This class simulates the behavior of the real FirebaseService
 * without making any actual API calls.
 */
class FakeFirebaseService extends FirebaseService
{
    public function __construct()
    {
        // Intentionally empty to prevent real service setup.
    }

    public function sendPushNotification($deviceTokens, $title, $message, $data = [])
    {
        // Simulate a successful FCM response.
        return [
            'success' => true,
            'message' => 'Fake push notification sent successfully.',
            'sent_to' => is_array($deviceTokens) ? $deviceTokens : [$deviceTokens],
            'title' => $title,
            'message' => $message,
        ];
    }

    public function sendGeoNotification($deviceTokens, $title, $message, $latitude, $longitude, $radius = 100)
    {
        // Simulate a successful FCM response for a geo-notification.
        return [
            'success' => true,
            'message' => 'Fake geo-notification sent successfully.',
            'sent_to' => is_array($deviceTokens) ? $deviceTokens : [$deviceTokens],
            'title' => $title,
            'message' => $message,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }
} 