<div class="inline md:hidden">
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
                    {{ __('Adding member is not possible due to the following issues:') }}
                </p>
                <ul style="list-style-type:disc" class="ml-4">
                    @foreach ($errorList as $error)
                    <li class="my-3">{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-3 text-center">
                <x-label class="inline hover:font-bold cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700 rounded-xl px-1 py-1" wire:click="showPOWASSelectorModal" wire:loading.attr="disabled">
                    &nbsp;<span>{{ __('Create Excel Template') }}</span>
                </x-label>

                <x-label class="inline hover:font-bold cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700 rounded-xl px-1 py-1" wire:click="showExcelImportModal" wire:loading.attr="disabled">
                    &nbsp;<span>{{ __('Import Excel Template') }}</span>
                </x-label>

                @if (Auth::user()->hasRole('admin'))
                <x-label class="inline hover:font-bold cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700 rounded-xl px-1 py-1" wire:click="showPOWASSelectorModalForManual" wire:loading.attr="disabled">
                    &nbsp;<span>{{ __('Add POWAS Application Manually') }}</span>
                </x-label>
                @else
                <x-label class="inline hover:font-bold cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700 rounded-xl px-1 py-1" wire:click="showPOWASSelectorModalForManual" wire:loading.attr="disabled">
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
                    <x-input wire:model.live="selectedPOWAS" id="selectedPOWAS" class="block mt-1 w-full" type="text" name="selectedPOWAS" autocomplete="off" list="powasList" disabled="{{ Auth::user()->hasRole('treasurer|secretary') }}" />
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
                    <x-input wire:model.live="numberOfMembers" id="numberOfMembers" class="block mt-1 w-full" type="number" name="numberOfMembers" autocomplete="off" />
                    <x-input-error for="numberOfMembers" class="mt-1" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-button wire:click="createCSVTemplate('{{ $selectedPOWAS }}')" class="mr-2" wire:loading.attr="disabled">
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
                    <x-input wire:model.live="selectedPOWAS" id="selectedPOWAS" class="block mt-1 w-full" type="text" name="selectedPOWAS" autocomplete="off" list="powasList" />
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
            <x-button wire:click="addMemberManually('{{ $selectedPOWAS }}')" class="mr-2" wire:loading.attr="disabled">
                <i class="fa-solid fa-file-circle-plus"></i>
                <span>&nbsp;{{ __('Add Member') }}</span>
            </x-button>
            <x-danger-button wire:click="$toggle('showingPOWASSelectorModalForManual')" wire:loading.attr="disabled">
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
                <x-input id="excelFile" class="block mt-1 w-full" type="file" wire:model="excelFile" accept=".xlsx" />
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
        <x-danger-button wire:click="$toggle('showingExcelImportModal')" class="w-full" wire:loading.attr="disabled">
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
        {{-- <div class="overflow-x-auto overflow-y-auto w-full"> --}}
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
                    <th class="px-3">{{ __('Account Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                $slotNumber = 0;
                @endphp
                @forelse ($importCollection as $item)
                <tr class="hover:bg-gray-300 dark:hover:bg-gray-100 hover:text-gray-900 py-2 cursor-pointer">
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
                    <td class="px-3 text-center">{{ $item['member_status'] }}</td>
                    @php
                    $slotNumber++;
                    @endphp
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
        {{-- </div> --}}
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
