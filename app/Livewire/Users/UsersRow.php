<?php

namespace App\Livewire\Users;

use App\Mail\ResetUserPassword;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Illuminate\Support\Str;

class UsersRow extends Component
{
    public $user;

    public function mount($user)
    {
        $this->user = $user;
    }

    public function resetpassword()
    {
        $newpass = Str::random(8);
        $email = 'randy.a257@gmail.com';

        try {
            Mail::to($email)->send(new ResetUserPassword($newpass, $email));

            $this->dispatch('alert', [
                'message' => 'New password successfully sent to ' . $email . '!',
                'messageType' => 'success',
                'position' => 'top-right',
            ])->to(UsersList::class);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'message' => $e->getMessage(),
                'messageType' => 'error',
                'position' => 'top-right',
            ])->to(UsersList::class);
        }
    }

    public function render()
    {
        return view('livewire.users.users-row');
    }
}
