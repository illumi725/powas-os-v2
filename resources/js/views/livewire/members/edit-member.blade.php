<x-form-section submit="confirmSave" autocomplete="off">
    @slot('title')
        {{ __('Personal Information') }}
    @endslot
    @slot('description')
        {{ __('Provide the personal information of the POWAS member.') }}
    @endslot

    <x-slot name="form" autocomplete="off">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.lastname" value="{{ __('Last Name') }}" />
            <x-input id="memberInfo.lastname" type="text" class="mt-1 block w-full"
                wire:model.live="memberInfo.lastname" autocomplete="off" />
            <x-input-error for="memberInfo.lastname" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.firstname" value="{{ __('First Name') }}" />
            <x-input id="memberInfo.firstname" type="text" class="mt-1 block w-full"
                wire:model.live="memberInfo.firstname" autocomplete="off" />
            <x-input-error for="memberInfo.firstname" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.middlename" value="{{ __('Middle Name') }}" />
            <x-input id="memberInfo.middlename" type="text" class="mt-1 block w-full"
                wire:model.live="memberInfo.middlename" autocomplete="off" />
            <x-input-error for="memberInfo.middlename" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.birthday" value="{{ __('Birthday') }}" />
            <x-input id="memberInfo.birthday" type="date" class="mt-1 block w-full"
                wire:model.live="memberInfo.birthday" autocomplete="off" />
            <x-input-error for="memberInfo.birthday" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.birthplace" value="{{ __('Birthplace') }}" />
            <x-input id="memberInfo.birthplace" type="text" class="mt-1 block w-full"
                wire:model.live="memberInfo.birthplace" autocomplete="off" list="birthplaces" />
            <datalist id="birthplaces">
                @foreach ($birthplaces as $item)
                    <option value="{{ $item }}"></option>
                @endforeach
            </datalist>
            <x-input-error for="memberInfo.birthplace" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.gender" value="{{ __('Sex') }}" />
            <x-combobox id="memberInfo.gender" class="mt-1 block w-full" wire:model.live="memberInfo.gender"
                autocomplete="off">
                @slot('options')
                    <option value="" disabled>{{ __('-Select Sex-') }}</option>
                    <option value="MALE">{{ __('MALE') }}</option>
                    <option value="FEMALE">{{ __('FEMALE') }}</option>
                @endslot
            </x-combobox>
            <x-input-error for="memberInfo.gender" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.contact_number" value="{{ __('Birthday') }}" />
            <x-input id="memberInfo.contact_number" type="number" class="mt-1 block w-full"
                wire:model.live="memberInfo.contact_number" autocomplete="off" />
            <x-input-error for="memberInfo.contact_number" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.civil_status" value="{{ __('Civil Status') }}" />
            <x-combobox id="memberInfo.civil_status" class="mt-1 block w-full" wire:model.live="memberInfo.civil_status"
                autocomplete="off">
                @slot('options')
                    <option value="" disabled>{{ __('-Select Civil Status-') }}</option>
                    <option value="SINGLE">{{ __('SINGLE') }}</option>
                    <option value="MARRIED">{{ __('MARRIED') }}</option>
                    @if ($memberInfo['gender'] == '')
                        <option value="WIDOW">{{ __('WIDOW') }}</option>
                        <option value="WIDOWER">{{ __('WIDOWER') }}</option>
                    @else
                        @if ($memberInfo['gender'] == 'FEMALE')
                            <option value="WIDOW">{{ __('WIDOW') }}</option>
                        @endif
                        @if ($memberInfo['gender'] == 'MALE')
                            <option value="WIDOWER">{{ __('WIDOWER') }}</option>
                        @endif
                    @endif
                    <option value="LEGALLY SEPARATED">{{ __('LEGALLY SEPARATED') }}</option>
                @endslot
            </x-combobox>
            <x-input-error for="memberInfo.civil_status" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.family_members" value="{{ __('Number of Family Members') }}" />
            <x-input id="memberInfo.family_members" type="number" min="1" class="mt-1 block w-full"
                wire:model.live="memberInfo.family_members" autocomplete="off" />
            <x-input-error for="memberInfo.family_members" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.membership_date" value="{{ __('Membership Date') }}" />
            <x-input id="memberInfo.membership_date" type="date" class="mt-1 block w-full"
                wire:model.live="memberInfo.membership_date" autocomplete="off" />
            <x-input-error for="memberInfo.membership_date" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.address1" value="{{ __('Zone/Sector/Village') }}" />
            <x-input id="memberInfo.address1" type="text" class="mt-1 block w-full"
                wire:model.live="memberInfo.address1" autocomplete="off" />
            <x-input-error for="memberInfo.address1" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.region" value="{{ __('Region') }}" />
            <x-combobox id="memberInfo.region" class="mt-1 block w-full" wire:model.live="memberInfo.region"
                autocomplete="off">
                @slot('options')
                    <option value="" disabled>{{ __('-Select Region-') }}</option>
                    @foreach ($regions as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                @endslot
            </x-combobox>
            <x-input-error for="memberInfo.region" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.province" value="{{ __('Province') }}" />
            <x-combobox id="memberInfo.province" class="mt-1 block w-full" wire:model.live="memberInfo.province"
                autocomplete="off">
                @slot('options')
                    <option value="" disabled>{{ __('-Select Province-') }}</option>
                    @foreach ($provinces as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                @endslot
            </x-combobox>
            <x-input-error for="memberInfo.province" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.municipality" value="{{ __('Municipality') }}" />
            <x-combobox id="memberInfo.municipality" class="mt-1 block w-full"
                wire:model.live="memberInfo.municipality" autocomplete="off">
                @slot('options')
                    <option value="" disabled>{{ __('-Select Municipality-') }}</option>
                    @foreach ($municipalities as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                @endslot
            </x-combobox>
            <x-input-error for="memberInfo.municipality" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.barangay" value="{{ __('Barangay') }}" />
            <x-combobox id="memberInfo.barangay" class="mt-1 block w-full" wire:model.live="memberInfo.barangay"
                wire:change="loadPresentAddress" autocomplete="off">
                @slot('options')
                    <option value="" disabled>{{ __('-Select Barangay-') }}</option>
                    @foreach ($barangays as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                @endslot
            </x-combobox>
            <x-input-error for="memberInfo.barangay" class="mt-1" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="memberInfo.present_address" value="{{ __('Present Address') }}" />
            <x-checkbox wire:model.live="sameas" name="sameas" id="sameas" />
            <label for="sameas"
                class="font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Same as above') }}</label>
            <x-input type="text" class="mt-1 block w-full" wire:model.live="memberInfo.present_address"
                disabled="{{ $sameas }}" />
            <x-input-error for="memberInfo.present_address" class="mt-1" />
        </div>
    </x-slot>
    @slot('actions')
        @can('edit member')
            <x-alert-message class="me-3" on="saved" />
            <x-button wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-button>

            <x-confirmation-modal wire:model.live="showingConfirmSave" maxWidth="sm">
                @slot('title')
                    <span>
                        {{ __('Save Changes?') }}
                    </span>
                @endslot
                @slot('content')
                    <div>
                        {{ __('Are you sure you want to save changes?') }}
                    </div>
                @endslot
                @slot('footer')
                    <x-secondary-button type="button" wire:click="saveInfo" wire:loading.attr="disabled">
                        {{ __('Yes') }}
                    </x-secondary-button>
                    <x-danger-button class="ms-3" wire:click="$toggle('showingConfirmSave')" wire:loading.attr="disabled">
                        {{ __('No') }}
                    </x-danger-button>
                @endslot
            </x-confirmation-modal>
        @endcan
    @endslot
</x-form-section>
