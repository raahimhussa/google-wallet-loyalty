<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyCard;
use App\Services\GoogleWalletService;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LoyaltyCardController extends Controller
{
    private $googleWalletService;
    private $firebaseService;

    public function __construct(GoogleWalletService $googleWalletService, FirebaseService $firebaseService)
    {
        $this->googleWalletService = $googleWalletService;
        $this->firebaseService = $firebaseService;
    }

    public function createCard(Request $request)
    {
        try {
            $userId = $request->input('user_id', 'demo_user_' . Str::random(6));
            $userName = $request->input('user_name', 'Demo User');
            
            // Generate unique IDs
            $cardId = 'CARD_' . Str::upper(Str::random(8));
            $classId = 'LOYALTY_CLASS_' . config('services.google_wallet.issuer_id');
            $objectId = 'LOYALTY_OBJECT_' . Str::upper(Str::random(12));
            $cardNumber = 'LC' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            // Create Google Wallet class (if not exists)
            $this->googleWalletService->createLoyaltyClass($classId);
            
            // Create loyalty card object
            $userData = [
                'user_name' => $userName,
                'card_number' => $cardNumber,
                'points' => 0
            ];
            
            $googleWalletObject = $this->googleWalletService->createLoyaltyObject($classId, $objectId, $userData);
            
            // Save to database
            $loyaltyCard = LoyaltyCard::create([
                'user_id' => $userId,
                'card_id' => $cardId,
                'google_wallet_object_id' => $objectId,
                'card_number' => $cardNumber,
                'points' => 0,
                'metadata' => [
                    'user_name' => $userName,
                    'class_id' => $classId
                ],
                'issued_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addYear(),
                'is_active' => true
            ]);
            
            // Generate save link
            $saveLink = $this->googleWalletService->generateSaveLink($objectId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'card' => $loyaltyCard,
                    'save_link' => $saveLink,
                    'google_wallet_object' => $googleWalletObject
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePoints(Request $request, $cardId)
    {
        try {
            $loyaltyCard = LoyaltyCard::where('card_id', $cardId)->firstOrFail();
            $newPoints = $request->input('points', 0);
            
            // Update in database
            $loyaltyCard->update(['points' => $newPoints]);
            
            // Update in Google Wallet
            $this->googleWalletService->updateLoyaltyObject($loyaltyCard->google_wallet_object_id, $newPoints);
            
            return response()->json([
                'success' => true,
                'data' => $loyaltyCard->fresh()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getCard($cardId)
    {
        try {
            $loyaltyCard = LoyaltyCard::where('card_id', $cardId)->firstOrFail();
            
            return response()->json([
                'success' => true,
                'data' => $loyaltyCard
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Card not found'
            ], 404);
        }
    }

    public function listCards()
    {
        try {
            $cards = LoyaltyCard::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $cards
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}