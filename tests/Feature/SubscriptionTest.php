<?php

namespace Tests\Feature;

use App\Models\Subscriber;
use App\Models\Topic;
use App\Utils\SecurityUtils;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    function test_subscribe_success_subscribedToTopic(): void {

        $topic = Topic::factory()->create();
        $subscriber = Subscriber::factory()->create();
        $mac = base64_encode(hash_hmac(
            'sha256', 
            $subscriber->client_id, 
            SecurityUtils::getDecrypted($subscriber->client_secret),
            true
        ));

        $payload = [
            'url' => 'http://example.com',
        ];
        $headers = [
            'Accept' => 'application/json',
            'ClientId' => $subscriber->client_id,
            'HashMac' =>  $mac,
        ];

        $response = $this->post('/api/subscribe/' . $topic->identifier, $payload, $headers);

        $response->assertJson([
            'url'               => $payload['url'],
            'topic'             => $topic->topic,
            'topic_identifier'  => $topic->identifier,
            'status'            => true,
            'message'           => 'Subscription successful.',
            'error'             => null,
        ]);

        $response->assertStatus(201);
    }
}
