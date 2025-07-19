<?php

namespace App\Services;

use Google\Client;
use Google\Service\Walletobjects;
use Google\Service\Walletobjects\LoyaltyClass;
use Google\Service\Walletobjects\LoyaltyObject;
use Google\Service\Walletobjects\Image;
use Google\Service\Walletobjects\ImageUri;
use Google\Service\Walletobjects\LocalizedString;
use Google\Service\Walletobjects\TranslatedString;
use Google\Service\Walletobjects\LoyaltyPoints;
use Google\Service\Walletobjects\LoyaltyPointsBalance;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;

class GoogleWalletService
{
    private $client;
    private $service;
    private $issuerId;

    public function __construct()
    {
        $this->issuerId = config('services.google_wallet.issuer_id');
        $this->initializeClient();
    }

    private function initializeClient()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/google-wallet-service-account.json'));
        $this->client->addScope('https://www.googleapis.com/auth/wallet_object.issuer');
        $this->service = new Walletobjects($this->client);
    }

    public function createLoyaltyClass($classId)
    {
        $loyaltyClass = new LoyaltyClass();
        $loyaltyClass->setId($this->issuerId . '.' . $classId);
        
        // Set program name
        $programName = new LocalizedString();
        $programName->setDefaultValue(new TranslatedString([
            'language' => 'en',
            'value' => 'Demo Loyalty Program'
        ]));
        $loyaltyClass->setProgramName($programName);
        
        // Set program logo
        $programLogo = new Image();
        $programLogo->setSourceUri(new ImageUri([
            'uri' => 'https://via.placeholder.com/200x200?text=LOGO'
        ]));
        $loyaltyClass->setProgramLogo($programLogo);
        
        // Set issuer name
        $issuerName = new LocalizedString();
        $issuerName->setDefaultValue(new TranslatedString([
            'language' => 'en',
            'value' => 'Demo Store'
        ]));
        $loyaltyClass->setIssuerName($issuerName);
        
        // Set review status
        $loyaltyClass->setReviewStatus('UNDER_REVIEW');
        
        try {
            $response = $this->service->loyaltyclass->insert($loyaltyClass);
            return $response;
        } catch (\Exception $e) {
            // Class might already exist
            if (strpos($e->getMessage(), 'already exists') !== false) {
                return $this->service->loyaltyclass->get($this->issuerId . '.' . $classId);
            }
            throw $e;
        }
    }

    public function createLoyaltyObject($classId, $objectId, $userData)
    {
        $loyaltyObject = new LoyaltyObject();
        $loyaltyObject->setId($this->issuerId . '.' . $objectId);
        $loyaltyObject->setClassId($this->issuerId . '.' . $classId);
        
        // Set account ID
        $loyaltyObject->setAccountId($userData['card_number']);
        
        // Set account name
        $loyaltyObject->setAccountName($userData['user_name'] ?? 'Loyalty Member');
        
        // Set loyalty points
        $loyaltyPoints = new LoyaltyPoints();
        $loyaltyPoints->setLabel('Points');
        $balance = new LoyaltyPointsBalance();
        $balance->setString($userData['points'] . ' pts');
        $loyaltyPoints->setBalance($balance);
        $loyaltyObject->setLoyaltyPoints($loyaltyPoints);
        
        // Set state
        $loyaltyObject->setState('ACTIVE');
        
        try {
            $response = $this->service->loyaltyobject->insert($loyaltyObject);
            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function generateSaveLink($objectId)
    {
        $payload = [
            'iss' => config('services.google_wallet.service_account_email'),
            'aud' => 'google',
            'origins' => ['https://pay.google.com'],
            'typ' => 'savetowallet',
            'payload' => [
                'loyaltyObjects' => [
                    [
                        'id' => $this->issuerId . '.' . $objectId
                    ]
                ]
            ]
        ];

        $privateKey = file_get_contents(storage_path('app/google-wallet-service-account.json'));
        $keyData = json_decode($privateKey, true);
        
        $jwt = JWT::encode($payload, $keyData['private_key'], 'RS256');
        
        return 'https://pay.google.com/gp/v/save/' . $jwt;
    }

    public function updateLoyaltyObject($objectId, $points)
    {
        try {
            $loyaltyObject = $this->service->loyaltyobject->get($this->issuerId . '.' . $objectId);
            
            // Update points
            $loyaltyPoints = new LoyaltyPoints();
            $loyaltyPoints->setLabel('Points');
            $balance = new LoyaltyPointsBalance();
            $balance->setString($points . ' pts');
            $loyaltyPoints->setBalance($balance);
            $loyaltyObject->setLoyaltyPoints($loyaltyPoints);
            
            $response = $this->service->loyaltyobject->update($this->issuerId . '.' . $objectId, $loyaltyObject);
            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}