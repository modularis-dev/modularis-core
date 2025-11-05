<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUser
{
    use AsAction;

    public function handle(User $user, array $properties, bool $alreadyValidated = false): User
    {
        $validated = Validator::validate($properties, $this->rules());

        $user->update($validated);

        return $user;
    }

    public function rules(): array
    {
        return [
            'name' => ['string', 'sometimes'],
            'avatar_url' => ['string', 'nullable', 'sometimes'],
        ];
    }
}
