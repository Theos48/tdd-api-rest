<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller {

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store(Request $request) {
		$request->validate([
			'name' => 'required|string|max:255|min:3',
			'last_name' => 'required|string|max:255|min:3',
			'email' => 'required|email|unique:users',
			'password' => 'required|min:8|confirmed',
			'password_confirmation' => 'required|same:password|min:8',
		]);
		
		$user = User::create($request->except('password_confirmation'));
		return ApiResponseHelper::successResponse(UserResource::make($user), 'User has been created', Response::HTTP_CREATED);
	}

	public function update(Request $request) {
		$request->validate([
			'name' => 'required|string|max:255|min:3',
			'last_name' => 'required|string|max:255|min:3',
		]);

		auth()->user()->update($request->only('name', 'last_name'));
		$user =  UserResource::make(auth()->user()->fresh());
		return ApiResponseHelper::successResponse($user, 'User has been updated', Response::HTTP_OK);
	}
}
