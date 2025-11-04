<?php

namespace Tests\Feature\Actions;

use App\Actions\User\CreateUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * A basic feature test example.
     */
    public function test_user_create(): void
    {
        $username = 'test_user';
        $email = 'test@example.com';
        $avatar = 'avatar.png';
        $password = 'password1!';
        $verified = false;

        $user = CreateUser::run(
            $username, $email, $avatar, $password, $verified
        );

        $this->assertModelExists($user);
        $this->assertEquals($username, $user->name);
        $this->assertEquals($avatar, $user->avatar_url);
        $this->assertTrue(Hash::check($password, $user->password));
        $this->assertEquals($verified, ! is_null($user->email_verified_at));
    }
}
