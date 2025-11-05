<?php

namespace App\Actions\User;

use DutchCodingCompany\FilamentSocialite\Events\Login;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Contracts\User as ContractsUser;
use Lorisleiva\Actions\Concerns\AsAction;
use SocialiteProviders\Manager\OAuth2\User as OAuth2User;

class UpdateUserFromProvider
{
    use AsAction;

    public function handle(Authenticatable $user, ContractsUser $oauthUser)
    {
        if ($oauthUser instanceof OAuth2User) {
            return UpdateUser::run($user, [
                'avatar_url' => $oauthUser->getAvatar(),
                'name' => $oauthUser->user['global_name'],
            ]);
        }

        return UpdateUser::run($user, [
            'avatar_url' => $oauthUser->getAvatar(),
            'name' => $oauthUser->getNickname(),
        ]);
    }

    public function asListener(Login $event)
    {
        $this->handle($event->socialiteUser->getUser(), $event->oauthUser);
    }
}
