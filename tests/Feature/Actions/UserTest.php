<?php

namespace Tests\Feature\Actions;

use App\Actions\User\CreateUser;
use App\Actions\User\UpdateUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as ContractsUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use SocialiteProviders\Manager\OAuth1\User as OAuth1User;
use SocialiteProviders\Manager\OAuth2\User as OAuth2User;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function mockSocialite(bool $useOauth2): ContractsUser
    {
        if ($useOauth2) {
            $oauthUser = Mockery::mock(OAuth2User::class);
        } else {
            $oauthUser = Mockery::mock(OAuth1User::class);
        }

        $oauthUser
            ->shouldReceive('getId')
            ->andReturn(rand());
        $oauthUser
            ->shouldReceive('getName')
            ->andReturn(Str::random());
        $oauthUser
            ->shouldReceive('getNickname')
            ->andReturn(Str::random());
        $oauthUser
            ->shouldReceive('getEmail')
            ->andReturn(Str::random().'@gmail.com');
        $oauthUser
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage');
        $oauthUser->user = [
            'global_name' => Str::random(),
        ];

        $provider = Mockery::mock(Provider::class);

        $provider->shouldReceive('user')->andReturn($oauthUser);

        Socialite::shouldReceive('driver')->with('discord')->andReturn($provider);

        return $oauthUser;
    }

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

    public function test_update_from_socialite_oauth2()
    {
        $response = $this->get('/oauth/discord');
        $response->assertStatus(302);

        $state = session()->get('state');
        $this->assertIsString($state);

        $oauthUser = $this->mockSocialite(true);
        $response = $this->get("/oauth/callback/discord?state=$state");
        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => $oauthUser->getEmail(),
            'name' => $oauthUser->user['global_name'],
            'avatar_url' => $oauthUser->getAvatar(),
        ]);
    }

    public function test_update_from_socialite_oauth()
    {
        $response = $this->get('/oauth/discord');
        $response->assertStatus(302);

        $state = session()->get('state');
        $this->assertIsString($state);

        $oauthUser = $this->mockSocialite(false);
        $response = $this->get("/oauth/callback/discord?state=$state");
        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => $oauthUser->getEmail(),
            'name' => $oauthUser->getNickname(),
            'avatar_url' => $oauthUser->getAvatar(),
        ]);
    }
}
