<?php

namespace App\Livewire\Powas;

use App\Mail\ResetUserPassword;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class UsersCardsList extends Component
{
    use WithPagination;
    public $search;
    public $pagination = 12;
    public $confirmingResetUserPassword = false;
    public $selectedUserID;
    public $resettingPassword = false;

    protected $pageName = 'users';

    public function clearfilter()
    {
        $this->reset([
            'search',
            'pagination',
        ]);

        $this->dispatch('alert', [
            'message' => 'All filters cleared!',
            'messageType' => 'info',
            'position' => 'top-right',
        ]);
    }

    public function showResetConfirmation($userid)
    {
        $this->selectedUserID = $userid;
        $this->confirmingResetUserPassword = true;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetpassword()
    {
        $newpass = Str::random(8);

        try {
            $toUpdate = User::find($this->selectedUserID);
            Mail::to($toUpdate->email)->send(new ResetUserPassword($newpass, $toUpdate->username));

            $toUpdate->password = Hash::make($newpass);
            $toUpdate->account_status = 'INACTIVE';

            $toUpdate->save();

            $this->reset([
                'confirmingResetUserPassword',
            ]);

            $this->dispatch('alert', [
                'message' => 'New password successfully sent to ' . $toUpdate->email . '!',
                'messageType' => 'success',
                'position' => 'top-right',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'message' => $e->getMessage(),
                'messageType' => 'error',
                'position' => 'top-right',
            ]);
        }
    }

    public function render()
    {
        if (!$this->search) {
            $userslist = User::join('user_infos', 'users.user_id', '=', 'user_infos.user_id')
                // ->join('powas', 'users.powas_id', '=', 'powas.powas_id')
                ->orderBy('user_infos.lastname', 'asc')
                ->paginate($this->pagination, ['*'], 'users');

            $loggedInUsers = DB::table('sessions')
                ->whereIn('user_id', $userslist->pluck('user_id'))
                ->pluck('user_id');
        } else {
            $userslist = User::join('user_infos', 'users.user_id', '=', 'user_infos.user_id')
                ->orderBy('user_infos.lastname', 'asc')
                ->where('user_infos.lastname', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('user_infos.firstname', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('user_infos.middlename', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('users.user_id', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('users.username', 'like', '%' . strtoupper($this->search) . '%')
                ->paginate($this->pagination, ['*'], 'users');

            $loggedInUsers = DB::table('sessions')
                ->whereIn('user_id', $userslist->pluck('user_id'))
                ->pluck('user_id');
        }

        return view('livewire.powas.users-cards-list', [
            'users' => $userslist,
            'loggedInUsers' => $loggedInUsers,
        ]);
    }
}
