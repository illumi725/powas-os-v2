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
            @livewire('logs.applications-logs')
        @endcan
    </div>

    {{-- Search and Pagination Control --}}
    <div x-show="expanded === 'filter'" class="grid grid-cols-1 md:grid-cols-2 gap-4 overflow-hidden" x-collapse>

        <div class="w-full md:col-span-2 grid grid-cols-1 md:grid-cols-4 gap-4">
            @if (Auth::user()->hasRole('admin'))
                <div class="w-full">
                    <x-label for="region" value="{{ __('Region: ') }}" />
                    <x-combobox class="mt-1 block w-full" id="region" name="region" wire:model.live="region"
                        wire:change="loadprovince">
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
                    <x-combobox class="mt-1 block w-full" id="province" name="province" wire:model.live="province"
                        wire:change="loadmunicipality">
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
                    <x-combobox class="mt-1 block w-full" id="powas" name="powas" wire:model.live="powas">
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

    {{-- Applications Cards List --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse ($applications as $application)
            <div class="text-slate-800 border border-slate-400 dark:border-none dark:bg-slate-300 shadow-md hover:scale-105 dark:hover:scale-105 rounded-lg"
                wire:key="{{ $application->application_id }}">
                <div class="py-4 rounded-lg border-b-8 border-slate-400 dark:border-slate-600">
                    <div class="mx-4 text-sm italic font-black w-full">
                        {{ $application->application_id }}
                    </div>
                    <div class="mx-4 font-bold w-full">
                        {{ $application->lastname . ', ' . $application->firstname . ' ' . $application->middlename }}
                    </div>
                    <div class="mx-4 text-xs italic">
                        {{ $application->address1 . ', ' . $application->barangay . ', ' . $application->municipality . ', ' . $application->province }}
                    </div>

                    <div class="mx-4 text-xs font-bold">
                        {{ $application->barangay . ' POWAS ' . $application->phase }}
                    </div>

                    <div class="mx-4 grid grid-cols-2">
                        <div>
                            @php
                                $statusStyle = '';
                                $icon = '';
                                if ($application->application_status == 'VERIFIED') {
                                    $statusStyle = 'bg-green-400 text-green';
                                    $icon = '<i class="fa-regular fa-circle-check"></i>';
                                } elseif ($application->application_status == 'PENDING') {
                                    $statusStyle = 'bg-red-400 text-red';
                                    $icon = '<i class="fa-solid fa-hourglass-half"></i>';
                                }
                            @endphp
                            <span
                                class="py-1 px-2 rounded-xl text-xs font-bold shadow {!! $statusStyle !!}">{!! $icon . '&nbsp;' . $application->application_status !!}</span>
                        </div>
                        <div class="text-right w-full gap-2">
                            @if (Auth::user()->hasRole('admin|president|treasurer'))
                                <button type="button"
                                    class="py-1 px-2 text-xs rounded-xl bg-blue-300 md:text-blue-800 hover:bg-blue-400 shadow"
                                    title="VIEW" wire:click="showApplicationDetailsModal({{ $application }})"><i
                                        class="fa-regular fa-eye"></i></button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-3 text-center text-slate-800">
                <x-label class="text-xl font-black" value="{{ __('No records found!') }}" />
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>
        {{ $applications->links() }}
    </div>

    @if (isset($selectedapplication))
        <x-dialog-modal wire:model.live="showingApplicationDetailsModal">
            <x-slot name="title">
                <div class="text-left">
                    {{ __('Application Details') }}
                </div>
            </x-slot>

            <x-slot name="content">
                <div>
                    <div class="w-full my-4 font-bold text-base">
                        {{ __('Personal Information') }}
                    </div>
                    <div class="grid grid-cols-3 text-left mb-4">
                        <div class="col-span-1 font-bold">
                            <span>{{ __('Full Name: ') }}</span>
                        </div>
                        <div class="col-span-2">
                            <span>{{ $selectedapplication->lastname . ', ' . $selectedapplication->firstname . ' ' . $selectedapplication->middlename }}</span>
                        </div>
                        <div class="col-span-1 font-bold mt-2">
                            <span>{{ __('Age: ') }}</span>
                        </div>
                        <div class="col-span-2 mt-2">
                            <span>{{ $age . __(' years old') }}</span>
                        </div>
                        <div class="col-span-1 font-bold mt-2">
                            <span>{{ __('Birthday: ') }}</span>
                        </div>
                        <div class="col-span-2 mt-2">
                            <span>{{ \Carbon\Carbon::parse($selectedapplication->birthday)->format('F j, Y') }}</span>
                        </div>
                        <div class="col-span-1 font-bold mt-2">
                            <span>{{ __('Birthplace: ') }}</span>
                        </div>
                        <div class="col-span-2 mt-2">
                            <span>{{ $selectedapplication->birthplace }}</span>
                        </div>
                        <div class="col-span-1 font-bold mt-2">
                            <span>{{ __('Gender: ') }}</span>
                        </div>
                        <div class="col-span-2 mt-2">
                            <span>{{ $selectedapplication->gender }}</span>
                        </div>
                        <div class="col-span-1 font-bold mt-2">
                            <span>{{ __('Contact No.: ') }}</span>
                        </div>
                        <div class="col-span-2 mt-2">
                            <span>{{ $selectedapplication->contact_number }}</span>
                        </div>
                        <div class="col-span-1 font-bold mt-2">
                            <span>{{ __('Civil Status: ') }}</span>
                        </div>
                        <div class="col-span-2 mt-2">
                            <span>{{ $selectedapplication->civil_status }}</span>
                        </div>
                        <div class="col-span-1 font-bold mt-2">
                            <span>{{ __('Address: ') }}</span>
                        </div>
                        <div class="col-span-2 mt-2">
                            <span>{{ $selectedapplication->address1 . ', ' . $selectedapplication->barangay . ', ' . $selectedapplication->municipality . ', ' . $selectedapplication->province }}</span>
                        </div>
                        <div class="col-span-1 font-bold mt-2">
                            <span>{{ __('# of Family Members: ') }}</span>
                        </div>
                        <div class="col-span-2 mt-2">
                            <span>{{ $selectedapplication->family_members }}</span>
                        </div>
                        <div class="col-span-1 font-bold mt-2">
                            <span>{{ __('Application Date: ') }}</span>
                        </div>
                        <div class="col-span-2 mt-2">
                            <span>{{ $selectedapplication->application_date }}</span>
                            @if ($days == 1)
                                <span
                                    class="italic font-bold text-green-600 dark:text-green-500">{{ __('[') . $days . __(' day ago') . __(']') }}</span>
                            @else
                                @if ($days >= 7)
                                    <span
                                        class="italic font-bold text-red-600 dark:text-red-500">{{ __('[') . $days . __(' days ago') . __(']') }}</span>
                                @else
                                    <span
                                        class="italic font-bold text-green-600 dark:text-green-500">{{ __('[') . $days . __(' days ago') . __(']') }}</span>
                                @endif
                            @endif
                        </div>
                        <div class="col-span-1 font-bold mt-2">
                            <span>{{ __('POWAS: ') }}</span>
                        </div>
                        <div class="col-span-2 mt-2">
                            <span>{{ $powasinfo->barangay . ' POWAS ' . $powasinfo->phase . ' - ' . $selectedapplication->powas_id }}</span>
                        </div>
                        <div class="col-span-1 font-bold mt-2">
                            <span>{{ __('Application Status: ') }}</span>
                        </div>
                        <div class="col-span-2 mt-2">
                            <span>{{ $selectedapplication->application_status }}</span>
                        </div>
                        @if ($selectedapplication->application_status == 'VERIFIED')
                            <div class="col-span-1 font-bold mt-2">
                                <span>{{ __('Verified by: ') }}</span>
                            </div>
                            <div class="col-span-2 mt-2">
                                <span>{{ $verifiedby->userinfo->lastname . ', ' . $verifiedby->userinfo->firstname . ' ' . $verifiedby->userinfo->middlename }}</span>
                                <span class="uppercase">
                                    {{ __('-') }}@foreach ($verifiedby->getRoleNames() as $role)
                                        {{ $role }}
                                    @endforeach
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                <hr style="border: 0.5px solid gray;">
                <div class="mb-4">
                    <div class="w-full my-4 font-bold text-base">
                        {{ __('Identification Card') }}
                    </div>
                    <div class="w-full grid place-items-center">
                        {{-- For Development --}}
                        <img class="rounded-xl border-4 border-dashed border-gray-600 dark:border-gray-300"
                            width="512px" src="{{ asset('/uploads/ids/' . $selectedapplication->id_path) }}"
                            alt="">

                        {{-- For Production --}}
                        {{-- <img class="rounded-xl border-4 border-dashed border-gray-600 dark:border-gray-300"
                            width="512px"
                            src="{{ asset('/powas-os/public/uploads/ids/' . $selectedapplication->id_path) }}"
                            alt=""> --}}
                    </div>
                </div>
                <hr style="border: 0.5px solid gray;">
                <div class="mb-4">
                    <div class="w-full my-4 font-bold text-base">
                        {{ __('Take Action') }}
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-5 md:gap-4">
                        <div class="col-span-2 w-full text-left my-auto">
                            <x-label for="payment" value="{{ __('Action: ') }}" />
                            <x-combobox name="actiontaken" id="actiontaken" class="block w-full"
                                wire:model.live="actiontaken">
                                <x-slot name="options">
                                    <option value="" disabled>{{ __('-Select Action-') }}</option>
                                    @if ($selectedapplication->application_status == 'PENDING')
                                        @can('verify application')
                                            <option value="VERIFY">{{ __('VERIFY') }}</option>
                                        @endcan
                                    @endif

                                    @can('approve application')
                                        <option value="APPROVE">{{ __('APPROVE') }}</option>
                                    @endcan

                                    <option value="REJECT">{{ __('REJECT') }}</option>
                                </x-slot>
                            </x-combobox>

                            <div class="col-span-2 text-left">
                                <x-input-error for="actiontaken" class="mt-1" />
                            </div>
                        </div>

                        <div class="col-span-3 text-left mt-4 md:mt-0">
                            @if ($actiontaken == 'REJECT')
                                <x-combobox name="rejectreason" id="rejectreason" class="block w-full"
                                    wire:model.live="rejectreason">
                                    <x-slot name="options">
                                        <option value="" disabled>{{ __('-Select Reject Reason-') }}</option>
                                        <option value="Invalid ID">{{ __('Invalid ID') }}</option>
                                        <option value="Information do not match with ID">
                                            {{ __('Information do not match with ID') }}</option>
                                        <option value="ID cannot be verified">{{ __('ID cannot be verified') }}
                                        </option>
                                        <option value="Application overdue">{{ __('Application overdue') }}</option>
                                    </x-slot>
                                </x-combobox>
                                <x-input-error for="rejectreason" class="mt-1" />
                            @endif

                            @if ($actiontaken == 'VERIFY')
                                <div>
                                    <span
                                        class="text-green-800 font-bold dark:text-green-500 my-auto">{{ __('You are about to verify this application.') }}</span>
                                </div>
                            @endif

                            @if ($actiontaken == 'APPROVE')
                                <div class="w-full grid grid-cols-4 gap-2">
                                    @if ($first50Count < 50)
                                        <div class="w-full my-auto col-span-1">
                                            <x-checkbox name="first50" id="first50" wire:model.live="first50"
                                                wire:click="setPayment" />
                                            <label class="font-medium text-sm text-gray-700 dark:text-gray-300"
                                                for="first50"
                                                value="{{ __('First 50') }}">{{ __('First 50') }}</label>
                                        </div>
                                    @endif
                                    <div
                                        class="w-full my-auto {{ $first50Count < 50 ? 'col-span-3' : 'col-span-4' }} ">
                                        @if ($first50 == 1)
                                            @if ($isExistsEquityCapitalAccount <= 0)
                                                <span
                                                    class="text-orange-800 font-bold dark:text-orange-500 my-auto">{{ __('Capital Account of EQUITY Account Type is not yet present in the Chart of Accounts!') }}</span>
                                            @else
                                                <x-label for="payment" value="{!! __('Members\' Capital: ') !!}" />
                                                <x-input wire:model="payment" id="payment" class="inline w-full"
                                                    type="number" name="payment" :placeholder="$first50 == 1
                                                        ? 'Members\' Capital'
                                                        : 'Application + Membership Fee'"
                                                    autocomplete="off" />
                                            @endif
                                        @else
                                            @if ($isExistsApplicationFeeAccount <= 0 || $isExistsMembershipFeeAccount <= 0)
                                                @if ($isExistsApplicationFeeAccount <= 0)
                                                    <span
                                                        class="text-orange-800 font-bold dark:text-orange-500 my-auto">{{ __('Application Fee Account of REVENUE Account Type is not yet present in the Chart of Accounts!') }}</span>
                                                @elseif ($isExistsMembershipFeeAccount <= 0)
                                                    <span
                                                        class="text-orange-800 font-bold dark:text-orange-500 my-auto">{{ __('Membership Fee Account of REVENUE Account Type is not yet present in the Chart of Accounts!') }}</span>
                                                @endif
                                            @else
                                                <x-label for="payment"
                                                    value="{{ __('Application + Membership Fee: ') }}" />
                                                <x-input wire:model="payment" id="payment" class="inline w-full"
                                                    type="number" name="payment" :placeholder="$first50 == 1
                                                        ? 'Members\' Capital'
                                                        : 'Application + Membership Fee'"
                                                    autocomplete="off" disabled="true" />
                                            @endif
                                        @endif
                                    </div>
                                    <x-input-error for="payment" class="mt-1 col-span-4" />
                                </div>
                            @endif

                            @if ($actiontaken == '')
                                <div>
                                    <span
                                        class="text-orange-800 font-bold dark:text-orange-500 my-auto">{{ __('Please take an action to proceed.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="confirmActionTaken" wire:loading.attr="disabled">
                    {{ __('Apply Action') }}
                </x-secondary-button>
                <x-danger-button class="ms-3" wire:click="$toggle('showingApplicationDetailsModal')"
                    wire:loading.attr="disabled">
                    {{ __('Close') }}
                </x-danger-button>
            </x-slot>
        </x-dialog-modal>

        {{-- Take Action Confirmation Modal --}}
        <x-confirmation-modal wire:model.live="confirmingActionTaken" maxWidth="md">
            <x-slot name="title">
                {{ __('Take Action') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Are you sure you would like to ') . $actiontaken . __(' this application?') }}
            </x-slot>

            <x-slot name="footer">
                <x-button wire:click="takeaction('{{ $selectedapplication->application_id }}')"
                    wire:loading.attr="disabled">
                    <i class="fa-solid fa-check"></i>&nbsp;
                    {{ __('Yes') }}
                </x-button>

                <x-danger-button class="ms-3" wire:click="$toggle('confirmingActionTaken')"
                    wire:loading.attr="disabled">
                    <i class="fa-solid fa-circle-xmark"></i>&nbsp;
                    {{ __('No') }}
                </x-danger-button>
            </x-slot>
        </x-confirmation-modal>

        {{-- Print Receipt --}}
        <x-confirmation-modal wire:model.live="printing" maxWidth="sm">
            <x-slot name="title">
                {{ __('Print Receipt') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Do you want to print receipt?') }}
            </x-slot>

            <x-slot name="footer">
                @if (count($trxnID))
                    <x-button-link id="receiptLink" wire:click="$toggle('printing')"
                        href="{{ route('other-receipt.view', ['trxnID' => json_encode($trxnID), 'printID' => json_encode($printIDs), 'receiptNumber' => $receiptNumber, 'powasID' => $powasinfo->powas_id]) }}"
                        wire:loading.attr="disabled" onclick="return openPopup('receiptLink');">
                        <i class="fa-solid fa-check"></i>&nbsp;
                        {{ __('Yes') }}
                    </x-button-link>
                @endif

                <x-danger-button class="ms-3" wire:click="$toggle('printing')" wire:loading.attr="disabled">
                    <i class="fa-solid fa-circle-xmark"></i>&nbsp;
                    {{ __('No') }}
                </x-danger-button>
            </x-slot>
        </x-confirmation-modal>
    @endif

    {{-- <script>
        let printWindow;

        function openPopup(element) {
            var url = document.getElementById(element).getAttribute('href');
            var windowWidth = 960;
            var windowHeight = 640;

            var screenWidth = window.screen.width;
            var screenHeight = window.screen.height;

            var leftPosition = (screenWidth - windowWidth) / 2;
            var topPosition = (screenHeight - windowHeight) / 2;

            var windowFeatures = 'width=' + windowWidth + ',height=' + windowHeight + ',left=' + leftPosition +
                ',top=' +
                topPosition + ',resizable=no';

            printWindow = window.open(url, 'myPopup', windowFeatures);

            // if (element == 'collectionSheet') {
            //     printWindow.print();
            // }

            if (getMobileOperatingSystem() != 'Android') {
                printWindow.addEventListener("afterprint", function() {
                    printWindow.close();
                });
            }

            return false;
        }

        function getMobileOperatingSystem() {
            const userAgent = navigator.userAgent || navigator.vendor || window.opera;

            if (/windows phone/i.test(userAgent)) {
                return "Windows Phone";
            }
            if (/android/i.test(userAgent)) {
                return "Android";
            }

            if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                return "iOS";
            }

            if (/Windows/.test(navigator.userAgent) && !/Windows Phone|Windows Mobile/.test(navigator.userAgent)) {
                return "Windows Desktop";
            }

            if (/Macintosh|MacIntel|MacPPC|Mac68K/.test(navigator.userAgent)) {
                return "MacOS";
            }

            if (/Linux/.test(navigator.userAgent) && !isAndroid) {
                return "Linux";
            }

            return "unknown";
        }
    </script> --}}
</div>
