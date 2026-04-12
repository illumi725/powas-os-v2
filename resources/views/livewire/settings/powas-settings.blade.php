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
