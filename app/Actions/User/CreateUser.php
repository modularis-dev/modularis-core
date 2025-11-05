<?php

namespace App\Actions\User;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateUser
{
    use AsAction;

    public function handle(array $properties, bool $alreadyValidated = false): User
    {
        if ($alreadyValidated) {
            $validated = $properties;
        } else {
            $validated = Validator::validate($properties, $this->rules());
        }

        $user = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (array_key_exists('avatar_url', $validated) && $validated['avatar_url']) {
            $user->avatar_url = $validated['avatar_url'];
        }

        if (array_key_exists('password', $validated) && $validated['password']) {
            $user->password = Hash::make($validated['password']);
        }

        if (array_key_exists('verify', $validated) && $validated['verify']) {
            $user->email_verified_at = Carbon::now();
        }

        $user->save();

        return $user;
    }

    public function rules(): array
    {
        return [
            'name' => ['string', 'required'],
            'email' => ['email', 'required'],
            'avatar_url' => ['url', 'nullable'],
            'password' => ['string', 'min:8', 'nullable'],
            'verify' => ['boolean', 'nullable'],
        ];
    }
}
