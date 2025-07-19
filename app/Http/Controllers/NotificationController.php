<?php

namespace App\Http\Controllers;

use App\Models\PushNotification;
use App\Models\LoyaltyCard;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function sendPushNotification(Request $request)
    {
        try {
            $cardId = $request->input('card_id');
            $title = $request->input('title', 'Loyalty Card Update');
            $message = $request->input('message', 'Your loyalty card has been updated');
            $deviceTokens = $request->input('device_tokens', []); // Array of FCM tokens
            
            $loyaltyCard = LoyaltyCard::where('card_id', $cardId)->firstOrFail();
            
            // Send notification
            $result = $this->firebaseService->sendPushNotification($deviceTokens, $title, $message, [
                'card_id' => $cardId,
                'type' => 'loyalty_update'
            ]);
            
            // Save notification record
            PushNotification::create([
                'user_id' => $loyaltyCard->user_id,
                'card_id' => $cardId,
                'type' => 'push',
                'title' => $title,
                'message' => $message,
                'is_sent' => true,
                'sent_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendGeoNotification(Request $request)
    {
        try {
            $cardId = $request->input('card_id');
            $title = $request->input('title', 'You\'re near a store!');
            $message = $request->input('message', 'Visit us to earn more points!');
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $radius = $request->input('radius', 100);
            $deviceTokens = $request->input('device_tokens', []);
            
            $loyaltyCard = LoyaltyCard::where('card_id', $cardId)->firstOrFail();
            
            // Send geo notification
            $result = $this->firebaseService->sendGeoNotification(
                $deviceTokens, 
                $title, 
                $message, 
                $latitude, 
                $longitude, 
                $radius
            );
            
            // Save notification record
            PushNotification::create([
                'user_id' => $loyaltyCard->user_id,
                'card_id' => $cardId,
                'type' => 'geo',
                'title' => $title,
                'message' => $message,
                'geo_data' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'radius' => $radius
                ],
                'is_sent' => true,
                'sent_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getNotifications($cardId)
    {
        try {
            $notifications = PushNotification::where('card_id', $cardId)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}