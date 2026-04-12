<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        // dd($input);
        Validator::make($input, [
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->username, 'username')],
            'lastname' => ['required', 'string', 'max:50'],
            'firstname' => ['required', 'string', 'max:50'],
            'middlename' => ['string', 'max:50'],
            'birthday' => ['required', 'date'],
            'address1' => ['required', 'string', 'max:50'],
            'region' => ['required', 'string', 'max:50'],
            'province' => ['required', 'string', 'max:50'],
            'municipality' => ['required', 'string', 'max:50'],
            'barangay' => ['required', 'string', 'max:50'],
            // 'provincial_address' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')->ignore($user->email, 'email')],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:2048'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'username' => $input['username'],
                'email' => $input['email'],
            ])->save();

            $user->userinfo->updateOrCreate(
                [
                    'user_id' => $user->user_id,
                ],
                [
                    'lastname' => strtoupper(
                        $input['lastname']
                    ),
                    'firstname' => strtoupper(
                        $input['firstname']
                    ),
                    'middlename' => strtoupper(
                        $input['middlename']
                    ),
                    'birthday' => date(
                        $input['birthday']
                    ),
                    'address1' => strtoupper(
                        $input['address1']
                    ),
                    'region' => strtoupper(
                        $input['region']
                    ),
                    'province' => strtoupper(
                        $input['province']
                    ),
                    'municipality' => strtoupper(
                        $input['municipality']
                    ),
                    'barangay' => strtoupper(
                        $input['barangay']
                    ),
                ]
            );

            // $userinfo->forceFill([
            //     'lastname' => $input['lastname'],
            // ])->save();

        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'username' => $input['username'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
