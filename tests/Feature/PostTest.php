<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 
     * create post validations
     */
    public function test_checkPostRequiredFields_failure_validationErrorsReturned(): void {

        Sanctum::actingAs(User::factory()->create());

        $topic = Topic::factory()->create();
        $payload = [
            'title' => 'Awesome post',
            'body' => 'This is body of the awesome post.',
        ];
        $headers = [
            'Accept' => 'application/json',
        ];

        $arrayKeys = array_keys($payload);

        foreach($arrayKeys as $key) {
            $newPayload = array_merge($payload, [
                $key => null,
            ]);

            $response = $this->post('/api/publish/' . $topic->identifier, $newPayload, $headers);

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
     * create post success
     * 
     * @return void
     */
    public function test_createPost_success_postCreated()
    {
        $this->withoutExceptionHandling();

        Sanctum::actingAs(User::factory()->create());

        $topic = Topic::factory()->create();

        $headers = ['Accept' => 'application/json'];
        $payload = [
            'title' => 'Awesome post',
            'body' => 'This is body of the awesome post.',
        ];

        $response = $this->post('/api/publish/' . $topic->identifier, $payload, $headers);

        $response->assertJson([
            'status' => true,
            'message' => 'Post created.',
            'data' => [
                'id' => 1,
                'title' => $payload['title'],
                'body' => $payload['body'],
                'topic_id' => $topic->id,
            ],
        ]);

        $response->assertStatus(201);
    }

    /**
     * 
     * get all topics success
     */
    public function test_getPosts_success_postsRetrieved(): void {
        
        $post1 = Post::factory()->create();
        $post2 = Post::factory()->create();

        $response = $this->get('/api/posts', [
            'Accept' => 'application/json'
        ]);

        $this->assertDatabaseCount('posts', 2);

        $response->assertJson([
            'status' => true,
            'message' => 'OK.',
            'data' => [
                [
                    'id' => $post1->id,
                    'title' => $post1->title,
                    'user_id' => $post1->user_id,
                ],
                [
                    'id' => $post2->id,
                    'title' => $post2->title,
                    'user_id' => $post2->user_id,
                ],
            ],
        ]);
    }
}
