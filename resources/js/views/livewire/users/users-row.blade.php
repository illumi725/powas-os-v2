<x-table.tbody-tr>
    <x-table.tbody-td class="md:hidden text-left pl-2 w-3/4">
        <div>
            <em>
                <small>
                    {{ $user->user_id }}

                    @foreach ($user->getRoleNames() as $role)
                        {{ __('[') . $role . __(']') }}
                    @endforeach
                </small>
            </em>
        </div>
        <div>
            <b>{{ $user->username }}</b>
        </div>
        <div>
            <small>{{ $user->email }}</small>
        </div>
        <div>
            <small><em><b>{{ $user->account_status }}</b></em></small>
        </div>
    </x-table.tbody-td>
    <th class="hidden md:table-cell">
        {{ $user->user_id }}
    </th>
    <x-table.tbody-td class="hidden md:table-cell pl-2">
        {{ $user->username }}
    </x-table.tbody-td>
    <x-table.tbody-td class="hidden md:table-cell pl-2">
        {{ $user->email }}
    </x-table.tbody-td>
    <x-table.tbody-td class="hidden md:table-cell text-center">
        {{ $user->account_status }}
    </x-table.tbody-td>
    <x-table.tbody-td class="hidden md:table-cell text-center">
        <div class="tooltip tooltip-top">
            @foreach ($user->getRoleNames() as $role)
                {{ $role }}
            @endforeach
            <span class="tooltiptext">
                <ul>
                    @forelse ($user->getPermissionNames() as $permission)
                        <li>{{ $permission }}</li>
                    @empty
                        <li>{{ __('No permisions provided!') }}</li>
                    @endforelse
                </ul>
            </span>
        </div>
    </x-table.tbody-td>
    <x-table.tbody-td class="text-center space-y-1">
        @if (Auth::user()->hasRole('admin'))
            <div class="md:inline">
                <x-secondary-button {{-- href="/powas/{{$powas->powas_id}}" --}} wire:click="resetpassword" wire:loading.attr="disabled">
                    <i class="fa-solid fa-unlock-keyhole"></i>
                    <span class="hidden md:inline">&nbsp;{{ __('Reset Password') }}</span>
                </x-secondary-button>
            </div>
            <div class="md:inline">
                <x-button-link {{-- href="/powas/{{$powas->powas_id}}" --}} wire:loading.attr="disabled">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span class="hidden md:inline">&nbsp;{{ __('Edit') }}</span>
                </x-button-link>
            </div>

            <div class="md:inline">
                <x-danger-button type="button" {{-- wire:click="showDeleteConfirmationModal" --}}>
                    <i class="fa-solid fa-trash"></i>
                    <span class="hidden md:inline">&nbsp;{{ __('Delete') }}</span>
                </x-danger-button>
            </div>
        @else
        @endif
    </x-table.tbody-td>
</x-table.tbody-tr>
