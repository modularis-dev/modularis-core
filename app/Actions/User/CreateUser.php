<?php

namespace App\Actions\User;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateUser
{
    use AsAction;

    public function handle(string $name, string $email, ?string $avatar = null, ?string $password = null, ?bool $verify = false): User
    {
        $user = new User([
            'name' => $name,
            'email' => $email,
        ]);

        if ($avatar) {
            $user->avatar_url = $avatar;
        }

        if ($password) {
            $user->password = Hash::make($password);
        }

        if ($verify) {
            $user->email_verified_at = Carbon::now();
        }

        $user->save();

        return $user;
    }
}
