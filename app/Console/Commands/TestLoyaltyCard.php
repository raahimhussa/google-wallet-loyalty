<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleWalletService;
use App\Services\FirebaseService;
use App\Models\LoyaltyCard;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestLoyaltyCard extends Command
{
    protected $signature = 'loyalty:test';
    protected $description = 'Test loyalty card creation and notification';

    public function handle()
    {
        $this->info('Testing Google Wallet Loyalty Card Integration...');
        
        try {
            $googleWalletService = new GoogleWalletService();
            $firebaseService = new FirebaseService();
            
            // Create test card
            $cardId = 'TEST_' . Str::upper(Str::random(8));
            $classId = 'TEST_CLASS_' . config('services.google_wallet.issuer_id');
            $objectId = 'TEST_OBJECT_' . Str::upper(Str::random(12));
            $cardNumber = 'TC' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            $this->info('Creating loyalty class...');
            $googleWalletService->createLoyaltyClass($classId);
            
            $this->info('Creating loyalty object...');
            $userData = [
                'user_name' => 'Test User',
                'points' => 50
            ];
            
            $googleWalletObject = $googleWalletService->createLoyaltyObject($classId, $objectId, $userData);
            
            $this->info('Saving to database...');
            $loyaltyCard = LoyaltyCard::create([
                'user_id' => 'test_user',
                'google_wallet_object_id' => $objectId,
                'account_name' => 'Test User',
                'points' => 50,
                'status' => 'active',
            ]);
            
            $this->info('Generating save link...');
            $saveLink = $googleWalletService->generateSaveLink($objectId);
            
            $this->info('✅ Test completed successfully!');
            $this->info('Card ID: ' . $cardId);
            $this->info('Card Number: ' . $cardNumber);
            $this->info('Save Link: ' . $saveLink);
            
        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
        }
    }
}