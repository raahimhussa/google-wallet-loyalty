<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\LoyaltyCard;
use App\Services\GoogleWalletService;
use Mockery\MockInterface;

class LoyaltyCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_loyalty_card(): void
    {
        $this->mock(GoogleWalletService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createLoyaltyClass')->once();
            $mock->shouldReceive('createLoyaltyObject')->once()->andReturn(['id' => 'fake-object-id']);
            $mock->shouldReceive('generateSaveLink')->once()->andReturn('fake-save-link');
        });

        $userName = 'Test User';

        $response = $this->postJson('/api/loyalty-cards', ['user_name' => $userName]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'card' => [
                    'user_id',
                    'card_id',
                    'google_wallet_object_id',
                    'card_number',
                    'points',
                    'metadata',
                    'issued_at',
                    'expires_at',
                    'is_active',
                ],
                'save_link',
                'google_wallet_object',
            ],
        ]);

        $this->assertDatabaseHas('loyalty_cards', [
            'metadata->user_name' => $userName,
        ]);
    }

    public function test_can_update_loyalty_card_points(): void
    {
        $card = LoyaltyCard::create([
            'user_id' => 'test-user-id',
            'card_id' => 'CARD_TEST123',
            'google_wallet_object_id' => 'WALLET_OBJECT_123',
            'card_number' => 'LC123456',
            'points' => 10,
            'metadata' => ['user_name' => 'Test User', 'class_id' => 'CLASS_123'],
            'issued_at' => now(),
            'expires_at' => now()->addYear(),
            'is_active' => true
        ]);

        $this->mock(GoogleWalletService::class, function (MockInterface $mock) {
            $mock->shouldReceive('updateLoyaltyObject')->once();
        });

        $newPoints = 100;

        $response = $this->patchJson("/api/loyalty-cards/{$card->card_id}/points", [
            'points' => $newPoints
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'points' => $newPoints,
            ]
        ]);

        $this->assertDatabaseHas('loyalty_cards', [
            'id' => $card->id,
            'points' => $newPoints
        ]);
    }
} 