<?php

namespace Tests\Feature\Service\Auth;

use App\Models\User;
use App\Services\Auth\LoginService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LoginService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = App::make(LoginService::class);

        User::factory()->withSanctumToken()->create([
            'email' => 'test@gmail.com',
        ]);

    }

    /**
     * A basic feature test example.
     */
    public function test_given_valid_credentials_when_login_then_return_token()
    {
        $email = 'test@gmail.com';
        $password = 'password';

        $token = $this->service->login($email, $password);

        $this->assertNotNull($token);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => 1,
        ]);
    }

    public function test_given_invalid_credentials_when_login_then_throw_exception()
    {
        $this->expectException(\Illuminate\Auth\AuthenticationException::class);
        $email = 'test@gmail.com';
        $password = 'wrong';

        $token = $this->service->login($email, $password);
    }

    public function test_given_authenticated_user_when_logout_then_delete_token()
    {
        Sanctum::actingAs(User::first());

        /**
         * @var User $user
         * */
        $user = auth()->user();
        $current_token = $user->currentAccessToken();

        $this->service->logout();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'id' => $current_token->id,
        ]);
    }

    public function test_given_authenticated_user_when_logout_all_then_delete_all_tokens()
    {
        Sanctum::actingAs(User::first());
        // create new token

        /**
         * @var User $user
         * */
        $user = auth()->user();

        $user->createToken('new-token');

        $this->service->logoutAll();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_given_authenticated_user_when_logout_device_then_delete_token()
    {
        Sanctum::actingAs(User::first());
        // create new token
        /**
         * @var User $user
         * */
        $user = auth()->user();
        $token = $user->createToken('new-token');
        $this->service->logoutDevice($token->accessToken->id);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'id' => $token->accessToken->id,
        ]);

        $this->assertGreaterThan(0, $user->tokens()->count());
    }
}
