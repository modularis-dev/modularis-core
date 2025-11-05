<?php

namespace Tests\Feature\Actions;

use App\Actions\User\CreateUser;
use App\Actions\User\UpdateUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_user_create(): void
    {
        $username = 'test_user';
        $email = 'test@example.com';
        $avatar = 'https://example.org/avatar.png';
        $password = 'password1!';
        $verified = false;

        $user = CreateUser::run([
            'name' => $username,
            'email' => $email,
            'avatar_url' => $avatar,
            'password' => $password,
            'verify' => $verified,
        ]);

        $this->assertModelExists($user);
        $this->assertEquals($username, $user->name);
        $this->assertEquals($avatar, $user->avatar_url);
        $this->assertTrue(Hash::check($password, $user->password));
        $this->assertEquals($verified, ! is_null($user->email_verified_at));
    }

    public function test_user_create_passwordless(): void
    {
        $username = 'test_user2';
        $email = 'test2@example.com';
        $avatar = 'https://example.org/avatar2.png';
        $password = null;
        $verified = true;

        $user = CreateUser::run([
            'name' => $username,
            'email' => $email,
            'avatar_url' => $avatar,
            'password' => $password,
            'verify' => $verified,
        ]);

        $this->assertModelExists($user);
        $this->assertEquals($username, $user->name);
        $this->assertEquals($avatar, $user->avatar_url);
        $this->assertEquals(null, $user->password);
        $this->assertEquals($verified, ! is_null($user->email_verified_at));
    }

    public function test_user_create_duplicate(): void
    {
        $u = User::first();

        $this->assertThrows(function () use ($u) {
            CreateUser::run([
                'name' => $u->name,
                'email' => $u->email,
                'avatar_url' => $u->avatar,
                'password' => 'testing1!',
                'verify' => false,
            ]);
        });
    }

    public function test_user_update(): void
    {
        $u = User::first();
        $this->assertModelExists($u);

        $name = 'Eggs Benedict';
        $avatar = 'https://example.org/eggs.png';
        $password_hash = $u->password_hash;

        $user = UpdateUser::run(
            $u, [
                'name' => $name,
                'avatar_url' => $avatar,
                'password' => 'testing',
            ]
        );

        $this->assertModelExists($u);
        $this->assertEquals($name, $u->name);
        $this->assertEquals($avatar, $u->avatar_url);
        $this->assertEquals($password_hash, $u->password_hash);
    }
}
