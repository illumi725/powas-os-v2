<div class="py-4 px-4 space-y-4">
    <div class="w-full text-right">
        <label class="dark:text-white font-bold hidden md:inline" for="search">{{ __('Search: ') }}</label>
        <x-input id="search" name="search" wire:model.live="search" autocomplete="off" placeholder="Search..."/>
        <label class="dark:text-white font-bold hidden md:inline" for="pagination">{{ __('# of rows per page: ') }}</label>
        <x-combobox id="pagination" name="pagination" wire:model.live="pagination">
            <x-slot name="options">
                @for ($i = 10; $i <= 100; $i = $i + 10)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </x-slot>
        </x-combobox>
    </div>
    <x-table.table>
        <x-slot name="thead">
            <x.table.thead>
                <x-table.thead-th class="md:hidden w-3/4 ">
                    {{ __('Users') }}
                </x-table.thead-th>
                <x-table.thead-th class="hidden md:table-cell">
                    {{ __('User ID') }}
                </x-table.thead-th>
                <x-table.thead-th class="hidden md:table-cell">
                    {{ __('Username') }}
                </x-table.thead-th>
                <x-table.thead-th class="hidden md:table-cell">
                    {{ __('Email') }}
                </x-table.thead-th>
                <x-table.thead-th class="hidden md:table-cell">
                    {{ __('Account Status') }}
                </x-table.thead-th>
                <x-table.thead-th class="hidden md:table-cell">
                    {{ __('Role') }}
                </x-table.thead-th>
                <x-table.thead-th>
                    {{ __('Actions') }}
                </x-table.thead-th>
            </x.table.thead>
        </x-slot>

        <x-slot name="tbody">
            <tbody>
                @forelse ($userslist as $user)
                    @livewire('users.users-row', [
                        'user' => $user,
                        ], key($user->user_id))
                @empty
                    <x-table.tbody-tr class="text-xl">
                        <td colspan="5" class="py-2 pl-2">
                            <div class="flex md:justify-center md:items-center justify-left items-left">
                                <span class="text-xl">{{ __('No records found...') }}</span>
                            </div>
                        </td>
                    </x-table.tbody-tr>
                @endforelse
            </tbody>
        </x-slot>


    </x-table.table>
    <div>
        {{ $userslist->links() }}
    </div>

    <x-alert-message class="me-3" on="alert" />
</div>
