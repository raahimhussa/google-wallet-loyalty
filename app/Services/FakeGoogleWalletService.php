<?php
<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * A fake Google Wallet service for testing purposes.
 * This class simulates the behavior of the real GoogleWalletService
 * without making any actual API calls.
 */
class FakeGoogleWalletService extends GoogleWalletService
{
    public function __construct()
    {
        // This constructor is intentionally empty to prevent the real service's
        // constructor from running and trying to connect to Google.
    }

    public function createLoyaltyClass($classId)
    {
        // Simulate creating a class and return a dummy response.
        return [
            'id' => $classId,
            'message' => 'Fake loyalty class created successfully.'
        ];
    }

    public function createLoyaltyObject($classId, $objectId, $userData)
    {
        // Simulate creating an object and return a dummy response.
        return [
            'id' => $objectId,
            'classId' => $classId,
            'state' => 'ACTIVE',
            'herojim' => [],
            'textModulesData' => [],
            'linksModuleData' => [],
            'imageModulesData' => [],
            'barcode' => [
                'type' => 'QR_CODE',
                'value' => 'fake-qr-code-' . Str::random(10),
            ],
            'locations' => [],
            'accountId' => 'fake-account-id',
            'accountName' => $userData['user_name'] ?? 'Fake User',
        ];
    }

    public function updateLoyaltyObject($objectId, $newPoints)
    {
        // Simulate updating an object and return a dummy response.
        return [
            'id' => $objectId,
            'points' => $newPoints,
            'message' => 'Fake loyalty object updated successfully.'
        ];
    }

    public function generateSaveLink($objectId)
    {
        // Simulate generating a save link.
        return 'https://wallet.google.com/wob/fake/' . $objectId;
    }
} 