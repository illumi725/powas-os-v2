<x-form-section submit="update('{{ $powas->powas_id }}')">
    <x-slot name="title">
        {{ __('POWAS Location') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Provide the address, phase name, inauguration date, and status of the POWAS Cooperative.') }}
    </x-slot>

    <x-slot name="form" autocomplete="off">
        <!-- Region  -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="regionInput" value="{{ __('Region') }}" />
            <x-combobox name="regionInput" id="regionInput" disabled class="mt-1 block w-full" wire:model="regionInput"
                wire:change="loadprovince(true)">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Region-') }}</option>
                    @foreach ($region as $item => $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </x-slot>
            </x-combobox>
            <x-input-error for="regionInput" class="mt-2" />
        </div>

        <!-- Province  -->
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="provinceInput" value="{{ __('Province') }}" />
            <x-combobox name="provinceInput" id="provinceInput" disabled class="mt-1 block w-full"
                wire:model="provinceInput" wire:change="loadmunicipality(true)">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Province-') }}</option>
                    @foreach ($province as $item => $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </x-slot>
            </x-combobox>
            <x-input-error for="provinceInput" class="mt-2" />
        </div>

        <!-- Municipality  -->
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="municipalityInput" value="{{ __('Municipality') }}" />
            <x-combobox name="municipalityInput" id="municipalityInput" disabled class="mt-1 block w-full"
                wire:model="municipalityInput" wire:change="loadbarangay(true)">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Municipality-') }}</option>
                    @foreach ($municipality as $item => $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </x-slot>
            </x-combobox>
            <x-input-error for="municipalityInput" class="mt-2" />
        </div>

        <!-- Barangay  -->
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="barangayInput" value="{{ __('Barangay') }}" />
            <x-combobox name="barangayInput" id="barangayInput" disabled class="mt-1 block w-full"
                wire:model="barangayInput">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Barangay-') }}</option>
                    @foreach ($barangay as $item => $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </x-slot>
            </x-combobox>
            <x-input-error for="barangayInput" class="mt-2" />
        </div>

        <!-- Zone -->
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="zoneInput" value="{{ __('Zone/Village/Sector') }}" />
            <x-input id="zoneInput" type="text" class="mt-1 block w-full" wire:model="zoneInput" disabled
                autocomplete="off" />
            <x-input-error for="zoneInput" class="mt-2" />
        </div>

        <!-- Phase -->
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="phaseInput" value="{{ __('Phase Name') }}" />
            <x-input id="phaseInput" type="text" class="mt-1 block w-full" wire:model="phaseInput" disabled
                autocomplete="off" />
            <x-input-error for="phaseInput" class="mt-2" />
        </div>

        <!-- Inauguration Date -->
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="inaugurationInput" value="{{ __('Inauguration Date') }}" />
            <x-input id="inaugurationInput" type="date" class="mt-1 block w-full" wire:model="inaugurationInput"
                autocomplete="off" />
            <x-input-error for="inaugurationInput" class="mt-2" />
        </div>

        <!-- Status  -->
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="statusInput" value="{{ __('Status') }}" />
            <x-combobox name="statusInput" id="statusInput" class="mt-1 block w-full" wire:model="statusInput">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Status-') }}</option>
                    <option value="ACTIVE">{{ __('ACTIVE') }}</option>
                    <option value="INACTIVE">{{ __('INACTIVE') }}</option>
                </x-slot>
            </x-combobox>
            <x-input-error for="statusInput" class="mt-2" />
        </div>
    </x-slot>
    <x-slot name="actions">
        @if (Auth::user()->hasRole('admin'))
            <x-alert-message class="me-3" on="saved" />

            <x-button wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-button>
        @endif
    </x-slot>
</x-form-section>
