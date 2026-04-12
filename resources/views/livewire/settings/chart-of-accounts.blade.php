<x-settings-box>
    <x-slot name="title">
        {{ __('Chart of Accounts') }}
    </x-slot>
    <x-slot name="description">
        {{ __('Add chart of accounts based on account titles submitted to Bureau of Internal Revenue (BIR).') }}

        <div class="w-full">
            <x-section-border />
            <div class="w-full grid grid-cols-12 text-gray-700 dark:text-gray-300" x-data="{ expanded: '' }">
                <div class="col-span-12">
                    <span class="text-xs font-bold cursor-pointer"
                        @click="expanded = ('assets' === expanded) ? '' : 'assets'">{{ __('ASSETS') }}
                        &nbsp;
                        <span x-show="expanded !== 'assets'"><i class="fa-solid fa-chevron-right"></i></span>
                        <span x-show="expanded === 'assets'"><i class="fa-solid fa-chevron-down"></i></span>
                    </span>
                    <div x-show="expanded === 'assets'" class="grid grid-cols-12" x-collapse>
                        @php
                            $assetsCount = 0;
                        @endphp
                        @forelse ($chartofaccounts as $item)
                            @if ($item->account_type == 'ASSET')
                                <p class="text-xs italic col-span-1 text-center cursor-pointer underline hover:no-underline hover:font-bold hover:bg-gray-300 dark:hover:bg-gray-700 rounded-md"
                                    wire:click="loadAccount({{ $item }})"
                                    wire:key="{{ $item->account_number }}">
                                    {{ $item->account_number }}
                                </p>

                                <p class="text-xs col-span-11 ml-2 tooltip tooltip-right">{{ $item->account_name }}
                                    &nbsp;
                                    @if (trim($item->description) != '')
                                        <span class="tooltiptext">{{ $item->description }}</span>
                                    @endif

                                    <span class="align-center">
                                        @if ($item->normal_balance == 'DEBIT')
                                            <b
                                                class="bg-blue-400 text-blue-900 rounded-md px-2 text-center"><small>{{ __('DEBIT') }}</small></b>
                                        @else
                                            <b
                                                class="bg-red-400 text-red-900 rounded-md px-2 text-center"><small>{{ __('CREDIT') }}</small></b>
                                        @endif
                                    </span>
                                </p>
                                @php
                                    $assetsCount++;
                                @endphp
                            @endif
                        @empty

                        @endforelse

                        @if ($assetsCount == 0)
                            <p class="text-xs col-span-12 ml-2">{{ __('Assets List is empty!') }}</p>
                        @endif
                    </div>
                </div>

                <div class="col-span-12">
                    <span class="text-xs font-bold cursor-pointer"
                        @click="expanded = ('liabilities' === expanded) ? '' : 'liabilities'">{{ __('LIABILITIES') }}
                        &nbsp;
                        <span x-show="expanded !== 'liabilities'"><i class="fa-solid fa-chevron-right"></i></span>
                        <span x-show="expanded === 'liabilities'"><i class="fa-solid fa-chevron-down"></i></span>
                    </span>
                    <div x-show="expanded === 'liabilities'" class="grid grid-cols-12" x-collapse>
                        @php
                            $liabilitiesCount = 0;
                        @endphp
                        @forelse ($chartofaccounts as $item)
                            @if ($item->account_type == 'LIABILITY')
                                <p class="text-xs italic col-span-1 text-center cursor-pointer underline hover:no-underline hover:font-bold hover:bg-gray-300 dark:hover:bg-gray-700 rounded-md"
                                    wire:click="loadAccount({{ $item }})"
                                    wire:key="{{ $item->account_number }}">
                                    {{ $item->account_number }}
                                </p>

                                <p class="text-xs col-span-11 ml-2 tooltip tooltip-right">{{ $item->account_name }}
                                    &nbsp;
                                    @if (trim($item->description) != '')
                                        <span class="tooltiptext">{{ $item->description }}</span>
                                    @endif

                                    <span class="align-center">
                                        @if ($item->normal_balance == 'DEBIT')
                                            <b
                                                class="bg-blue-400 text-blue-900 rounded-md px-2 text-center"><small>{{ __('DEBIT') }}</small></b>
                                        @else
                                            <b
                                                class="bg-red-400 text-red-900 rounded-md px-2 text-center"><small>{{ __('CREDIT') }}</small></b>
                                        @endif
                                    </span>
                                </p>
                                @php
                                    $liabilitiesCount++;
                                @endphp
                            @endif
                        @empty

                        @endforelse

                        @if ($liabilitiesCount == 0)
                            <p class="text-xs col-span-12 ml-2">{{ __('Liabilities List is empty!') }}</p>
                        @endif
                    </div>
                </div>

                <div class="col-span-12">
                    <span class="text-xs font-bold cursor-pointer"
                        @click="expanded = ('equity' === expanded) ? '' : 'equity'">{{ __('POWAS EQUITY') }}
                        &nbsp;
                        <span x-show="expanded !== 'equity'"><i class="fa-solid fa-chevron-right"></i></span>
                        <span x-show="expanded === 'equity'"><i class="fa-solid fa-chevron-down"></i></span>
                    </span>
                    <div x-show="expanded === 'equity'" class="grid grid-cols-12" x-collapse>
                        @php
                            $equitiesCount = 0;
                        @endphp
                        @forelse ($chartofaccounts as $item)
                            @if ($item->account_type == 'EQUITY')
                                <p class="text-xs italic col-span-1 text-center cursor-pointer underline hover:no-underline hover:font-bold hover:bg-gray-300 dark:hover:bg-gray-700 rounded-md"
                                    wire:click="loadAccount({{ $item }})"
                                    wire:key="{{ $item->account_number }}">
                                    {{ $item->account_number }}
                                </p>

                                <p class="text-xs col-span-11 ml-2 tooltip tooltip-right">{{ $item->account_name }}
                                    &nbsp;
                                    @if (trim($item->description) != '')
                                        <span class="tooltiptext">{{ $item->description }}</span>
                                    @endif

                                    <span class="align-center">
                                        @if ($item->normal_balance == 'DEBIT')
                                            <b
                                                class="bg-blue-400 text-blue-900 rounded-md px-2 text-center"><small>{{ __('DEBIT') }}</small></b>
                                        @else
                                            <b
                                                class="bg-red-400 text-red-900 rounded-md px-2 text-center"><small>{{ __('CREDIT') }}</small></b>
                                        @endif
                                    </span>
                                </p>
                                @php
                                    $equitiesCount++;
                                @endphp
                            @endif
                        @empty

                        @endforelse

                        @if ($equitiesCount == 0)
                            <p class="text-xs col-span-12 ml-2">{{ __('POWAS Equity List is empty!') }}</p>
                        @endif
                    </div>
                </div>

                <div class="col-span-12">
                    <span class="text-xs font-bold cursor-pointer"
                        @click="expanded = ('revenues' === expanded) ? '' : 'revenues'">{{ __('REVENUES') }}
                        &nbsp;
                        <span x-show="expanded !== 'revenues'"><i class="fa-solid fa-chevron-right"></i></span>
                        <span x-show="expanded === 'revenues'"><i class="fa-solid fa-chevron-down"></i></span>
                    </span>
                    <div x-show="expanded === 'revenues'" class="grid grid-cols-12" x-collapse>
                        @php
                            $revenuesCount = 0;
                        @endphp
                        @forelse ($chartofaccounts as $item)
                            @if ($item->account_type == 'REVENUE')
                                <p class="text-xs italic col-span-1 text-center cursor-pointer underline hover:no-underline hover:font-bold hover:bg-gray-300 dark:hover:bg-gray-700 rounded-md"
                                    wire:click="loadAccount({{ $item }})"
                                    wire:key="{{ $item->account_number }}">
                                    {{ $item->account_number }}
                                </p>

                                <p class="text-xs col-span-11 ml-2 tooltip tooltip-right">{{ $item->account_name }}
                                    &nbsp;
                                    @if (trim($item->description) != '')
                                        <span class="tooltiptext">{{ $item->description }}</span>
                                    @endif

                                    <span class="align-center">
                                        @if ($item->normal_balance == 'DEBIT')
                                            <b
                                                class="bg-blue-400 text-blue-900 rounded-md px-2 text-center"><small>{{ __('DEBIT') }}</small></b>
                                        @else
                                            <b
                                                class="bg-red-400 text-red-900 rounded-md px-2 text-center"><small>{{ __('CREDIT') }}</small></b>
                                        @endif
                                    </span>
                                </p>
                                @php
                                    $revenuesCount++;
                                @endphp
                            @endif
                        @empty

                        @endforelse

                        @if ($revenuesCount == 0)
                            <p class="text-xs col-span-12 ml-2">{{ __('Revenues List is empty!') }}</p>
                        @endif
                    </div>
                </div>

                <div class="col-span-12">
                    <span class="text-xs font-bold cursor-pointer"
                        @click="expanded = ('expenses' === expanded) ? '' : 'expenses'">{{ __('EXPENSES') }}
                        &nbsp;
                        <span x-show="expanded !== 'expenses'"><i class="fa-solid fa-chevron-right"></i></span>
                        <span x-show="expanded === 'expenses'"><i class="fa-solid fa-chevron-down"></i></span>
                    </span>
                    <div x-show="expanded === 'expenses'" class="grid grid-cols-12" x-collapse>
                        @php
                            $expensesCount = 0;
                        @endphp
                        @forelse ($chartofaccounts as $item)
                            @if ($item->account_type == 'EXPENSE')
                                <p class="text-xs italic col-span-1 text-center cursor-pointer underline hover:no-underline hover:font-bold hover:bg-gray-300 dark:hover:bg-gray-700 rounded-md"
                                    wire:click="loadAccount({{ $item }})"
                                    wire:key="{{ $item->account_number }}">
                                    {{ $item->account_number }}
                                </p>

                                <p class="text-xs col-span-11 ml-2 tooltip tooltip-right">{{ $item->account_name }}
                                    &nbsp;
                                    @if (trim($item->description) != '')
                                        <span class="tooltiptext">{{ $item->description }}</span>
                                    @endif

                                    <span class="align-center">
                                        @if ($item->normal_balance == 'DEBIT')
                                            <b
                                                class="bg-blue-400 text-blue-900 rounded-md px-2 text-center"><small>{{ __('DEBIT') }}</small></b>
                                        @else
                                            <b
                                                class="bg-red-400 text-red-900 rounded-md px-2 text-center"><small>{{ __('CREDIT') }}</small></b>
                                        @endif
                                    </span>
                                </p>
                                @php
                                    $expensesCount++;
                                @endphp
                            @endif
                        @empty

                        @endforelse

                        @if ($expensesCount == 0)
                            <p class="text-xs col-span-12 ml-2">{{ __('Expenses List is empty!') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="contents">
        <div class="w-full">
            <div class="space-y-4" x-data="{ tableShow: false }">
                <form>
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="mt-2 md:mt-0">
                            <x-label for="accounttype" value="{{ __('Account Type') }}" />
                            <x-combobox name="accounttype" id="accounttype" class="mt-1 block w-full"
                                wire:model.live="accounttype" wire:change="setNormalBalance">
                                <x-slot name="options">
                                    <option value="" disabled>{{ __('-Select Account Type-') }}</option>
                                    <option value="ASSET">{{ __('ASSET') }}</option>
                                    <option value="LIABILITY">{{ __('LIABILITY') }}</option>
                                    <option value="EQUITY">{{ __('EQUITY') }}</option>
                                    <option value="REVENUE">{{ __('REVENUE') }}</option>
                                    <option value="EXPENSE">{{ __('EXPENSE') }}</option>
                                </x-slot>
                            </x-combobox>
                            <x-input-error for="accounttype" class="mt-1" />
                        </div>
                        <div class="mt-2 md:mt-0">
                            <x-label for="normalbalance" value="{{ __('Normal Balance') }}" />
                            <x-combobox name="normalbalance" id="normalbalance" class="mt-1 block w-full"
                                wire:model.live="normalbalance">
                                <x-slot name="options">
                                    <option value="" disabled>{{ __('-Select Normal Balance-') }}</option>
                                    <option value="DEBIT">{{ __('DEBIT') }}</option>
                                    <option value="CREDIT">{{ __('CREDIT') }}</option>
                                </x-slot>
                            </x-combobox>
                            <x-input-error for="normalbalance" class="mt-1" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
                        <div>
                            <x-label for="accountnumber" value="{{ __('Account Number') }}" />
                            <x-input class="block mt-1 w-full" wire:model="accountnumber" id="accountnumber"
                                type="number" name="accountnumber" autocomplete="off" :disabled="!$isCreatingNew" />
                            <x-input-error for="accountnumber" class="mt-1" />
                        </div>
                        <div>
                            <x-label for="accountname" value="{{ __('Account Name') }}" />
                            <x-input class="block mt-1 w-full" wire:model="accountname" id="accountname"
                                type="text" name="accountname" autocomplete="off" />
                            <x-input-error for="accountname" class="mt-1" />
                        </div>
                        <div>
                            <x-label for="alias" value="{{ __('Alias') }}" />
                            <x-input class="block mt-1 w-full" wire:model="alias" id="alias" type="text"
                                name="alias" autocomplete="off" />
                            <x-input-error for="alias" class="mt-1" />
                        </div>
                        <div class="md:col-span-3 mt-2">
                            <x-label for="description" value="{{ __('Description') }}" />
                            <x-textarea class="block mt-1 w-full" wire:model="description" id="description"
                                type="text" name="description" autocomplete="off"></x-textarea>
                            <x-input-error for="description" class="mt-1" />
                        </div>
                        <div class="md:col-span-3 mt-2 text-end items-center grid grid-rows-2 gap-1">
                            <x-alert-message class="me-3" on="coaMessage" />
                            <div class="ml-4 inline">
                                <x-label
                                    class="inline hover:font-bold cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700 rounded-xl px-1 py-1"
                                    @click="tableShow = !tableShow; $refs.table.focus">
                                    &nbsp;<span
                                        x-text="tableShow ? '{{ __('Hide') }}' : '{{ __('Show') }}'"></span>&nbsp;{{ __('Table') }}
                                </x-label>

                                @can('view logs')
                                    @livewire('logs.coa-logs')
                                @endcan

                                @if (Auth::user()->hasRole('admin'))
                                    <x-label
                                        class="inline hover:font-bold cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700 rounded-xl px-1 py-1"
                                        wire:click="populateFromJSONFile">
                                        &nbsp;<span>{{ __('Import Default COA') }}</span>
                                    </x-label>
                                @endif
                            </div>
                            @if (Auth::user()->hasRole('admin'))
                                <div>
                                    <x-button type="button" wire:click="saveAccount" wire:loading.attr="disabled">
                                        @if ($isCreatingNew == true)
                                            <i class="fa-regular fa-floppy-disk"></i>
                                            <span>&nbsp;{{ __('Save') }}</span>
                                        @else
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            <span>&nbsp;{{ __('Update') }}</span>
                                        @endif
                                    </x-button>

                                    @if ($isCreatingNew == false)
                                        <div class="inline">
                                            <x-danger-button wire:loading.attr="disabled" type="button"
                                                wire:click="confirmActionTaken('delete')">
                                                <i class="fa-solid fa-trash"></i>
                                                <span>&nbsp;{{ __('Delete') }}</span>
                                            </x-danger-button>
                                            <x-secondary-button wire:loading.attr="disabled" type="button"
                                                wire:click="cancel">
                                                <i class="fa-regular fa-circle-xmark"></i>
                                                <span>&nbsp;{{ __('Cancel') }}</span>
                                            </x-secondary-button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="space-y-4 overflow-hidden" x-show="tableShow" {{-- x-transition:enter="transition-all ease-out duration-300"
                    x-transition:enter-start="transform scale-y-0"
                    x-transition:enter-end="transform scale-y-100"
                    x-transition:leave="transition-all ease-in duration-300"
                    x-transition:leave-start="transform scale-y-100"
                    x-transition:leave-end="transform scale-y-0" --}} x-ref="table"
                    x-collapse>
                    {{-- <x-section-border/> --}}

                    <div class="w-full text-right">
                        <label class="dark:text-white font-bold hidden lg:inline"
                            for="search">{{ __('Search: ') }}</label>
                        <x-input id="search" name="search" wire:model.live="search" autocomplete="off"
                            placeholder="Search..." />
                        {{-- <label class="dark:text-white font-bold hidden lg:inline"
                            for="pagination">{{ __('# of rows per page: ') }}</label>
                        <x-combobox id="pagination" name="pagination" wire:model.live="pagination">
                            <x-slot name="options">
                                @for ($i = 10; $i <= 100; $i = $i + 10)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </x-slot>
                        </x-combobox> --}}
                    </div>

                    <x-table.table class="text-xs">
                        <x-slot name="thead">
                            <x-table.thead>
                                <x-table.thead-th class="md:hidden w-full">
                                    {{ __('Account Titles') }}
                                </x-table.thead-th>
                                <x-table.thead-th class="hidden md:table-cell">
                                    {{ __('Account Number') }}
                                </x-table.thead-th>
                                <x-table.thead-th class="hidden md:table-cell">
                                    {{ __('Account Name') }}
                                </x-table.thead-th>
                                <x-table.thead-th class="hidden md:table-cell">
                                    {{ __('Description') }}
                                </x-table.thead-th>
                                <x-table.thead-th class="hidden md:table-cell">
                                    {{ __('Account Type') }}
                                </x-table.thead-th>
                                <x-table.thead-th class="hidden md:table-cell">
                                    {{ __('Normal Balance') }}
                                </x-table.thead-th>
                            </x-table.thead>
                        </x-slot>
                        <x-slot name="tbody">
                            <tbody>
                                @forelse ($accountlist as $account)
                                    <x-table.tbody-tr wire:click="loadAccount({{ $account }})"
                                        wire:key="{{ $account->account_number }}">
                                        <x-table.tbody-td class="md:hidden text-left pl-2 w-full">
                                            <div>
                                                <em>
                                                    <small>
                                                        {{ $account->account_number . __(' - ') . $account->account_type }}
                                                    </small>
                                                </em>
                                            </div>
                                            <div>
                                                <b>{{ $account->account_name }}</b>
                                            </div>
                                            <div>
                                                <small>{{ $account->description }}</small>
                                            </div>
                                            <div>
                                                <small><em><b>{{ $account->normal_balance }}</b></em></small>
                                            </div>
                                        </x-table.tbody-td>
                                        <th class="hidden md:table-cell">
                                            {{ $account->account_number }}
                                        </th>
                                        <x-table.tbody-td class="hidden md:table-cell pl-2">
                                            {{ $account->account_name }}
                                        </x-table.tbody-td>
                                        <x-table.tbody-td class="hidden md:table-cell pl-2">
                                            {{ $account->description }}
                                        </x-table.tbody-td>
                                        <x-table.tbody-td class="hidden md:table-cell pl-2 text-center">
                                            {{ $account->account_type }}
                                        </x-table.tbody-td>
                                        <x-table.tbody-td class="hidden md:table-cell pl-2 text-center">
                                            {{ $account->normal_balance }}
                                        </x-table.tbody-td>
                                    </x-table.tbody-tr>
                                @empty
                                    <x-table.tbody-tr class="text-xl">
                                        <td colspan="5" class="py-2 pl-2">
                                            <div
                                                class="flex md:justify-center md:items-center justify-left items-left">
                                                <span class="text-xl">{{ __('No records found...') }}</span>
                                            </div>
                                        </td>
                                    </x-table.tbody-tr>
                                @endforelse
                            </tbody>
                        </x-slot>
                    </x-table.table>
                </div>
            </div>
        </div>

        {{-- Take Action Confirmation Modal --}}
        <x-confirmation-modal wire:model.live="confirmingActionTaken" maxWidth="md">
            <x-slot name="title">
                @if ($actionTaken == 'delete')
                    {{ __('Confirm Account Deletion...') }}
                @elseif ($actionTaken == 'update')
                    {{ __('Confirm Account Update...') }}
                @endif
            </x-slot>

            <x-slot name="content">
                {{ __('Are you sure you would like to ') . $actionTaken . __(' this account?') }}
            </x-slot>

            <x-slot name="footer">
                @if ($actionTaken == 'delete')
                    <x-button wire:click="delete({{ $accountnumber }})" wire:loading.attr="disabled">
                        <i class="fa-solid fa-check"></i>&nbsp;
                        {{ __('Yes') }}
                    </x-button>
                @elseif ($actionTaken == 'update')
                    <x-button wire:click="delete({{ $accountnumber }})" wire:loading.attr="disabled">
                        <i class="fa-solid fa-check"></i>&nbsp;
                        {{ __('Yes') }}
                    </x-button>
                @endif

                <x-danger-button class="ms-3" wire:click="$toggle('confirmingActionTaken')"
                    wire:loading.attr="disabled">
                    <i class="fa-solid fa-circle-xmark"></i>&nbsp;
                    {{ __('No') }}
                </x-danger-button>
            </x-slot>
        </x-confirmation-modal>
    </x-slot>
</x-settings-box>
