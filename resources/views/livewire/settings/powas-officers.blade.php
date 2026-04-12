<x-form-section submit="saveRoles" method="post">
    <ul>
        @foreach ($usersAll as $user)
            <li>
                {{ $user->username }}
            </li>
        @endforeach
    </ul>
    @slot('title')
        {{ __('POWAS Officers') }}
    @endslot
    @slot('description')
        {{ __('Set all the members with roles.') }}
    @endslot

    <x-slot name="form" autocomplete="off">
        {{-- @dd($oldOfficers) --}}
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="president" value="{{ __('President') }}" />
            <x-input id="president" type="text" class="mt-1 block w-full" wire:model="officers.president"
                autocomplete="off" list="membersListPresident" />
            <datalist id="membersListPresident">
                @foreach ($powasMembers as $member)
                    <option value="{{ $member->member_id }}">{{ $member->lastname . ', ' . $member->firstname }}
                    </option>
                @endforeach
            </datalist>
            <x-input-error for="officers.president" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="vice-president" value="{{ __('Vice-President') }}" />
            <x-input id="vice-president" type="text" class="mt-1 block w-full" wire:model="officers.vice-president"
                autocomplete="off" list="membersListVicePresident" />
            <datalist id="membersListVicePresident">
                @foreach ($powasMembers as $member)
                    <option value="{{ $member->member_id }}">{{ $member->lastname . ', ' . $member->firstname }}
                    </option>
                @endforeach
            </datalist>
            <x-input-error for="officers.vice-president" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="secretary" value="{{ __('Secretary') }}" />
            <x-input id="secretary" type="text" class="mt-1 block w-full" wire:model="officers.secretary"
                autocomplete="off" list="membersListSecretary" />
            <datalist id="membersListSecretary">
                @foreach ($powasMembers as $member)
                    <option value="{{ $member->member_id }}">{{ $member->lastname . ', ' . $member->firstname }}
                    </option>
                @endforeach
            </datalist>
            <x-input-error for="officers.secretary" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="treasurer" value="{{ __('Treasurer') }}" />
            <x-input id="treasurer" type="text" class="mt-1 block w-full" wire:model="officers.treasurer"
                autocomplete="off" list="membersListTreasurer" />
            <datalist id="membersListTreasurer">
                @foreach ($powasMembers as $member)
                    <option value="{{ $member->member_id }}">{{ $member->lastname . ', ' . $member->firstname }}
                    </option>
                @endforeach
            </datalist>
            <x-input-error for="officers.treasurer" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="auditor" value="{{ __('Auditor') }}" />
            <x-input id="auditor" type="text" class="mt-1 block w-full" wire:model="officers.auditor"
                autocomplete="off" list="membersListAuditor" />
            <datalist id="membersListAuditor">
                @foreach ($powasMembers as $member)
                    <option value="{{ $member->member_id }}">{{ $member->lastname . ', ' . $member->firstname }}
                    </option>
                @endforeach
            </datalist>
            <x-input-error for="officers.auditor" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="collector-reader" value="{{ __('Collector/Reader') }}" />
            <x-input id="collector-reader" type="text" class="mt-1 block w-full"
                wire:model="officers.collector-reader" autocomplete="off" list="membersListCollectorReader" />
            <datalist id="membersListCollectorReader">
                @foreach ($powasMembers as $member)
                    <option value="{{ $member->member_id }}">{{ $member->lastname . ', ' . $member->firstname }}
                    </option>
                @endforeach
            </datalist>
            <x-input-error for="officers.collector-reader" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4 text-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Board of Directors') }}</h3>
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-input id="bod1" type="text" class="block w-full" wire:model="officers.bod1" autocomplete="off"
                list="membersListBOD1" />
            <datalist id="membersListBOD1">
                @foreach ($powasMembers as $member)
                    <option value="{{ $member->member_id }}">{{ $member->lastname . ', ' . $member->firstname }}
                    </option>
                @endforeach
            </datalist>
            <x-input-error for="officers.bod1" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-input id="bod2" type="text" class="block w-full" wire:model="officers.bod2" autocomplete="off"
                list="membersListBOD2" />
            <datalist id="membersListBOD2">
                @foreach ($powasMembers as $member)
                    <option value="{{ $member->member_id }}">{{ $member->lastname . ', ' . $member->firstname }}
                    </option>
                @endforeach
            </datalist>
            <x-input-error for="officers.bod2" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-input id="bod3" type="text" class="block w-full" wire:model="officers.bod3"
                autocomplete="off" list="membersListBOD3" />
            <datalist id="membersListBOD3">
                @foreach ($powasMembers as $member)
                    <option value="{{ $member->member_id }}">{{ $member->lastname . ', ' . $member->firstname }}
                    </option>
                @endforeach
            </datalist>
            <x-input-error for="officers.bod3" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-input id="bod4" type="text" class="block w-full" wire:model="officers.bod4"
                autocomplete="off" list="membersListBOD4" />
            <datalist id="membersListBOD4">
                @foreach ($powasMembers as $member)
                    <option value="{{ $member->member_id }}">{{ $member->lastname . ', ' . $member->firstname }}
                    </option>
                @endforeach
            </datalist>
            <x-input-error for="officers.bod4" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-input id="bod5" type="text" class="block w-full" wire:model="officers.bod5"
                autocomplete="off" list="membersListBOD5" />
            <datalist id="membersListBOD5">
                @foreach ($powasMembers as $member)
                    <option value="{{ $member->member_id }}">{{ $member->lastname . ', ' . $member->firstname }}
                    </option>
                @endforeach
            </datalist>
            <x-input-error for="officers.bod5" class="mt-2" />
        </div>
    </x-slot>
    <x-slot name="actions">
        @canany(['add user', 'assign roles'])
            <x-alert-message class="me-3" on="saved" />
            <x-button wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-button>

            @if ($showingChangeOfficerConfirmation == true)
                <x-dialog-modal wire:model.live="showingChangeOfficerConfirmation" maxWidth="sm">
                    <x-slot name="title">
                        <div class="text-left">
                            {{ __('Confirm Officer Change...') }}
                        </div>
                    </x-slot>

                    <x-slot name="content" class="text-left">
                        <div class="text-left">
                            <span class="block">
                                {!! __('Are you sure you want to change <b><i>') .
                                    strtoupper($oldUserInfo->roles->pluck('name')[0]) .
                                    '</i></b> from <b>' .
                                    $oldUserInfo->userinfo->lastname .
                                    ', ' .
                                    $oldUserInfo->userinfo->firstname .
                                    '</b> to <b>' .
                                    $newUserInfo->lastname .
                                    ', ' .
                                    $newUserInfo->firstname .
                                    '</b>?' !!}
                            </span>
                        </div>
                    </x-slot>
                    @slot('footer')
                        <x-button class="ms-3" wire:click="updateUserRole" wire:loading.attr="disabled">
                            {{ __('Yes') }}
                        </x-button>
                        <x-danger-button class="ms-3" wire:click="$toggle('showingChangeOfficerConfirmation')"
                            wire:loading.attr="disabled">
                            {{ __('No') }}
                        </x-danger-button>
                    @endslot
                </x-dialog-modal>
            @endif
        @endcanany
    </x-slot>
</x-form-section>
