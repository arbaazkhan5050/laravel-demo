<?php

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class UserService
{

    public function checkAuthentication($request , $credentials)
    {
      try {
      
        if(!$token = JWTAuth::attempt($credentials)) {
      
          return response()->json(['error' => 'Invalid login credential'], 401);
      
        }
      
      } catch(JWTException $e) {
      
        return response()->json(['error' => 'Could not create token'], 500);
      
      }
  
      $user = $request->user();
      
      return response()->json(compact('token', 'user'));
       
    }
}
