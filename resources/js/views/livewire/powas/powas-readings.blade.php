<div class="py-4 px-4 space-y-4" x-data="{ expanded: '' }" id="readingTable">
    <x-alert-message class="me-3" on="alert" />

    {{-- Filter --}}
    <div class="w-full grid grid-cols-3">
        <div class="col-span-2">
            <span
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Reading Records') }}</span>
            <div class="inline ml-4">
                <span class="font-bold cursor-pointer uppercase dark:text-white"
                    @click="expanded = ('filter' === expanded) ? '' : 'filter'">
                    {{ __('Filter') }}
                    &nbsp;
                    <span x-show="expanded !== 'filter'"><i class="fa-solid fa-chevron-right"></i></span>
                    <span x-show="expanded === 'filter'"><i class="fa-solid fa-chevron-down"></i></span>
                </span>
            </div>
        </div>
        @can('add reading')
            <div class="inline w-full">
                <div class="flex justify-end">
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="56">
                            <x-slot name="trigger" class="text-right">
                                <button
                                    class="py-1 px-2 text-xs rounded-xl bg-blue-300 md:text-blue-800 hover:bg-blue-400 shadow font-bold">
                                    {{ __('ACTIONS') }}
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <div class="not-italic font-normal">
                                    <x-dropdown-link href="#" wire:click="createReadingTemplate"
                                        class="text-xs py-1 my-0">{{ __('CREATE EXCEL TEMPLATE') }}</x-dropdown-link>

                                    <x-dropdown-link href="#" wire:click="showExcelImportModal"
                                        class="text-xs py-1 my-0">{{ __('IMPORT EXCEL TEMPLATE') }}</x-dropdown-link>

                                    <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                    <x-dropdown-link href="{{ route('powas.add.reading', ['powasID' => $powasID]) }}"
                                        class="text-xs py-1 my-0">{{ __('ENTER READING MANUALLY') }}</x-dropdown-link>
                                    @if (count($readingDates) > 0)
                                        <x-dropdown-link href="#readingTable" wire:click="printReadingSheet"
                                            class="text-xs py-1 my-0">{{ __('PRINT READING SHEET') }}</x-dropdown-link>
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>

                @if (count($readingDates) > 0)
                    <x-dialog-modal wire:model.live="showingReadingDateSelector" maxWidth="sm">
                        @slot('title')
                            {{ __('Select Reading Date') }}
                        @endslot
                        @slot('content')
                            <div>
                                <x-combobox class="w-full block" id="reading_date" name="reading_date"
                                    wire:model.live="readingDate">
                                    <x-slot name="options">
                                        @php
                                            $reading_day = $powasSettings->reading_day;

                                            if ($reading_day < 10) {
                                                $reading_day = '0' . $powasSettings->reading_day;
                                            }
                                        @endphp
                                        {{-- <option
                                            value="{{ Carbon\Carbon::parse($readingDates[0]->reading_date)->format('Y-m-' . $reading_day) }}">
                                            {{ Carbon\Carbon::parse($readingDates[0]->reading_date)->format('F ' . $powasSettings->reading_day . ', Y') }}
                                        </option> --}}
                                        <option
                                            value="{{ Carbon\Carbon::parse($readingDates[0]->reading_date)->addMonth()->format('Y-m-' . $reading_day) }}">
                                            {{ Carbon\Carbon::parse($readingDates[0]->reading_date)->addMonth()->format('F ' . $powasSettings->reading_day . ', Y') }}
                                        </option>
                                        @foreach ($readingDates as $item)
                                            <option value="{{ $item->reading_date }}">
                                                {{ Carbon\Carbon::parse($item->reading_date)->format('F d, Y') }}
                                            </option>
                                        @endforeach
                                    </x-slot>
                                </x-combobox>
                            </div>
                        @endslot
                        @slot('footer')
                            <x-button-link id="readingSheet" wire:click="$toggle('showingReadingDateSelector')"
                                href="{{ route('powas.reading-sheet', ['powasID' => $powasID, 'readingDate' => $readingDate]) }}"
                                wire:loading.attr="disabled" onclick="return openPopup('readingSheet');">
                                {{ __('Print') }}
                            </x-button-link>

                            <x-danger-button class="ms-3" wire:click="$toggle('showingReadingDateSelector')"
                                wire:loading.attr="disabled">
                                {{ __('Close') }}
                            </x-danger-button>
                        @endslot
                    </x-dialog-modal>
                @endif

                <x-confirmation-modal wire:model.live="showingCountErrorModal" maxWidth="sm">
                    @slot('title')
                        <span>
                            {{ __('Import/Export Error!') }}
                        </span>
                    @endslot
                    @slot('content')
                        @php
                            $verb = ['is', 'record'];

                            if ($savedCount > 1) {
                                $verb = ['are', 'records'];
                            }
                        @endphp
                        <span>
                            {{ __('Unable to proceed with the import or export because there ') . $verb[0] . __(' already saved reading ') . $verb[1] . ' for the month of ' . $billingMonth . '. Please click ACTIONS > ADD READING MANUALLY to add reading records.' }}
                        </span>
                    @endslot
                    @slot('footer')
                        <x-danger-button wire:click="$toggle('showingCountErrorModal')" wire:loading.attr="disabled">
                            <i class="fa-regular fa-circle-xmark"></i>
                            <span>&nbsp;{{ __('Close') }}</span>
                        </x-danger-button>
                    @endslot
                </x-confirmation-modal>

                {{-- Import Excel File --}}
                <x-dialog-modal wire:model.live="showingExcelImportModal" maxWidth="lg">
                    <x-slot name="title">
                        {{ __('Select Excel File to Import') }}
                    </x-slot>
                    <x-slot name="content">
                        <div>
                            <x-input id="excelFile" class="block mt-1 w-full" type="file" wire:model="excelFile"
                                accept=".xlsx" />
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
                                <span>&nbsp;{{ __('View Data') }}</span>
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
                <x-import-viewer wire:model.live="showingImportDataModal" maxWidth="2xl">
                    <x-slot name="title">
                        {{ __('Import Viewer') }}
                    </x-slot>
                    <x-slot name="content">
                        {{-- <div class="overflow-x-auto overflow-y-auto w-full"> --}}
                        <table class="table-auto min-w-max md:w-full">
                            <thead>
                                <tr>
                                    <th class="px-3">{{ __('SL#') }}</th>
                                    <th class="px-3">{{ __('Full Name') }}</th>
                                    <th class="px-3">{{ __('Previous Reading') }}</th>
                                    <th class="px-3">{{ __('Present Reading') }}</th>
                                    <th class="px-3">{{ __('Reading Count') }}</th>
                                    <th class="px-3">{{ __('Reading Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $slotNumber = 0;
                                @endphp
                                @forelse ($importCollection as $item)
                                    <tr
                                        class="hover:bg-gray-300 dark:hover:bg-gray-100 hover:text-gray-900 py-2 cursor-pointer">
                                        <td class="px-2 text-center">{{ $slotNumber + 1 }}</td>
                                        <td class="px-2">{{ $item['member_name'] }}</td>
                                        <td class="px-2 text-right">{{ number_format($item['prev_reading'], 2) }}</td>
                                        <td class="px-2 text-right">{{ number_format($item['reading'], 2) }}</td>
                                        <td class="px-2 text-center">{{ $item['reading_count'] }}</td>
                                        <td class="px-2">
                                            {{ \Carbon\Carbon::parse(PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['reading_date']))->format('F d, Y') }}
                                        </td>
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
            </div>
        @endcan
    </div>

    <div x-show="expanded === 'filter'" class="grid grid-cols-1 md:grid-cols-3 gap-2 overflow-hidden" x-collapse>
        <div class="w-full block mt-2 md:mt-0 gap-2">
            <x-label class="inline" for="search" value="{{ __('Search:') }}" />
            <x-input class="w-full block" id="search" name="search" wire:model.live="search" autocomplete="off"
                placeholder="Search..." />
        </div>

        <div class="inline">
            <x-label class="inline" for="pagination" value="{{ __('# of rows per page: ') }}" />
            <x-combobox class="w-full block" id="pagination" name="pagination" wire:model.live="pagination">
                <x-slot name="options">
                    @for ($i = 10; $i <= 1000; $i = $i + 10)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </x-slot>
            </x-combobox>
        </div>

        <div class="w-full block mt-2 md:mt-0 gap-2">
            <x-label class="inline" value="{{ __('Reading Date Range:') }}" />
            <div class="w-full grid grid-cols-7">
                <x-input class="w-full col-span-3" type="date" wire:model.lazy="startDate"
                    placeholder="Start Date" />
                <div class="flex justify-center text-center items-center">
                    <x-label class="font-bold" value="{{ __('to') }}" />
                </div>
                <x-input class="w-full col-span-3" type="date" wire:model.lazy="endDate"
                    placeholder="End Date" />
            </div>
            @error('endDate')
                <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </div>

        <div class="md:col-span-3 flex justify-end">
            <button x-show="expanded === 'filter'" type="button" wire:click="clearFilter"
                class="uppercase text-xs py-1 px-2 rounded-xl font-bold shadow bg-gray-400 text-gray">{{ __('Clear Filter') }}</button>
        </div>
    </div>

    <div class="w-full">
        @if (count($powasReadings) == 0 || $powasReadings == null)
            <div class="my-2 text-center">
                <x-label class="text-xl font-black my-16" value="{{ __('No records found!') }}" />
            </div>
        @else
            <div class="shadow-lg p-2 border rounded-lg border-slate-600 dark:border-slate-400">
                <div class="overflow-x-auto overflow-y-auto max-h-[600px]">
                    <x-table.table class="text-xs md:text-sm table-auto min-w-max md:w-full">
                        <x-slot name="thead">
                            <x-table.thead-tr>
                                <x-table.thead-th class="px-2">{{ __('SL#') }}</x-table.thead-th>
                                <x-table.thead-th class="px-2">{{ __('READING ID') }}</x-table.thead-th>
                                <x-table.thead-th class="px-2">{{ __('MEMBER NAME') }}</x-table.thead-th>
                                <x-table.thead-th class="px-2">{{ __('PREVIOUS READING') }}</x-table.thead-th>
                                <x-table.thead-th class="px-2">{{ __('PRESENT READING') }}</x-table.thead-th>
                                <x-table.thead-th class="px-2">{{ __('READING COUNT') }}</x-table.thead-th>
                                <x-table.thead-th class="px-2">{{ __('READING DATE') }}</x-table.thead-th>
                                <x-table.thead-th class="px-2">{{ __('RECORDED BY') }}</x-table.thead-th>
                            </x-table.thead-tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @php
                                $readingCounter = 0;
                            @endphp
                            <x-table.tbody>
                                @foreach ($powasReadings as $item)
                                    @php
                                        $readingCounter++;
                                    @endphp
                                    <x-table.tbody-tr wire:key="{{ $item->reading_id }}">
                                        <x-table.tbody-td class="text-center px-2">
                                            {{ $readingCounter }}
                                        </x-table.tbody-td>

                                        <x-table.tbody-td class="text-center px-2">
                                            {{ $item->reading_id }}
                                        </x-table.tbody-td>

                                        <x-table.tbody-td class="px-2">
                                            {{ $item->lastname . ', ' . $item->firstname }}
                                        </x-table.tbody-td>

                                        <x-table.tbody-td class="px-2 text-right">
                                            {{ $previousReadingList[$item->member_id] }}
                                        </x-table.tbody-td>

                                        <x-table.tbody-td class="px-2 text-right">
                                            {{ $item->reading }}
                                        </x-table.tbody-td>

                                        @if ($item->reading_count == 0)
                                            <x-table.tbody-td class="px-2 text-center">
                                                {{ __('Initial Reading') }}
                                            </x-table.tbody-td>
                                        @else
                                            <x-table.tbody-td class="px-2 text-center">
                                                {{ $item->reading_count }}
                                            </x-table.tbody-td>
                                        @endif

                                        <x-table.tbody-td class="px-2 text-center">
                                            {{ $item->reading_date }}
                                        </x-table.tbody-td>

                                        <x-table.tbody-td class="px-2">
                                            {{ $usersList[$item->recorded_by] }}
                                        </x-table.tbody-td>
                                    </x-table.tbody-tr>
                                @endforeach
                            </x-table.tbody>
                        </x-slot>
                    </x-table.table>
                </div>
            </div>
            <div class="mt-2">
                {{ $powasReadings->links() }}
            </div>
        @endif
    </div>
</div>
