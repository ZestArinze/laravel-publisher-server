<?php

namespace Tests\Feature;

use App\Models\Subscriber;
use App\Models\User;
use App\Utils\SecurityUtils;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubscriberTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * 
     * get all topics success
     */
    public function test_subscriberCredentials_success_clientIdAndSecretGenerated(): void {
        
        Sanctum::actingAs(User::factory()->create());

        $response = $this->get('/api/credentials', [
            'Accept' => 'application/json'
        ]);

        $response->assertJson([
            'status' => true,
            'message' => 'New client credentials generated successfully.',
            'data' => [],
            'error' => null,
        ]);

        Subscriber::factory(2)->create();
        $this->assertDatabaseCount('subscribers', 3);
    }
}
