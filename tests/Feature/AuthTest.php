<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $email = 'arinze@zest.com';
    private $password = '11111111';

    public function setUp(): void {

        parent::setUp();
        
        $this->user = User::factory()->create([
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'role' => Role::USER,
        ]);
    }

    /**
     * 
     * login validation
     * 
     * @return void
     */
    public function test_login_failure_mustSupplyRequiredFields() {

        $data = [
            'email' => $this->user->email,
            'password' => $this->user->password,
        ];

        // arrange
        collect([
            'email', 'password', 
        ])->each(function($field) use ($data) {
            
            // arrange
            $payload = array_merge($data, [$field => ""]);

            // act
            $response = $this->post('/api/login', $payload, [
                'Accept' => 'application/json'
            ]);

            // assert
            $response->assertJson([
                    'status' => false,
                    'message' => 'Validation failed.',
                    'data' => NULL,
                    'error' => [
                        $field => [
                        0 => 'The ' . str_replace("_", " ", $field) . ' field is required.',
                        ],
                    ],
                ])->assertStatus(422);
        });
    }

    /**
     * 
     * Login success
     *
     * @return void
     */
    public function test_login_success_authTokenIssued()
    {
        $this->withoutExceptionHandling();

        $payload = [
            'email' => $this->user->email,
            'password' => $this->password,
        ];

        $response = $this->post('/api/login', $payload, [
            'Accept' => 'application/json'
        ]); 

        $response->assertJson([
            'status' => true,
            'message' => 'Login successful.',
            'data' => [
              'token_type' => 'Bearer token',
            ],
            'error' => NULL,
          ])->assertStatus(200);
    }
}
