<?php

namespace Tests\Feature\Service\Auth;

use App\Services\Auth\SanctumRegister;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class SanctumRegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_when_given_right_input_then_return_token(): void
    {
        $email = $this->faker()->email;
        $name = $this->faker()->name;
        $password = $this->faker()->password;
        $deviceName = $this->faker()->word;

        $service = App::make(SanctumRegister::class);

        $token = $service->register($email, $password, $name, $deviceName);
        $this->assertNotNull($token, 'The token is null.');

        $this->assertDatabaseHas('users', ['email' => $email]);
        $this->assertDatabaseHas('personal_access_tokens', ['name' => $deviceName]);
    }

    public function test_when_given_no_device_name_then_return_default_token(): void
    {
        $email = $this->faker()->email;
        $name = $this->faker()->name;
        $password = $this->faker()->password;

        $service = new SanctumRegister;
        $token = $service->register($email, $password, $name);

        $this->assertNotNull($token, 'The token is null.');
        $this->assertDatabaseHas('users', ['email' => $email]);
        $this->assertDatabaseHas('personal_access_tokens', ['name' => 'default']);
    }
}
