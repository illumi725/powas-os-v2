<div class="py-4 px-8 space-y-4">
    <x-alert-message class="me-3" on="alert" />
    <div class="w-full grid grid-cols-1 md:grid-cols-2">
        <div class="flex items-center">
            <h3 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Billing Management') }}
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-0">
            <div class="mt-2 md:mt-0 md:col-span-2">
                <x-input class="md:inline w-full" id="search" name="search" wire:model.live.debounce.250ms="search"
                    autocomplete="off" placeholder="Search..." />
            </div>
            <div class="flex justify-end items-center">
                <x-label class="text-xs inline mr-2"
                    value="{{ __('Saved: ' . $savedCount . '/' . count($validReadingsList)) }}" />

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger" class="text-right">
                        <button
                            class="py-1 px-2 text-xs rounded-xl bg-blue-300 md:text-blue-800 hover:bg-blue-400 shadow font-bold">
                            {{ __('ACTIONS') }}
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="not-italic font-normal">
                            @if ($savedCount == count($validReadingsList))
                                @php
                                    if (count($selectedToPrint) > 0) {
                                        $billingsToPrint = [];
                                        foreach ($selectedToPrint as $key => $value) {
                                            $billingsToPrint[] = $key;
                                        }
                                    } else {
                                        $billingsToPrint = [];
                                        $billingsToPrint = $toPrintBilling;
                                    }
                                @endphp

                                <x-dropdown-link id="billingPrint"
                                    href="{{ route('powas.print-billing', ['toPrint' => json_encode($billingsToPrint)]) }}"
                                    class="text-xs py-1 my-0 uppercase" wire:loading.attr="disabled"
                                    onclick="return openPopup('billingPrint');">
                                    @if (count($selectedToPrint) > 0)
                                        <span>
                                            {{ __('Print Selected Billing') }}
                                        </span>
                                    @else
                                        <span>
                                            {{ __('Print All Billing') }}
                                        </span>
                                    @endif
                                </x-dropdown-link>

                                <x-dropdown-link id="receiptPrint"
                                    href="{{ route('billing-receipts', ['billingIDs' => json_encode($billingsToPrint), 'advancePrint' => 'YES']) }}"
                                    class="text-xs py-1 my-0 uppercase" wire:loading.attr="disabled"
                                    onclick="return openPopup('receiptPrint');">
                                    @if (count($selectedToPrint) > 0)
                                        <span>
                                            {{ __('Advance Print Selected Receipts') }}
                                        </span>
                                    @else
                                        <span>
                                            {{ __('Advance Print All Receipts') }}
                                        </span>
                                    @endif
                                </x-dropdown-link>

                                <x-dropdown-link id="collectionSheet"
                                    href="{{ route('powas.collection-sheet', ['powasID' => $powasID]) }}"
                                    class="text-xs py-1 my-0 uppercase" wire:loading.attr="disabled"
                                    onclick="return openPopup('collectionSheet');">
                                    <span>
                                        {{ __('Print Collection Sheet') }}
                                    </span>
                                </x-dropdown-link>
                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                            @endif

                            <x-dropdown-link wire:click.prevent="openGlobalDiscountModal" href="#" class="text-xs py-1 my-0 uppercase"
                                wire:loading.attr="disabled">
                                <span>&nbsp;{{ __('Set Global Discount') }}</span>
                            </x-dropdown-link>

                            <x-dropdown-link wire:click="saveAll" href="#" class="text-xs py-1 my-0 uppercase"
                                wire:loading.attr="disabled">
                                <span>&nbsp;{{ __('Save All') }}</span>
                            </x-dropdown-link>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

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
    </div>

    <div class="mt-4">
        @php
            $ctr = 0;
        @endphp
        @forelse ($validReadingsList as $item => $value)
            @php
                $ctr++;
            @endphp
            <x-bill-card wire:key="{{ $value['billing_id'] }}">
                @slot('checkbox')
                    <span>
                        <input type="checkbox" id="{{ $value['billing_id'] }}"
                            wire:model.live="selectedToPrint.{{ $value['billing_id'] }}"
                            value="{{ $value['billing_id'] }}" />
                        <label for="{{ $value['billing_id'] }}">
                            {{ __('Billing ID: ') }}
                            <span class="font-bold">{{ $value['billing_id'] }}</span>
                        </label>
                    </span>
                @endslot
                @slot('counter')
                    {{ $ctr }}
                @endslot
                @slot('memberName')
                    {{ $value['member_name'] }}
                @endslot
                @slot('billNumber')
                    {{ $value['bill_number'] }}
                @endslot
                @slot('billingMonth')
                    {{ $value['billing_month'] }}
                @endslot
                @slot('billingPeriod')
                    {{ $value['cut_off_start'] . ' to ' . $value['cut_off_end'] }}
                @endslot
                @slot('dueDate')
                    {{ \Carbon\Carbon::parse($value['due_date'])->format('F j, Y') }}
                @endslot
                @slot('isExists')
                    {{ $value['is_exists'] }}
                @endslot
                {{-- @slot('printCount')
                    {{ $value['print_count'] }}
                @endslot --}}
                @slot('presentReading')
                    {{ $value['present_reading'] }}
                @endslot
                @slot('previousReading')
                    {{ $value['previous_reading'] }}
                @endslot
                @slot('consumption')
                    {{ number_format($value['cubic_meter_used'], 1) }}&nbsp;m<sup>3</sup>
                @endslot
                @slot('isMinimum')
                    @if ($value['is_minimum'] == true)
                        {{ __('Amount [minimum]:') }}
                    @else
                        {{ __('Amount:') }}
                    @endif
                @endslot
                @slot('billAmount')
                    {{ number_format($value['billing_amount'], 2) }}
                @endslot

                @php
                    if ($value['isTransacted'] == 'YES') {
                        $transacted = true;
                    } else {
                        $transacted = '';
                    }
                @endphp

                @slot('discountControl')
                    <div class="w-full">
                        <div>
                            <label class="text-xs block">{{ __('Discount Type:') }}</label>
                        </div>

                        <div class="grid grid-cols-2">
                            <div class="text-slate-800">
                                <input type="radio" id="percent_{{ $item }}"
                                    wire:model="discountType.{{ $item }}" value="percent" />
                                <label class="inline normal-case text-sm"
                                    for="percent_{{ $item }}">{{ __('Percent') }}</label>
                            </div>
                            <div class="text-slate-800">
                                <input type="radio" id="amount_{{ $item }}"
                                    wire:model="discountType.{{ $item }}" value="amount" />
                                <label class="inline normal-case text-sm"
                                    for="amount_{{ $item }}">{{ __('Amount') }}</label>
                            </div>
                        </div>
                        <div class="w-full mt-1">
                            <label class="text-xs block">
                                @if ($discountType[$item] == 'percent')
                                    {{ __('Percentage:') }}
                                @else
                                    {{ __('Amount:') }}
                                @endif
                            </label>
                            <input type="number" class="w-full rounded-md" wire:model="discount.{{ $item }}" />
                            <x-input-error for="discount.{{ $item }}" />
                        </div>
                    </div>
                @endslot
                @slot('penaltyControl')
                    <div class="w-full">
                        <label class="text-xs block text-slate-800"
                            for="penalty.{{ $item }}">{{ __('Penalty:') }}</label>
                        <input type="number" class="w-full block rounded-md" wire:model="penalty.{{ $item }}" />
                        <x-input-error class="normal-case" for="penalty.{{ $item }}" />
                    </div>
                    <div class="w-full mt-2 text-end">
                        <x-action-message class="normal-case inline mr-2" on="saved_{{ $item }}">
                            <i class="fa-solid fa-floppy-disk font-bold text-xl text-green-800"></i>
                        </x-action-message>

                        @if ($value['isTransacted'] == 'NO')
                            <x-button wire:click="saveBilling('{{ $item }}')" class="inline"
                                wire:loading.attr="disabled">
                                <i class="fa-regular fa-floppy-disk"></i>
                                <span>&nbsp;{{ __('Save') }}</span>
                            </x-button>
                        @endif

                        @if ($value['is_exists'] == 'YES')
                            @php
                                $printThis = [$value['billing_id']];
                            @endphp
                            <x-button-link id="receiptLink_{{ str_replace('-', '_', $item) }}"
                                href="{{ route('powas.print-billing', ['toPrint' => json_encode($printThis)]) }}"
                                class="inline" wire:loading.attr="disabled"
                                onclick="return openPopup_{{ str_replace('-', '_', $item) }}();"
                                wire:loading.attr="disabled">
                                <i class="fa-solid fa-print"></i>
                                <span>&nbsp;{{ __('Print') }}</span>
                            </x-button-link>
                        @endif
                    </div>
                @endslot
            </x-bill-card>

            <script>
                let {{ str_replace('-', '_', $item) }};

                function openPopup_{{ str_replace('-', '_', $item) }}() {
                    var url = document.getElementById('receiptLink_{{ str_replace('-', '_', $item) }}').getAttribute('href');
                    var windowWidth = 960;
                    var windowHeight = 640;

                    var screenWidth = window.screen.width;
                    var screenHeight = window.screen.height;

                    var leftPosition = (screenWidth - windowWidth) / 2;
                    var topPosition = (screenHeight - windowHeight) / 2;

                    var windowFeatures = 'width=' + windowWidth + ',height=' + windowHeight + ',left=' + leftPosition +
                        ',top=' +
                        topPosition + ',resizable=no';

                    {{ str_replace('-', '_', $item) }} = window.open(url, 'myPopup', windowFeatures);
                    // {{ str_replace('-', '_', $item) }}.print();
                    // window.open(url, 'myPopup', windowFeatures);

                    if (getMobileOperatingSystem() != 'Android') {
                        {{ str_replace('-', '_', $item) }}.addEventListener('afterprint', function() {
                            {{ str_replace('-', '_', $item) }}.close();
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
            </script>
        @empty
            <div class="my-2 text-center">
                <x-label class="text-xl font-black my-16" value="{{ __('No records found!') }}" />
            </div>
        @endforelse
    </div>
    <!-- Global Discount Modal -->
    <x-dialog-modal wire:model="showingGlobalDiscountModal">
        <x-slot name="title">
            {{ __('Set Global Discount') }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-sm font-bold text-blue-700">Apply Discount to All</div>
                    <div class="text-xs text-blue-600 mt-1">This will overwrite the discount for all pending bills in the list below.</div>
                </div>

                <div>
                    <x-label value="{{ __('Discount Type') }}" />
                    <div class="flex space-x-4 mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model.live="globalDiscountType" value="amount" class="form-radio text-blue-600">
                            <span class="ml-2">Fixed Amount (&#8369;)</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model.live="globalDiscountType" value="percent" class="form-radio text-blue-600">
                            <span class="ml-2">Percentage (%)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <x-label value="{{ __('Value') }}" />
                    <x-input type="number" step="0.01" class="block w-full mt-1" wire:model.live="globalDiscountValue" />
                    <x-input-error for="globalDiscountValue" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('showingGlobalDiscountModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ml-2" wire:click="applyGlobalDiscount" wire:loading.attr="disabled">
                {{ __('Apply to All') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
