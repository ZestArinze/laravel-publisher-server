<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class SignUpTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void {
        parent::setUp();
    }

    /**
     * 
     * 
     * User registration validation test
     * 
     * @return void
     */
    public function test_register_failure_checkSignupRequiredFields() {

        // $this->withoutExceptionHandling();

        $data = [
            'name' => 'John Doe',
            'email' => 'someone@example.com',
            'password' => '11111111',
            'password_confirmation' => '11111111',
        ];

        // arrange
        collect([
            'name', 'email', 'password', 
        ])->each(function($field) use ($data) {
            
            // arrange
            $payload = array_merge($data, [$field => '']);

            // act
            $response = $this->post('/api/register', $payload, [
                'Accept' => 'application/json'
            ]);

            // assert
            $response->assertJson([
                'status' => false, 
                'message' => 'Validation failed.', 
                'data' => null, 
                'error' => [
                    $field => [
                         'The ' . str_replace('_', ' ', $field) .' field is required.' 
                      ] 
                   ] 
             ])->assertStatus(422);
        });
    }


    /**
     * 
     * User registration unque fields test
     *
     * @return void
     */
    public function test_register_failure_noDuplicatesForUniqueFields()
    {
        $payload = [
            'name' => 'Arinze Zest',
            'email' => 'arinze@zest.com',
            'password' => '11111111',
            'password_confirmation' => '11111111',
        ];

        // register
        $this->post('/api/register', $payload,[
            'Accept' => 'application/json'
        ]);

        // try registering with the same data
        $response = $this->post('/api/register', $payload,[
            'Accept' => 'application/json'
        ]);
        
        $response->assertJson([
            'status' => false,
            'message' => 'Validation failed.',
            'data' => NULL,
            'error' => [
              'email' => [
                0 => 'The email has already been taken.',
              ],
            ],
          ])->assertStatus(422);
    }

    /**
     * 
     * 
     * User registration success test
     *
     * @return void
     */
    public function test_register_success_userAccountCreated()
    {

        $payload = [
            'name' => 'Arinze Zest',
            'email' => 'arinze@zest.com',
            'password' => '11111111',
            'password_confirmation' => '11111111',
        ];

        $response = $this->post('/api/register', $payload,[
            'Accept' => 'application/json'
        ]);
        
        $response->assertJson([
                'status' => true, 
                'message' => 'Account created. You may login now.', 
                'data' => [
                      'name'    => $payload['name'], 
                      'email'   => $payload['email'], 
                   ], 
                'error' => null 
             ])->assertStatus(201);
    }

    /*
    |
    |   @TODO more test methods
    |
    */
}
