<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\UserService;


class AuthenticateController extends Controller
{
    /** App\Services\UserService */
    private UserService $user_service;

    /**
     * __construct
     *
     * @param  App\Services\UserService $user_service
     *
     * @return void
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }


	public function authenticate(LoginRequest $request)
	{
		$validatedProperties = $request->validated();
		$credentials = $request->only('email','password');
		$authentication_result = $this->user_service->checkAuthentication($request , $credentials);
        return $authentication_result;
	}
}