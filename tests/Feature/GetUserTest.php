<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Config;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\User;

class GetUserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetUser()
    {
        $user = User::where('email','root@example.com')->first();
       
        $token = JWTAuth::fromUser($user);
       
        $baseUrl = Config::get('app.url') . '/api/user?token=' . $token;
      
        $response = $this->json('GET', $baseUrl . '/', []);
    
        $response->assertStatus(200);
    }
}
