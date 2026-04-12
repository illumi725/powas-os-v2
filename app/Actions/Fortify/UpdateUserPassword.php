<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\PhoneNumberFormat2;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ])->validateWithBag('updatePassword');

        if ($user->getAccountStatus() == 'INACTIVE') {
            $validator = Validator::make($input, [
                'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')->ignore($user->email, 'email')],
                'contact_number' => ['required', new PhoneNumberFormat2],
            ]);

            $validator->validate();

            $user->forceFill([
                'email' => $input['email'],
                'contact_number' => $input['contact_number'],
            ])->save();
        }

        $user->forceFill([
            'password' => Hash::make($input['password']),
            'account_status' => 'ACTIVE',
        ])->save();
    }
}
