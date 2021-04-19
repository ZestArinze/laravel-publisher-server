<?php

namespace Tests\Feature;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Str;

class TopicTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 
     * create topic validations
     */
    public function test_checkTopicRequiredFields_failure_validationErrorsReturned(): void {

        Sanctum::actingAs(User::factory()->create());

        $payload = [
            'topic' => 'Body Wash',
        ];
        $headers = [
            'Accept' => 'application/json',
        ];

        $arrayKeys = array_keys($payload);

        foreach($arrayKeys as $key) {
            $newPayload = array_merge($payload, [
                $key => null,
            ]);

            $response = $this->post('/api/topics', $newPayload, $headers);

            $response->assertJson([
                'status'  => false,
                'message' => 'Validation failed.',
                'data'    => null,
                'error' => [
                    $key => [
                        'The ' . str_replace('_', ' ', $key) . ' field is required.',
                    ],
                ]
            ])->assertStatus(422);
        }
    }

    /**
     *
     * @return void
     */
    function test_createTopic_success_topicCreated(): void {
        
        Sanctum::actingAs(User::factory()->create());

        $payload = [
            'topic' => 'Body Wash',
        ];
        $headers = [
            'Accept' => 'application/json',
        ];

        $response = $this->post('/api/topics', $payload, $headers);

        $response->assertJson([
            'status' => true,
            'message' => 'Topic created.',
            'data' => [
              'topic' => $payload['topic'],
              'identifier' => Str::slug($payload['topic']),
            ],
            'error' => null,
        ]);

        $response->assertStatus(201);

        // create 10 more topics
        Topic::factory(10)->create();

        // assert that there are now 11 records in the table
        $this->assertDatabaseCount('topics', 11);
    }
    
    /**
     * 
     * get all topics success
     */
    public function test_getTopics_success_topicsRetrieved(): void {
        
        $topic1 = Topic::factory()->create();
        $topic2 = Topic::factory()->create();

        $response = $this->get('/api/topics', [
            'Accept' => 'application/json'
        ]);

        $this->assertDatabaseCount('topics', 2);

        $response->assertJson([
            'status' => true,
            'message' => 'OK.',
            'data' => [
                [
                    'id' => $topic1->id,
                    'topic' => $topic1->topic,
                    'user_id' => $topic1->user_id,
                ],
                [
                    'id' => $topic2->id,
                    'topic' => $topic2->topic,
                    'user_id' => $topic2->user_id,
                ],
            ],
        ]);
    }
}
