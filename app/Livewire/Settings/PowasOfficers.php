<?php

namespace App\Livewire\Settings;

use App\Events\ActionLogger;
use App\Models\Powas;
use App\Models\PowasMembers;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class PowasOfficers extends Component
{
    public $powasID;
    public $powas;
    public $members;
    public $powasUsers;
    public $usersAll;
    public $roles;
    public $officers = [];
    public $data = [];
    public $oldOfficers = [];
    public $isChangingOfficerConfirmed = false;
    public $showingChangeOfficerConfirmation = false;
    public $oldUserInfo;
    public $newUserInfo;
    public $selectedMemberID;
    public $selectedOldBOD;
    public $bodList = [];

    protected $validationAttributes = [
        'officers.president' => 'President',
        'officers.vice-president' => 'Vice-President',
        'officers.secretary' => 'Secretary',
        'officers.treasurer' => 'Treasurer',
        'officers.auditor' => 'Auditor',
        'officers.collector-reader' => 'Collector/Reader',
        'officers.bod1' => 'Board of Director 1',
        'officers.bod2' => 'Board of Director 3',
        'officers.bod3' => 'Board of Director 3',
        'officers.bod5' => 'Board of Director 4',
        'officers.bod5' => 'Board of Director 5',
    ];

    public function mount($powas_id)
    {
        $this->powasID = $powas_id;
        $this->powas = Powas::find($powas_id);
        $this->powasUsers = User::with('roles')
            ->join('user_infos', 'users.user_id', '=', 'user_infos.user_id')
            ->where('users.powas_id', $this->powasID)
            ->where(function ($query) {
                $query->where('account_status', 'ACTIVE')
                    ->orWhere('account_status', 'INACTIVE');
            })
            ->get();
        $this->roles = Role::all();
        $this->usersAll = User::with('roles')->get();

        $this->loadOfficers();
    }

    public function loadOfficers()
    {
        $userRoles = [];
        $this->reset(['bodList']);

        $ctr = 0;

        foreach ($this->powasUsers as $user) {
            $userRole = $user->roles->pluck('name')[0];
            $userRoles[$user->user_id] = [
                'role' => $userRole,
                'name' => $user->lastname . ', ' . $user->firstname . ' ' . $user->middlename,
            ];
            $this->oldOfficers[$user->user_id] = [
                'role' => $userRole,
                'username' => $user->username,
                'lastname' => $user->lastname,
                'firstname' => $user->firstname,
                'middlename' => $user->middlename,
                'birthday' => $user->birthday,
            ];

            if ($userRole == 'board') {
                $ctr++;
                $this->bodList['bod' . $ctr] = [
                    'user_id' => $user->user_id,
                    'name' => $user->lastname . ', ' . $user->firstname . ' ' . $user->middlename,
                ];
                $this->officers['bod' . $ctr] = $user->lastname . ', ' . $user->firstname . ' ' . $user->middlename;
            } else {
                $this->officers[$userRole] = $user->lastname . ', ' . $user->firstname . ' ' . $user->middlename;
            }
        }
    }

    public function customValidation()
    {
        $this->resetErrorBag('officers.*');
        foreach ($this->officers as $position => $officer) {
            foreach ($this->officers as $otherPosition => $otherOfficer) {
                if ($position != $otherPosition && $officer == $otherOfficer) {
                    return $this->addError('officers.' . $position, ucfirst($position) . ' cannot be the same as ' . ucfirst($otherPosition) . '!');
                }
            }
        }
    }

    public function checkPattern($memberID)
    {
        $pattern = '/^[A-Z]{3}-[A-Z]{3}-[A-Z]{3}-\d{3}-\d{4}$/';

        if (preg_match($pattern, $memberID)) {
            return true;
        }

        return false;
    }

    public function checkRolesExists($role)
    {
        $roleExists = false;
        foreach ($this->oldOfficers as $userID => $officer) {
            if ($officer['role'] === $role) {
                $roleExists = true;
            }
        }

        return $roleExists;
    }

    public function saveRoles()
    {
        foreach ($this->officers as $position => $memberID) {
            if ($this->checkPattern($memberID)) {
                $validatedInput = $this->customValidation();
                if ($validatedInput == null) {
                    $setPosition = $position;

                    if (str_starts_with($position, 'bod')) {
                        $setPosition = 'board';
                    }
                    // dd(isset($this->bodList[$position]));

                    $roleExists = $this->checkRolesExists($setPosition);
                    if ($roleExists === true) {
                        foreach ($this->oldOfficers as $userID => $officer) {
                            if ($officer['role'] === $setPosition) {
                                $this->reset([
                                    'oldUserInfo',
                                    'newUserInfo',
                                    'showingChangeOfficerConfirmation',
                                ]);

                                // dd('Test Case 1');
                                // $this->bodList[$position];

                                $this->oldUserInfo = User::with('roles')->find($this->bodList[$position]['user_id']);

                                $this->selectedMemberID = $memberID;

                                $this->newUserInfo = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                                    ->where('powas_applications.powas_id', $this->powasID)
                                    ->where('powas_members.member_id', $this->selectedMemberID)
                                    ->first();

                                $this->showingChangeOfficerConfirmation = true;
                            }
                        }
                    } else {
                        $memberInfo = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                            ->where('powas_applications.powas_id', $this->powasID)
                            ->where('powas_members.member_id', $memberID)
                            ->first();

                        // dd('Test Case 2');

                        $generatedUsername = strtolower($memberInfo->lastname[0] . '.' . str_replace(' ', '', $memberInfo->firstname));

                        $checkUsername = User::where('username', $generatedUsername)->exists();

                        if ($checkUsername === true) {
                            return $this->dispatch('saved', [
                                'message' => 'Username is already taken!',
                                'messageType' => 'warning',
                                'position' => 'top-right',
                            ]);
                        }

                        $newUserID = rand(10000, 99999);

                        $toSaveUserAccount = [
                            'user_id' => $newUserID,
                            'username' => $generatedUsername,
                            'password' => Hash::make('powasos123'),
                            'powas_id' => $memberInfo->powas_id,
                        ];

                        $toSaveUserInfo = [
                            'user_id' => $newUserID,
                            'lastname' => $memberInfo->lastname,
                            'firstname' => $memberInfo->firstname,
                            'middlename' => $memberInfo->middlename,
                            'birthday' => $memberInfo->birthday,
                            'address1' => $memberInfo->address1,
                            'region' => $memberInfo->region,
                            'province' => $memberInfo->province,
                            'municipality' => $memberInfo->municipality,
                            'barangay' => $memberInfo->barangay,
                        ];

                        $this->createUserAccount($toSaveUserAccount, $memberInfo);
                        $this->createUserInfo($toSaveUserInfo);

                        $newUserAccount = User::find($newUserID);

                        $newUserAccount->syncRoles([$setPosition]);

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated user role for <b><i>' . $memberInfo->lastname . ', ' . $memberInfo->firstname . '</i></b> from <b>""</b> to <b>"' . $setPosition . '"</b>.';

                        ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'user-account', $this->powasID);

                        $this->dispatch('saved', [
                            'message' => 'New user profile successfully created!',
                            'messageType' => 'success',
                            'position' => 'top-right',
                        ]);
                    }
                }
            }
        }

        $this->powasUsers = User::with('roles')
            ->join('user_infos', 'users.user_id', '=', 'user_infos.user_id')
            ->where('users.powas_id', $this->powasID)
            ->where('account_status', 'ACTIVE')
            ->orWhere('account_status', 'INACTIVE')
            ->get();

        $this->loadOfficers();
    }

    public function createUserAccount(array $toSave, $memberInfo)
    {
        User::updateOrCreate($toSave);

        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created user account for <b><i>' . $memberInfo->lastname . ', ' . $memberInfo->firstname . '</i></b> with User ID <b>' . $toSave['user_id'] . '</b> at <b>' . $this->powas->barangay . ' POWAS ' . $this->powas->phase . '</b>.';

        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'user-account', $this->powasID);
    }

    public function createUserInfo(array $toSave)
    {
        UserInfo::updateOrCreate($toSave);

        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created user information for <b><i>' . $toSave['lastname'] . ', ' . $toSave['firstname'] . '</i></b> with User ID <b>' . $toSave['user_id'] . '</b> at <b>' . $this->powas->barangay . ' POWAS ' . $this->powas->phase . '</b>.';

        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'user-account', $this->powasID);
    }

    public function updateUserRole()
    {
        $newUser = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_applications.powas_id', $this->powasID)
            ->where('powas_members.member_id', $this->selectedMemberID)
            ->first();

        $generatedUsername = strtolower($newUser->lastname[0] . '.' . str_replace(' ', '', $newUser->firstname));

        $checkUsername = User::where('username', $generatedUsername)->exists();

        $currentStatus = 'DEACTIVATED';

        if ($checkUsername === true) {
            $oldAccount = User::with('userinfo')->where('username', $generatedUsername)->first();

            if ($oldAccount->account_status == 'DEACTIVATED') {
                // dd('Test Case 3');

                $newUser = User::join('user_infos', 'users.user_id', '=', 'user_infos.user_id')->where('users.user_id', $oldAccount->user_id)->first();

                $newUser->account_status = 'INACTIVE';
                $newUser->save();

                $newUser->syncRoles([$this->oldUserInfo->roles->pluck('name')[0]]);

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated user role for <b><i>' . $newUser->lastname . ', ' . $newUser->firstname . '</i></b> from <b>"' . $this->oldUserInfo->roles->pluck('name')[0] . '"</b> to <b>""</b>.';

                ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'user-account', $this->powasID);

                $this->oldUserInfo->account_status = $currentStatus;
                $this->oldUserInfo->save();

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated account status for <b><i>' . $this->oldUserInfo->userinfo->lastname . ', ' . $this->oldUserInfo->userinfo->firstname . '</i></b> from <b>"' . $this->oldUserInfo->account_status . '"</b> to <b>"' . $currentStatus . '"</b>.';

                ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'user-account', $this->powasID);

                $this->oldUserInfo->roles()->detach();

                $this->dispatch('saved', [
                    'message' => 'New user profile successfully created!',
                    'messageType' => 'success',
                    'position' => 'top-right',
                ]);

                $this->showingChangeOfficerConfirmation = false;

                $this->powasUsers = User::with('roles')
                    ->join('user_infos', 'users.user_id', '=', 'user_infos.user_id')
                    ->where('users.powas_id', $this->powasID)
                    ->where('account_status', 'ACTIVE')
                    ->orWhere('account_status', 'INACTIVE')
                    ->get();

                $this->loadOfficers();

                return;
            } else {
                return $this->dispatch('saved', [
                    'message' => 'Username is already taken!',
                    'messageType' => 'warning',
                    'position' => 'top-right',
                ]);
            }
        }
        // dd('Test Case 4');

        // dd($this->oldUserInfo);

        $newUserID = rand(10000, 99999);

        $toSaveUserAccount = [
            'user_id' => $newUserID,
            'username' => $generatedUsername,
            'password' => Hash::make('powasos123'),
            'powas_id' => $newUser->powas_id,
        ];

        $toSaveUserInfo = [
            'user_id' => $newUserID,
            'lastname' => $newUser->lastname,
            'firstname' => $newUser->firstname,
            'middlename' => $newUser->middlename,
            'birthday' => $newUser->birthday,
            'address1' => $newUser->address1,
            'region' => $newUser->region,
            'province' => $newUser->province,
            'municipality' => $newUser->municipality,
            'barangay' => $newUser->barangay,
        ];

        $this->createUserAccount($toSaveUserAccount, $newUser);
        $this->createUserInfo($toSaveUserInfo);

        $newUserAccount = User::find($newUserID);

        $newUserAccount->syncRoles([$this->oldUserInfo->roles->pluck('name')[0]]);

        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated user role for <b><i>' . $newUser->lastname . ', ' . $newUser->firstname . '</i></b> from <b>"' . $this->oldUserInfo->roles->pluck('name')[0] . '"</b> to <b>""</b>.';

        ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'user-account', $this->powasID);

        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated user role for <b><i>' . $this->oldUserInfo->userinfo->lastname . ', ' . $this->oldUserInfo->userinfo->firstname . '</i></b> from <b>"' . $this->oldUserInfo->roles->pluck('name')[0] . '"</b> to <b>""</b>.';

        ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'user-account', $this->powasID);

        $this->oldUserInfo->account_status = $currentStatus;
        $this->oldUserInfo->save();

        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated account status for <b><i>' . $this->oldUserInfo->userinfo->lastname . ', ' . $this->oldUserInfo->userinfo->firstname . '</i></b> from <b>"' . $this->oldUserInfo->account_status . '"</b> to <b>"' . $currentStatus . '"</b>.';

        ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'user-account', $this->powasID);

        $this->oldUserInfo->roles()->detach();

        $this->dispatch('saved', [
            'message' => 'New user profile successfully created!',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);

        $this->showingChangeOfficerConfirmation = false;

        $this->powasUsers = User::with('roles')
            ->join('user_infos', 'users.user_id', '=', 'user_infos.user_id')
            ->where('users.powas_id', $this->powasID)
            ->where('account_status', 'ACTIVE')
            ->orWhere('account_status', 'INACTIVE')
            ->get();

        $this->loadOfficers();
    }

    public function render()
    {
        $powasMembers = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_applications.powas_id', $this->powasID)
            ->orderBy('powas_applications.lastname', 'asc')
            ->orderBy('powas_applications.firstname', 'asc')
            ->orderBy('powas_applications.middlename', 'asc')
            ->get();

        return view('livewire.settings.powas-officers', [
            'powasMembers' => $powasMembers,
        ]);
    }
}
