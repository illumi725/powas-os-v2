<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UsersList extends Component
{
    use WithPagination;

    public $search = '';
    public $pagination = 10;

    public function render()
    {
        if (!$this->search) {
            $userslist = User::orderBy('username', 'asc')
                ->paginate($this->pagination);
        } else {
            $userslist = User::orderBy('username', 'asc')
                ->where('username', 'like', '%' . strtoupper($this->search) . '%')
                ->paginate($this->pagination);
        }

        return view('livewire.users.users-list', [
            'userslist' => $userslist,
        ]);
    }
}
