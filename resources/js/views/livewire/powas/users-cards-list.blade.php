<div class="py-4 px-4 space-y-4" x-data="{ expanded: '' }">
    <x-alert-message class="me-3" on="alert" />

    {{-- Filter --}}
    <div class="w-full">
        <span class="font-bold cursor-pointer uppercase dark:text-white"
            @click="expanded = ('filter' === expanded) ? '' : 'filter'">
            {{ __('Filter') }}
            &nbsp;
            <span x-show="expanded !== 'filter'"><i class="fa-solid fa-chevron-right"></i></span>
            <span x-show="expanded === 'filter'"><i class="fa-solid fa-chevron-down"></i></span>
        </span>
        <button x-show="expanded === 'filter'" type="button" wire:click="clearfilter"
            class="ml-4 uppercase text-xs py-1 px-2 rounded-xl font-bold shadow bg-gray-400 text-gray">{{ __('Clear Filter') }}</button>
        @can('view logs')
            @livewire('logs.user-account')
        @endcan
    </div>

    {{-- Search and Pagination Control --}}
    <div x-show="expanded === 'filter'" class="grid grid-cols-1 md:grid-cols-2 gap-4 overflow-hidden" x-collapse>
        <div class="w-full md:col-span-2 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2 flex items-center">

            </div>
            <div class="w-full">
                <x-label class="inline" for="search" value="{{ __('Search: ') }}" />
                <x-input class="w-full block" id="search" name="search" wire:model.live="search" autocomplete="off"
                    placeholder="Search..." />
            </div>
            <div class="w-full">
                <x-label class="inline" for="pagination" value="{{ __('# of rows per page: ') }}" />
                <x-combobox class="w-full block" id="pagination" name="pagination" wire:model.live="pagination">
                    <x-slot name="options">
                        @for ($i = 12; $i <= 120; $i = $i + 12)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </x-slot>
                </x-combobox>
            </div>
        </div>
    </div>

    {{-- Members Cards List --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse ($users as $user)
            @if ($user->user_id != Auth::user()->user_id)
                <div class="text-slate-800 border border-slate-400 dark:border-none dark:bg-slate-300 shadow-md hover:scale-105 dark:hover:scale-105 rounded-lg"
                    wire:key="{{ $user->user_id }}">
                    <div class="py-4 rounded-lg border-b-8 border-slate-400 dark:border-slate-600">
                        <div class="mx-4 text-sm italic font-black w-full">
                            {{ $user->username }} <span>{!! $loggedInUsers->contains($user->user_id)
                                ? '<span class="inline-flex text-xs font-bold ml-1 px-1 not-italic bg-green-400 rounded-full">online</span>'
                                : '<span class="inline-flex text-xs font-bold ml-1 px-1 not-italic bg-red-400 rounded-full">offline</span>' !!}</span>
                        </div>
                        <div class="mx-4 font-bold whitespace-nowrap overflow-hidden">
                            {{ $user->userinfo->lastname . ', ' . $user->userinfo->firstname . ' ' . $user->userinfo->middlename }}
                        </div>
                        <div class="mx-4 text-xs italic whitespace-nowrap overflow-hidden">
                            {{ $user->userinfo->address1 . ', ' . $user->userinfo->barangay . ', ' . $user->userinfo->municipality . ', ' . $user->userinfo->province }}
                        </div>

                        <div class="mx-4 text-xs font-bold">
                            @php
                                $phaseName = '';
                            @endphp
                            @if (isset($user->powas))
                                @php
                                    $phaseName = ' [' . $user->powas->barangay . ' POWAS ' . $user->powas->phase . ']';
                                @endphp
                            @endif

                            @forelse ($user->getRoleNames() as $role)
                                {{ $role . $phaseName }}
                            @empty
                                {{ 'unassigned' . $phaseName }}
                            @endforelse
                        </div>

                        <div class="mx-4 grid grid-cols-2">
                            <div>
                                @php
                                    $statusStyle = '';
                                    $icon = '';
                                    if ($user->account_status == 'ACTIVE') {
                                        $statusStyle = 'bg-green-400 text-green';
                                        $icon = '<i class="fa-regular fa-circle-check"></i>';
                                    } elseif ($user->account_status == 'INACTIVE') {
                                        $statusStyle = 'bg-red-400 text-red';
                                        $icon = '<i class="fa-solid fa-lock"></i>';
                                    } elseif ($user->account_status == 'DEACTIVATED') {
                                        $statusStyle = 'bg-gray-400 text-gray';
                                        $icon = '<i class="fa-solid fa-link-slash"></i>';
                                    }
                                @endphp
                                <span class="py-1 px-2 rounded-xl text-xs font-bold shadow {!! $statusStyle !!}">
                                    {!! $icon . '&nbsp;' . $user->account_status !!}
                                </span>
                            </div>
                            <div class="text-right w-full gap-2">
                                <button type="button" wire:click="showResetConfirmation('{{ $user->user_id }}')"
                                    class="py-1 px-2 text-xs rounded-xl bg-orange-300 md:text-orange-800 hover:bg-orange-400 shadow"
                                    title="RESET PASSWORD" wire:loading.attr="disabled"><i
                                        class="fa-solid fa-unlock-keyhole"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="md:col-span-3 text-center text-slate-800">
                <x-label class="text-xl font-black" value="{{ __('No records found!') }}" />
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>
        {{ $users->links() }}
    </div>

    {{-- Reset User Password Confirmation Modal --}}
    <x-confirmation-modal wire:model.live="confirmingResetUserPassword" maxWidth="md">
        <x-slot name="title">
            {{ __('Reset User Password') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to reset user password?') }}
        </x-slot>

        <x-slot name="footer">
            <div class="mr-2 flex align-middle" wire:loading wire:target="resetpassword">
                <x-label class="inline" value="{{ __('Resetting password...') }}" />
            </div>

            <x-button wire:click="resetpassword" wire:loading.attr="disabled">
                <i class="fa-solid fa-unlock-keyhole"></i>&nbsp;
                {{ __('Reset') }}
            </x-button>

            <x-secondary-button class="ms-3" wire:click="$toggle('confirmingResetUserPassword')"
                wire:loading.attr="disabled">
                <i class="fa-solid fa-circle-xmark"></i>&nbsp;
                {{ __('Cancel') }}
            </x-secondary-button>
        </x-slot>
    </x-confirmation-modal>
</div>
