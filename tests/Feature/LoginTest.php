<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{


    use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLogin(){
     
        $response = $this->postJson('http://127.0.0.1:8000/api/authenticate',
            [
                'email' => 'root@example.com',
                'password' => 'root12345'
            ]
        );

        $response->assertStatus(200)
            ->assertJsonStructure(['token','user']);
    }
}
