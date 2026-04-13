<x-form-section submit="updatePOWASSettings" method="post">
    <x-slot name="title">
        {{ __('POWAS Preferences') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Provide the water rate, first 50 payment, application and membership fee, minimum payment, etc.') }}
    </x-slot>

    <x-slot name="form" autocomplete="off">
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="water_rate" value="{{ __('Water Rate (in peso)') }}" />
            <x-input id="water_rate" type="number" class="mt-1 block w-full" wire:model="powasSettings.water_rate"
                autocomplete="off" />
            <x-input-error for="powasSettings.water_rate" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="first_50_fee" value="{{ __('First 50 Fee (in peso)') }}" />
            <x-input id="first_50_fee" type="number" class="mt-1 block w-full" wire:model="powasSettings.first_50_fee"
                autocomplete="off" />
            <x-input-error for="powasSettings.first_50_fee" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="application_fee" value="{{ __('Application Fee (in peso)') }}" />
            <x-input id="application_fee" type="number" class="mt-1 block w-full"
                wire:model="powasSettings.application_fee" autocomplete="off" />
            <x-input-error for="powasSettings.application_fee" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="membership_fee" value="{{ __('Membership Fee (in peso)') }}" />
            <x-input id="membership_fee" type="number" class="mt-1 block w-full"
                wire:model="powasSettings.membership_fee" autocomplete="off" />
            <x-input-error for="powasSettings.membership_fee" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="minimum_payment" value="{{ __('Minimum Payment (in peso)') }}" />
            <x-input id="minimum_payment" type="number" class="mt-1 block w-full"
                wire:model="powasSettings.minimum_payment" autocomplete="off" />
            <x-input-error for="powasSettings.minimum_payment" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="due_date_day" value="{!! __('Due Date Day (e.g. every 3rd day of the month)') !!}" />
            <x-input id="due_date_day" type="number" class="mt-1 block w-full" wire:model="powasSettings.due_date_day"
                autocomplete="off" />
            <x-input-error for="powasSettings.due_date_day" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="days_before_disconnection"
                value="{{ __('Days Before Disconnection (Number of days after the due date for disconnection)') }}" />
            <x-input id="days_before_disconnection" type="number" class="mt-1 block w-full"
                wire:model="powasSettings.days_before_disconnection" autocomplete="off" />
            <x-input-error for="powasSettings.days_before_disconnection" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="reading_day" value="{!! __('Reading Day (e.g. every 28th day of the month)') !!}" />
            <x-input id="reading_day" type="number" class="mt-1 block w-full" wire:model="powasSettings.reading_day"
                autocomplete="off" />
            <x-input-error for="powasSettings.reading_day" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="collection_day" value="{!! __('Collection Day (e.g. every 3rd day of the month)') !!}" />
            <x-input id="collection_day" type="number" class="mt-1 block w-full"
                wire:model="powasSettings.collection_day" autocomplete="off" />
            <x-input-error for="powasSettings.collection_day" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="penalty_per_day" value="{!! __('Penalty per Day (in peso)') !!}" />
            <x-input id="penalty_per_day" type="number" class="mt-1 block w-full"
                wire:model="powasSettings.penalty_per_day" autocomplete="off" />
            <x-input-error for="powasSettings.penalty_per_day" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="reconnection_fee" value="{!! __('Reconnection Fee (in peso)') !!}" />
            <x-input id="reconnection_fee" type="number" class="mt-1 block w-full"
                wire:model="powasSettings.reconnection_fee" autocomplete="off" />
            <x-input-error for="powasSettings.reconnection_fee" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="members_micro_savings" value="{!! __('Member\'s Micro-Savings (in peso)') !!}" />
            <x-input id="members_micro_savings" type="number" class="mt-1 block w-full"
                wire:model="powasSettings.members_micro_savings" autocomplete="off" />
            <x-input-error for="powasSettings.members_micro_savings" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="land_owners_id" value="{!! __('Land Owner\'s Account Number') !!}" />
            <x-input id="land_owners_id" type="text" class="mt-1 block w-full"
                wire:model="powasSettings.land_owners_id" autocomplete="off" list="membersList" />
            <datalist id="membersList">
                @foreach ($membersList as $memberID => $memberName)
                    <option value="{{ $memberID }}">{{ $memberName }}</option>
                @endforeach
            </datalist>
            <x-input-error for="powasSettings.land_owners_id" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="bill_paper_size" value="{!! __('Bill Paper Size') !!}" />
            <x-combobox id="bill_paper_size" type="text" class="mt-1 block w-full"
                wire:model="powasSettings.bill_paper_size" autocomplete="off">
                @slot('options')
                    <option value="105mm">{{ __('A6 (1/4 of A4)') }}</option>
                    <option value="80mm">{{ __('80mm Thermal Paper') }}</option>
                @endslot
            </x-combobox>
            <x-input-error for="powasSettings.bill_paper_size" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="receipt_paper_size" value="{!! __('Receipt Paper Size') !!}" />
            <x-combobox id="receipt_paper_size" type="text" class="mt-1 block w-full"
                wire:model="powasSettings.receipt_paper_size" autocomplete="off">
                @slot('options')
                    <option value="80mm">{{ __('80mm Thermal Paper') }}</option>
                    <option value="58mm">{{ __('58mm Thermal Paper') }}r</option>
                @endslot
            </x-combobox>
            <x-input-error for="powasSettings.receipt_paper_size" class="mt-2" />
        </div>

        {{-- ATP Configuration Section --}}
        <div class="col-span-6 sm:col-span-4 mt-8">
            <p class="text-base font-semibold text-gray-700 border-b pb-2">Authority to Print (ATP) Configuration</p>
            <p class="text-xs text-gray-500 mt-1 mb-3">Required for BIR CAS compliance. Enter the ATP details as issued by the BIR for your accredited printer.</p>
        </div>
        <div class="col-span-6 sm:col-span-4 mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-label for="atp_number" value="{{ __('ATP Number') }}" />
                <x-input id="atp_number" type="text" class="mt-1 block w-full" wire:model="powasSettings.atp_number" autocomplete="off" placeholder="e.g. ATP-RDO-12345"/>
                <x-input-error for="powasSettings.atp_number" class="mt-2" />
            </div>
            <div>
                <x-label for="atp_date_issued" value="{{ __('ATP Date Issued') }}" />
                <x-input id="atp_date_issued" type="date" class="mt-1 block w-full" wire:model="powasSettings.atp_date_issued" autocomplete="off" />
                <x-input-error for="powasSettings.atp_date_issued" class="mt-2" />
            </div>
            <div>
                <x-label for="atp_valid_until" value="{{ __('ATP Valid Until') }}" />
                <x-input id="atp_valid_until" type="date" class="mt-1 block w-full" wire:model="powasSettings.atp_valid_until" autocomplete="off" />
                <x-input-error for="powasSettings.atp_valid_until" class="mt-2" />
            </div>
            <div>
                <x-label for="printer_name" value="{{ __('Printer / Accredited Printing Company Name') }}" />
                <x-input id="printer_name" type="text" class="mt-1 block w-full" wire:model="powasSettings.printer_name" autocomplete="off" />
                <x-input-error for="powasSettings.printer_name" class="mt-2" />
            </div>
            <div class="md:col-span-2">
                <x-label for="printer_address" value="{{ __('Printer Address') }}" />
                <x-input id="printer_address" type="text" class="mt-1 block w-full" wire:model="powasSettings.printer_address" autocomplete="off" />
                <x-input-error for="powasSettings.printer_address" class="mt-2" />
            </div>
            <div>
                <x-label for="printer_tin" value="{{ __('Printer TIN') }}" />
                <x-input id="printer_tin" type="text" class="mt-1 block w-full" wire:model="powasSettings.printer_tin" autocomplete="off" placeholder="e.g. 123-456-789-000"/>
                <x-input-error for="powasSettings.printer_tin" class="mt-2" />
            </div>
            <div>
                <x-label for="printer_accreditation_no" value="{{ __('Printer BIR Accreditation No.') }}" />
                <x-input id="printer_accreditation_no" type="text" class="mt-1 block w-full" wire:model="powasSettings.printer_accreditation_no" autocomplete="off" />
                <x-input-error for="powasSettings.printer_accreditation_no" class="mt-2" />
            </div>
            <div>
                <x-label for="printer_accreditation_date" value="{{ __('Printer Accreditation Date') }}" />
                <x-input id="printer_accreditation_date" type="date" class="mt-1 block w-full" wire:model="powasSettings.printer_accreditation_date" autocomplete="off" />
                <x-input-error for="powasSettings.printer_accreditation_date" class="mt-2" />
            </div>
        </div>
        <div class="col-span-6 sm:col-span-4 mt-4">
            <p class="text-sm font-semibold text-gray-700 mt-2 mb-1">Serial Number Range</p>
            <p class="text-xs text-gray-500 mb-3">As printed on your ATP-approved booklet. The system will use this range to generate sequential OR numbers automatically.</p>
        </div>
        <div class="col-span-6 sm:col-span-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-label for="serial_number_start" value="{{ __('Serial Number (Start)') }}" />
                <x-input id="serial_number_start" type="text" class="mt-1 block w-full" wire:model="powasSettings.serial_number_start" autocomplete="off" placeholder="e.g. 0000001"/>
                <x-input-error for="powasSettings.serial_number_start" class="mt-2" />
            </div>
            <div>
                <x-label for="serial_number_end" value="{{ __('Serial Number (End)') }}" />
                <x-input id="serial_number_end" type="text" class="mt-1 block w-full" wire:model="powasSettings.serial_number_end" autocomplete="off" placeholder="e.g. 0500000"/>
                <x-input-error for="powasSettings.serial_number_end" class="mt-2" />
            </div>
            <div>
                <x-label for="current_serial_number" value="{{ __('Current Serial Number (Last Used)') }}" />
                <x-input id="current_serial_number" type="text" class="mt-1 block w-full" wire:model="powasSettings.current_serial_number" autocomplete="off" placeholder="Leave blank if starting fresh" />
                <x-input-error for="powasSettings.current_serial_number" class="mt-2" />
            </div>
        </div>
    </x-slot>
    <x-slot name="actions">
        @can('edit powas preferences')
            <x-alert-message class="me-3" on="saved" />
            <x-button wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-button>
        @endcan
    </x-slot>
</x-form-section>
