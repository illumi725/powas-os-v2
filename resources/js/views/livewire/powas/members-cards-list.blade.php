<div class="py-4 px-4 space-y-4" x-data="{ expanded: '' }">
    <x-alert-message class="me-3" on="alert" />

    {{-- Filter --}}
    <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4">
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
                @livewire('logs.member-logs')
            @endcan
        </div>
        <div class="hidden md:inline">
            @can('add member')
                <div class="w-full flex justify-end">
                    <x-button type="button" wire:click="showAddMemberModal" wire:loading.attr="disabled">
                        <i class="fa-solid fa-plus"></i>
                        <span>&nbsp;{{ __('Add Member') }}</span>
                    </x-button>
                </div>

                {{-- Add Member Dialog --}}
                <x-dialog-modal wire:model.live="showingAddMemberModal" maxWidth="lg">
                    <x-slot name="title">
                        {{ __('Choose Action') }}
                    </x-slot>
                    <x-slot name="content">
                        @if (!empty($errorList))
                            <div>
                                <p class="mb-4 font-bold">
                                    {{ __('Adding member is not possible due to the following issues:') }}</p>
                                <ul style="list-style-type:disc" class="ml-4">
                                    @foreach ($errorList as $error)
                                        <li class="my-3">{!! $error !!}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-3 text-center">
                                <x-label
                                    class="inline hover:font-bold cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700 rounded-xl px-1 py-1"
                                    wire:click="showPOWASSelectorModal" wire:loading.attr="disabled">
                                    &nbsp;<span>{{ __('Create Excel Template') }}</span>
                                </x-label>

                                <x-label
                                    class="inline hover:font-bold cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700 rounded-xl px-1 py-1"
                                    wire:click="showExcelImportModal" wire:loading.attr="disabled">
                                    &nbsp;<span>{{ __('Import Excel Template') }}</span>
                                </x-label>

                                @if (Auth::user()->hasRole('admin'))
                                    <x-label
                                        class="inline hover:font-bold cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700 rounded-xl px-1 py-1"
                                        wire:click="showPOWASSelectorModalForManual" wire:loading.attr="disabled">
                                        &nbsp;<span>{{ __('Add POWAS Application Manually') }}</span>
                                    </x-label>
                                @else
                                    <x-label
                                        class="inline hover:font-bold cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700 rounded-xl px-1 py-1"
                                        wire:click="showPOWASSelectorModalForManual" wire:loading.attr="disabled">
                                        &nbsp;<span>{{ __('Add POWAS Application Manually') }}</span>
                                    </x-label>
                                @endif
                            </div>
                        @endif
                    </x-slot>

                    <x-slot name="footer">
                        <x-danger-button wire:click="$toggle('showingAddMemberModal')" wire:loading.attr="disabled">
                            <i class="fa-regular fa-circle-xmark"></i>
                            <span>&nbsp;{{ __('Cancel') }}</span>
                        </x-danger-button>
                    </x-slot>
                </x-dialog-modal>

                @if (Auth::user()->hasRole('admin|treasurer|secretary'))
                    {{-- POWAS Selector --}}
                    <x-dialog-modal wire:model.live="showingPOWASSelectorModal" maxWidth="lg">
                        <x-slot name="title">
                            {{ __('Choose POWAS') }}
                        </x-slot>
                        <x-slot name="content">
                            {{-- @dd($powasSelections) --}}
                            <div class="grid grid-cols-1 md:grid-cols-6 md:gap-2">
                                <div class="md:col-span-4">
                                    <x-label class="inline" for="selectedPOWAS" value="{{ __('POWAS ID') }}" />
                                    <x-input wire:model.live="selectedPOWAS" id="selectedPOWAS" class="block mt-1 w-full"
                                        type="text" name="selectedPOWAS" autocomplete="off" list="powasList"
                                        disabled="{{ Auth::user()->hasRole('treasurer|secretary') }}" />
                                    <datalist id="powasList">
                                        @foreach ($powasSelections as $item => $value)
                                            <option value="{{ $value->powas_id }}">
                                                {{ $value->barangay . ' ' . $value->phase . ' (' . $value->zone . ') - ' . $value->municipality . ', ' . $value->province }}
                                            </option>
                                        @endforeach
                                    </datalist>
                                    <x-input-error for="selectedPOWAS" class="mt-1" />
                                </div>
                                <div class="col-span-2">
                                    <x-label class="inline" for="numberOfMembers" value="{{ __('# of members') }}" />
                                    <x-input wire:model.live="numberOfMembers" id="numberOfMembers"
                                        class="block mt-1 w-full" type="number" name="numberOfMembers"
                                        autocomplete="off" />
                                    <x-input-error for="numberOfMembers" class="mt-1" />
                                </div>
                            </div>
                        </x-slot>

                        <x-slot name="footer">
                            <x-button wire:click="createCSVTemplate('{{ $selectedPOWAS }}')" class="mr-2"
                                wire:loading.attr="disabled">
                                <i class="fa-solid fa-file-circle-plus"></i>
                                <span>&nbsp;{{ __('Create') }}</span>
                            </x-button>
                            <x-danger-button wire:click="$toggle('showingPOWASSelectorModal')" wire:loading.attr="disabled">
                                <i class="fa-regular fa-circle-xmark"></i>
                                <span>&nbsp;{{ __('Cancel') }}</span>
                            </x-danger-button>
                        </x-slot>
                    </x-dialog-modal>
                @endif

                @if (Auth::user()->hasRole('admin'))
                    {{-- POWAS Selector --}}
                    <x-dialog-modal wire:model.live="showingPOWASSelectorModalForManual" maxWidth="lg">
                        <x-slot name="title">
                            {{ __('Choose POWAS') }}
                        </x-slot>
                        <x-slot name="content">
                            {{-- @dd($powasSelections) --}}
                            <div class="grid grid-cols-1 md:grid-cols-6 md:gap-2">
                                <div class="md:col-span-6">
                                    <x-label class="inline" for="selectedPOWAS" value="{{ __('POWAS ID') }}" />
                                    <x-input wire:model.live="selectedPOWAS" id="selectedPOWAS" class="block mt-1 w-full"
                                        type="text" name="selectedPOWAS" autocomplete="off" list="powasList" />
                                    <datalist id="powasList">
                                        @foreach ($powasSelections as $item => $value)
                                            <option value="{{ $value->powas_id }}">
                                                {{ $value->barangay . ' ' . $value->phase . ' (' . $value->zone . ') - ' . $value->municipality . ', ' . $value->province }}
                                            </option>
                                        @endforeach
                                    </datalist>
                                    <x-input-error for="selectedPOWAS" class="mt-1" />
                                </div>
                            </div>
                        </x-slot>

                        <x-slot name="footer">
                            <x-button wire:click="addMemberManually('{{ $selectedPOWAS }}')" class="mr-2"
                                wire:loading.attr="disabled">
                                <i class="fa-solid fa-file-circle-plus"></i>
                                <span>&nbsp;{{ __('Add Member') }}</span>
                            </x-button>
                            <x-danger-button wire:click="$toggle('showingPOWASSelectorModalForManual')"
                                wire:loading.attr="disabled">
                                <i class="fa-regular fa-circle-xmark"></i>
                                <span>&nbsp;{{ __('Cancel') }}</span>
                            </x-danger-button>
                        </x-slot>
                    </x-dialog-modal>
                @endif

                {{-- Import Excel File --}}
                <x-dialog-modal wire:model.live="showingExcelImportModal" maxWidth="lg">
                    <x-slot name="title">
                        {{ __('Select Excel File to Import') }}
                    </x-slot>
                    <x-slot name="content">
                        {{-- @dd($powasSelections) --}}
                        <div>
                            <x-input id="excelFile" class="block mt-1 w-full" type="file" wire:model="excelFile"
                                accept=".xlsx" />
                            {{-- @if ($id_path)
                        <div class="mt-3 w-full text-center">
                            <img class="w-96" src="{{ $id_path->temporaryUrl() }}" alt="Preview">
                        </div>
                    @endif --}}
                            <x-input-error for="excelFile" class="mt-1" />
                            <div class="mr-2 flex align-middle" wire:loading wire:target="importExcelFile">
                                <x-label class="inline" value="{{ __('Importing...') }}" />
                            </div>
                        </div>
                    </x-slot>
                    <x-slot name="footer">
                        <div class="w-full grid grid-cols-3 gap-2">
                            <x-secondary-button wire:click="showImportData" class="w-full" wire:loading.attr="disabled">
                                <i class="fa-solid fa-eye"></i>
                                <span>&nbsp;{{ __('View') }}</span>
                            </x-secondary-button>
                            <x-button wire:click="importExcelFile" class="w-full" wire:loading.attr="disabled">
                                <i class="fa-solid fa-upload"></i>
                                <span>&nbsp;{{ __('Import') }}</span>
                            </x-button>
                            <x-danger-button wire:click="$toggle('showingExcelImportModal')" class="w-full"
                                wire:loading.attr="disabled">
                                <i class="fa-regular fa-circle-xmark"></i>
                                <span>&nbsp;{{ __('Cancel') }}</span>
                            </x-danger-button>
                        </div>
                    </x-slot>
                </x-dialog-modal>

                {{-- View Import Data --}}
                <x-import-viewer wire:model.live="showingImportDataModal">
                    <x-slot name="title">
                        {{ __('Import Viewer') }}
                    </x-slot>
                    <x-slot name="content">
                        <div class="overflow-x-auto overflow-y-auto w-full max-h-[600px]">
                        <table class="table-auto min-w-max">
                            <thead>
                                <tr>
                                    <th class="px-3">{{ __('SL#') }}</th>
                                    <th class="px-3">{{ __('Full Name') }}</th>
                                    <th class="px-3">{{ __('Birthday') }}</th>
                                    <th class="px-3">{{ __('Birthplace') }}</th>
                                    <th class="px-3">{{ __('Gender') }}</th>
                                    <th class="px-3">{{ __('Contact Number') }}</th>
                                    <th class="px-3">{{ __('Civil Status') }}</th>
                                    <th class="px-3">{{ __('Home Address') }}</th>
                                    <th class="px-3">{{ __('Present Address') }}</th>
                                    <th class="px-3">{{ __('# of Family Members') }}</th>
                                    <th class="px-3">{{ __('Application Status') }}</th>
                                    <th class="px-3">{{ __('Meter Number') }}</th>
                                    <th class="px-3">{{ __('Membership Date') }}</th>
                                    <th class="px-3">{{ __('First 50') }}</th>
                                    <th class="px-3">{{ __('Land Owner') }}</th>
                                    <th class="px-3">{{ __('Account Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $slotNumber = 0;
                                @endphp
                                @forelse ($importCollection as $item)
                                    <tr
                                        class="hover:bg-gray-300 dark:hover:bg-gray-100 hover:text-gray-900 py-2 cursor-pointer">
                                        <td class="px-3 text-center">{{ $slotNumber + 1 }}</td>
                                        <td class="px-3">
                                            {{ $item['lastname'] . ', ' . $item['firstname'] . ' ' . $item['middlename'] }}
                                        </td>
                                        <td class="px-3">
                                            {{ \Carbon\Carbon::parse(PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['birthday']))->format('F d, Y') }}
                                        </td>
                                        <td class="px-3">{{ $item['birthplace'] }}</td>
                                        <td class="px-3 text-center">{{ $item['gender'] }}</td>
                                        <td class="px-3">{{ $item['contact_number'] }}</td>
                                        <td class="px-3 text-center">{{ $item['civil_status'] }}</td>
                                        <td class="px-3">
                                            {{ $item['address1'] . ', ' . $item['barangay'] . ', ' . $item['municipality'] . ', ' . $item['province'] . ' - ' . $item['region'] }}
                                        </td>
                                        <td class="px-3">{{ $item['present_address'] }}</td>
                                        <td class="px-3 text-center">{{ $item['family_members'] }}</td>
                                        <td class="px-3 text-center">{{ $item['application_status'] }}</td>
                                        <td class="px-3">{{ $item['meter_number'] }}</td>
                                        <td class="px-3">
                                            {{ \Carbon\Carbon::parse(PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['membership_date']))->format('F d, Y') }}
                                        </td>
                                        <td class="px-3 text-center">{{ $item['firstfifty'] }}</td>
                                        <td class="px-3 text-center">{{ $item['land_owner'] }}</td>
                                        <td class="px-3 text-center">{{ $item['member_status'] }}</td>
                                        @php
                                            $slotNumber++;
                                        @endphp
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </x-slot>
                    <x-slot name="footer">
                        <x-danger-button wire:click="$toggle('showingImportDataModal')" wire:loading.attr="disabled">
                            <i class="fa-regular fa-circle-xmark"></i>
                            <span>&nbsp;{{ __('Cancel') }}</span>
                        </x-danger-button>
                    </x-slot>
                </x-import-viewer>
            @endcan
        </div>

        <div class="md:col-span-2">
            {{-- Search and Pagination Control --}}
            <div x-show="expanded === 'filter'" class="grid grid-cols-1 md:grid-cols-2 gap-4 overflow-hidden"
                x-collapse>
                <div class="w-full md:col-span-2 grid grid-cols-1 md:grid-cols-4 gap-4">

                    @if (Auth::user()->hasRole('admin'))
                        <div class="w-full">
                            <x-label for="region" value="{{ __('Region: ') }}" />
                            <x-combobox class="mt-1 block w-full" id="region" name="region"
                                wire:model.live="region" wire:change="loadprovince">
                                <x-slot name="options">
                                    <option value="" disabled>{{ __('-Select Region-') }}</option>
                                    @foreach ($regionlist as $regionname)
                                        <option value="{{ $regionname }}">{{ $regionname }}</option>
                                    @endforeach
                                </x-slot>
                            </x-combobox>
                        </div>
                        <div class="w-full">
                            <x-label for="province" value="{{ __('Province: ') }}" />
                            <x-combobox class="mt-1 block w-full" id="province" name="province"
                                wire:model.live="province" wire:change="loadmunicipality">
                                <x-slot name="options">
                                    <option value="" disabled>{{ __('-Select Province-') }}</option>
                                    @foreach ($provincelist as $provincename)
                                        <option value="{{ $provincename }}">{{ $provincename }}</option>
                                    @endforeach
                                </x-slot>
                            </x-combobox>
                        </div>
                        <div class="w-full">
                            <x-label for="municipality" value="{{ __('Municipality: ') }}" />
                            <x-combobox class="mt-1 block w-full" id="municipality" name="municipality"
                                wire:model.live="municipality" wire:change="loadpowas">
                                <x-slot name="options">
                                    <option value="" disabled>{{ __('-Select Municipality-') }}</option>
                                    @foreach ($municipalitylist as $municipalityname)
                                        <option value="{{ $municipalityname }}">{{ $municipalityname }}</option>
                                    @endforeach
                                </x-slot>
                            </x-combobox>
                        </div>
                        <div class="w-full">
                            <x-label for="powas" value="{{ __('POWAS: ') }}" />
                            <x-combobox class="mt-1 block w-full" id="powas" name="powas"
                                wire:model.live="powas">
                                <x-slot name="options">
                                    <option value="" disabled>{{ __('-Select POWAS-') }}</option>
                                    @forelse ($powaslist as $powas)
                                        <option value="{{ $powas->powas_id }}">
                                            {{ $powas->barangay . ' POWAS ' . $powas->phase . ' (' . $powas->zone . ')' }}
                                        </option>
                                    @empty
                                    @endforelse
                                </x-slot>
                            </x-combobox>
                        </div>

                        <div class="md:col-span-2 flex items-center">
                            <x-input-error for="powas" class="mt-1" />
                        </div>
                    @else
                        <div class="md:col-span-2 flex items-center">

                        </div>
                    @endif

                    <div class="w-full">
                        <x-label class="inline" for="search" value="{{ __('Search: ') }}" />
                        <x-input class="w-full block" id="search" name="search" wire:model.live="search"
                            autocomplete="off" placeholder="Search..." />
                    </div>
                    <div class="w-full">
                        <x-label class="inline" for="pagination" value="{{ __('# of rows per page: ') }}" />
                        <x-combobox class="w-full block" id="pagination" name="pagination"
                            wire:model.live="pagination">
                            <x-slot name="options">
                                @for ($i = 12; $i <= 120; $i = $i + 12)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </x-slot>
                        </x-combobox>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Members Cards List --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse ($members as $member)
            <div class="text-slate-800 border border-slate-400 dark:border-none dark:bg-slate-300 shadow-md hover:scale-105 dark:hover:scale-105 rounded-lg"
                wire:key="{{ $member->member_id }}">
                <div class="py-4 rounded-lg border-b-8 border-slate-400 dark:border-slate-600">
                    <div class="mx-4 text-sm italic font-black grid grid-cols-2">
                        <div class="text-sm italic font-black w-full">
                            {{ $member->member_id }}
                        </div>

                        @canany([
                            'edit member',
                            'delete member',
                            'add reading',
                            'create billing',
                            'create bill
                            payment',
                            ])
                            <div class="inline w-full">
                                <div class="flex justify-end">
                                    <div class="ms-3 relative">
                                        <x-dropdown align="right" width="48">
                                            <x-slot name="trigger" class="text-right">
                                                <button
                                                    class="py-1 px-2 text-xs rounded-xl bg-blue-300 md:text-blue-800 hover:bg-blue-400 shadow font-bold">
                                                    {{ __('ACTIONS') }}
                                                </button>
                                            </x-slot>

                                            <x-slot name="content">
                                                <div class="not-italic font-normal">
                                                    @can('add reading')
                                                        <x-dropdown-link href="#"
                                                            wire:click="showAddReadingModal('{{ $member->member_id }}')"
                                                            class="text-xs py-1 my-0">
                                                            {{ app(\App\Livewire\Powas\MembersList::class)->getReadingStatus($member->member_id) . __(' READING') }}
                                                        </x-dropdown-link>
                                                    @endcan
                                                    @can('edit member')
                                                        <x-dropdown-link
                                                            href="{{ route('member-info', ['memberID' => $member->member_id]) }}"
                                                            class="text-xs py-1 my-0">
                                                            {{ __('EDIT PROFILE') }}
                                                        </x-dropdown-link>
                                                    @endcan
                                                </div>
                                            </x-slot>
                                        </x-dropdown>
                                    </div>
                                </div>
                            </div>
                        @endcanany
                    </div>
                    <div class="mx-4 font-bold w-full whitespace-nowrap overflow-hidden">
                        {{ $member->lastname . ', ' . $member->firstname . ' ' . $member->middlename }}
                    </div>
                    <div class="mx-4 text-xs italic whitespace-nowrap overflow-hidden">
                        {{ $member->address1 . ', ' . $member->barangay . ', ' . $member->municipality . ', ' . $member->province }}
                    </div>

                    <div class="mx-4 text-xs font-bold">
                        {{ $member->barangay . ' POWAS ' . $member->phase }}
                    </div>

                    <div class="mx-4 text-xs">
                        {{ 'Member since ' . Carbon\Carbon::parse($member->membership_date)->format('F d, Y') }}
                    </div>

                    <div class="mx-4 grid grid-cols-2">
                        <div>
                            @php
                                $statusStyle = '';
                                $icon = '';
                                if ($member->member_status == 'ACTIVE') {
                                    $statusStyle = 'bg-green-400 text-green';
                                    $icon = '<i class="fa-regular fa-circle-check"></i>';
                                } elseif ($member->member_status == 'LOCKED') {
                                    $statusStyle = 'bg-red-400 text-red';
                                    $icon = '<i class="fa-solid fa-lock"></i>';
                                } elseif ($member->member_status == 'DISCONNECTED') {
                                    $statusStyle = 'bg-gray-400 text-gray';
                                    $icon = '<i class="fa-solid fa-link-slash"></i>';
                                }
                            @endphp
                            <span
                                class="py-1 px-2 rounded-xl text-xs font-bold shadow {!! $statusStyle !!}">{!! $icon . '&nbsp;' . $member->member_status !!}</span>
                        </div>
                        {{-- <div class="text-right w-full gap-2">
                            @can('edit powas')
                                <a href="{{ route('powas.show', ['powas_id' => $powas->powas_id]) }}" class="py-1 px-2 text-xs rounded-xl bg-blue-300 md:text-blue-800 hover:bg-blue-400 shadow" title="EDIT"><i class="fa-solid fa-pen-to-square"></i></a>
                            @endcan

                            @can('delete powas')
                                <button type="button" class="py-1 px-2 text-xs rounded-xl bg-red-300 md:text-red-800 hover:bg-red-400 shadow" title="DELETE" wire:click="showDeleteConfirmationModal({{ $powas }})"><i class="fa-solid fa-trash"></i></button>
                            @endcan
                        </div> --}}
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-3 text-center text-slate-800 my-16">
                <x-label class="text-xl font-black" value="{{ __('No records found!') }}" />
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>
        {{ $members->links() }}
    </div>

    {{-- Add Reading Modal --}}
    @isset($selectedMemberID)
        <x-dialog-modal wire:model.live="showingAddReadingModal" maxWidth="sm">
            @slot('title')
                <span>
                    {{ __('Add Reading') }}
                </span>
            @endslot
            @slot('content')
                @php
                    $memberInfo = \App\Models\PowasMembers::join(
                        'powas_applications',
                        'powas_members.application_id',
                        '=',
                        'powas_applications.application_id',
                    )
                        ->where('powas_members.member_id', $selectedMemberID)
                        ->first();
                @endphp

                @if ($transactionStatus == 'YES')
                    <div class="w-full my-4 text-center">
                        <span class="text-base text-red-600 dark:text-red-400 font-black">
                            {{ __('This bill for the month of ') . $billingMonth . ' is already settled!' }}
                        </span>
                    </div>
                @else
                    <form class="w-full" wire:submit.prevent="confirmSave" method="post">
                        @csrf
                        <div class="w-full grid grid-cols-2 gap-1">
                            <div class="w-full col-span-2 grid grid-cols-2 py-1 px-1 border border-dashed rounded-md mb-4">
                                <div class="w-full">
                                    <x-label value="{{ __('Reading ID: ') }}" />
                                </div>
                                <div class="w-full">
                                    <x-label class="inline font-bold" value="{{ $readingIDs }}" />
                                </div>

                                <div class="w-full">
                                    <x-label value="{{ __('Account Number: ') }}" />
                                </div>
                                <div class="w-full">
                                    <x-label class="inline font-bold" value="{{ $selectedMemberID }}" />
                                </div>

                                <div class="w-full">
                                    <x-label value="{{ __('Account Name: ') }}" />
                                </div>
                                <div class="w-full">
                                    <x-label class="inline font-bold"
                                        value="{{ $memberInfo->lastname . ', ' . $memberInfo->firstname }}" />
                                </div>

                                <div class="w-full flex items-center">
                                    <x-label value="{{ __('Reading Count: ') }}" />
                                </div>

                                <div class="w-full">
                                    <div class="w-full">
                                        <x-input type="number" class="w-full text-right" wire:model.live="readingCounts"
                                            disabled="{{ !$isInitialReading }}" />
                                    </div>
                                    <div class="w-full col-span-2">
                                        <x-input-error class="text-sm" for="readingCounts" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full grid grid-cols-2">
                            <div class="w-full flex items-center">
                                <x-label for="readingDate" value="{{ __('Reading Date: ') }}" />
                            </div>
                            <div class="w-full">
                                <x-input type="date" class="w-full" wire:model.live="readingDate" />
                            </div>
                            <div class="w-full col-span-2">
                                <x-input-error class="text-sm" for="readingDate" />
                            </div>
                        </div>

                        <div class="w-full grid grid-cols-2">
                            @if ($previousReadingDate == null)
                                <div class="w-full flex items-center">
                                    <x-label for="previousReading" value="{{ __('Previous Reading: ') }}" />
                                </div>
                            @else
                                <div class="w-full grid grid-rows-2">
                                    <x-label for="previousReading" value="{{ __('Previous Reading: ') }}" />
                                    <x-label class="italic" for="previousReading" value="{{ $previousReadingDate }}" />
                                </div>
                            @endif

                            <div class="w-full">
                                <x-input type="number" class="w-full text-right" wire:model.live="previousReading"
                                    disabled />
                            </div>
                            {{-- <div class="w-full col-span-2">
                            <x-input-error class="text-sm" for="previousReading" />
                        </div> --}}
                        </div>

                        <div class="w-full grid grid-cols-2">
                            <div class="w-full flex items-center">
                                <x-label for="presentReading" value="{{ __('Present Reading: ') }}" />
                            </div>
                            <div class="w-full">
                                <x-input type="number" class="w-full text-right" wire:model.live="presentReading"
                                    autofocus />
                            </div>
                            <div class="w-full col-span-2">
                                <x-input-error class="text-sm" for="presentReading" />
                            </div>
                        </div>
                    </form>
                @endif
            @endslot
            @slot('footer')
                @if ($transactionStatus == 'NO')
                    @canany(['add reading', 'edit reading'])
                        <x-secondary-button type="button" wire:click="confirmSave" wire:loading.attr="disabled">
                            {{ __('Save') }}
                        </x-secondary-button>
                    @endcanany
                @endif
                <x-danger-button class="ms-3" wire:click="$toggle('showingAddReadingModal')" wire:loading.attr="disabled">
                    {{ __('Close') }}
                </x-danger-button>
            @endslot
        </x-dialog-modal>

        <x-confirmation-modal wire:model.live="showingConfirmSaveModal" maxWidth="sm">
            @slot('title')
                <span>
                    {{ __('Confirm Add Reading') }}
                </span>
            @endslot
            @slot('content')
                <div>
                    {{ __('Are you sure to want to save reading?') }}
                </div>
            @endslot
            @slot('footer')
                <x-secondary-button type="button"
                    wire:click="saveReading('{{ $readingIDs }}', '{{ $selectedMemberID }}')"
                    wire:loading.attr="disabled">
                    {{ __('Yes') }}
                </x-secondary-button>
                <x-danger-button class="ms-3" wire:click="$toggle('showingConfirmSaveModal')" wire:loading.attr="disabled">
                    {{ __('No') }}
                </x-danger-button>
            @endslot
        </x-confirmation-modal>

        <x-dialog-modal wire:model.live="showingPenaltyDiscountModal" maxWidth="sm">
            @slot('title')
                <span>
                    {{ __('Penalties and Discounts') }}
                </span>
            @endslot

            @slot('content')
                <form wire:submit.prevent="saveBilling('{{ $selectedMemberID }}')" method="post">
                    @csrf
                    <div class="w-full">
                        <div class="text-center uppercase font-bold">
                            {{ __('Discounts') }}
                        </div>

                        <div>
                            <x-label class="text-xs block">{{ __('Discount Type:') }}</x-label>
                        </div>

                        <div class="grid grid-cols-2">
                            <div class="text-slate-800">
                                <x-input type="radio" id="percent" wire:model="discountType" value="percent" />
                                <x-label class="inline normal-case text-sm" for="percent">{{ __('Percent') }}</x-label>
                            </div>
                            <div class="text-slate-800">
                                <x-input type="radio" id="amount" wire:model="discountType" value="amount" />
                                <x-label class="inline normal-case text-sm" for="amount">{{ __('Amount') }}</x-label>
                            </div>
                        </div>
                        <div class="w-full mt-1">
                            <x-label class="text-xs block">
                                @if ($discountType == 'percent')
                                    {{ __('Percentage:') }}
                                @else
                                    {{ __('Amount:') }}
                                @endif
                            </x-label>
                            <x-input type="number" class="w-full rounded-md" wire:model="discount" />
                            <x-input-error for="discount" />
                        </div>
                    </div>

                    <div class="w-full mt-2">
                        <div class="text-center uppercase font-bold">
                            {{ __('Penalties') }}
                        </div>
                        <x-label class="text-xs block text-slate-800" for="penalty">{{ __('Penalty:') }}</x-label>
                        <x-input type="number" class="w-full block rounded-md" wire:model="penalty" />
                        <x-input-error class="normal-case" for="penalty" />
                    </div>
                </form>
            @endslot

            @slot('footer')
                <x-button id="saveBill" wire:click="saveBilling('{{ $selectedMemberID }}')" {{-- href="{{ route('billing-receipts', ['billingIDs' => json_encode($toPrintBilling)]) }}"
                    onclick="return openPopup('printBill');" --}}
                    wire:loading.attr="disabled">
                    {{ __('Save') }}
                </x-button>
                <x-danger-button class="ms-3" wire:click="$toggle('showingPenaltyDiscountModal')"
                    wire:loading.attr="disabled">
                    {{ __('Close') }}
                </x-danger-button>
            @endslot
        </x-dialog-modal>

        <x-confirmation-modal wire:model.live="showingConfirmPrintModal" maxWidth="sm">
            @slot('title')
                <span>
                    {{ __('Confirm Bill Printing') }}
                </span>
            @endslot

            @slot('content')
                <div>
                    {{ __('Do you want to print bill?') }}
                </div>
                <div class="w-full grid grid-cols-2 mt-2">
                    <div>
                        <x-input type="checkbox" class="inline" wire:model.live="isBillPrint" />
                        <x-label class="inline" for="isBillPrint" value="{{ __('Print bill') }}" />
                    </div>
                    <div>
                        <x-input type="checkbox" class="inline" wire:model.live="isAutoPrint" />
                        <x-label class="inline" for="isAutoPrint" value="{{ __('Auto print') }}" />
                    </div>
                    <div class="w-full col-span-2">
                        <span>
                            {{ __('Resets when this page is reloaded or refreshed') }}
                        </span>
                    </div>
                </div>
            @endslot

            @slot('footer')
                <x-button-link id="billReceipt" wire:click="$toggle('showingConfirmPrintModal')"
                    href="{{ route('powas.print-billing', ['toPrint' => json_encode($toPrintBilling)]) }}"
                    onclick="return openPopup('billReceipt');" wire:loading.attr="disabled">
                    {{ __('Yes') }}
                </x-button-link>
                <x-danger-button class="ms-3" wire:click="$toggle('showingConfirmPrintModal')"
                    wire:loading.attr="disabled">
                    {{ __('No') }}
                </x-danger-button>
            @endslot
        </x-confirmation-modal>
    @endisset
</div>
