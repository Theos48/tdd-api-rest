<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;


abstract class TestCase extends BaseTestCase {
	//
	protected string $apiBaseUrl = 'api/v1';

	/**
	 * @param User $user
	 *
	 * @return string[]
	 */
	protected function getAuthHeaders(User $user): array {
		$token = JWTAuth::fromUser($user);
		return ['Authorization' => 'Bearer ' . $token];
	}

	/**
	 *
	 * @param User $user
	 *
	 * @return void
	 */
	protected function actingAsUser(User $user): void {
		$this->withHeaders($this->getAuthHeaders($user));
	}
}
