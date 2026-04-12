<form wire:submit="saveApplication" autocomplete="off" method="post">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 lg:gap-8">
        <div class="mt-2 md:mt-0">
            <x-label for="lastname" value="{{ __('Last Name') }}" />
            <x-input wire:model="lastname" id="lastname" class="block mt-1 w-full" type="text" name="lastname"
                autofocus autocomplete="off" />
            <x-input-error for="lastname" class="mt-1" />
        </div>

        <div class="mt-2 md:mt-0">
            <x-label for="firstname" value="{{ __('First Name') }}" />
            <x-input wire:model="firstname" id="firstname" class="block mt-1 w-full" type="text" name="firstname"
                autocomplete="off" />
            <x-input-error for="firstname" class="mt-1" />
        </div>

        <div class="mt-2 md:mt-0">
            <x-label for="middlename" value="{{ __('Middle Name') }}" />
            <x-input wire:model="middlename" id="middlename" class="block mt-1 w-full" type="text" name="middlename"
                autocomplete="off" />
            <x-input-error for="middlename" class="mt-1" />
        </div>

        <div class="mt-2">
            <x-label for="birthday" value="{{ __('Birthday') }}" />
            <x-input wire:model="birthday" id="birthday" class="block mt-1 w-full" type="date" name="birthday"
                autocomplete="off" />
            <x-input-error for="birthday" class="mt-1" />
        </div>

        <div class="mt-2">
            <x-label for="birthplace" value="{{ __('Birthplace (Municipality, Province)') }}" />
            <x-input wire:model="birthplace" id="birthplace" class="block mt-1 w-full" type="text" name="birthplace"
                autocomplete="off" list="birthplaces" />
            <datalist id="birthplaces">
                @foreach ($birthplacelist as $item)
                    <option value="{{ $item }}"></option>
                @endforeach
            </datalist>
            <x-input-error for="birthplace" class="mt-1" />
        </div>

        <div class="mt-2">
            <x-label for="gender" value="{{ __('Gender') }}" />
            <x-combobox name="gender" id="gender" class="mt-1 block w-full" wire:model.live="gender">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Gender-') }}</option>
                    <option value="MALE">{{ __('MALE') }}</option>
                    <option value="FEMALE">{{ __('FEMALE') }}</option>
                </x-slot>
            </x-combobox>
            <x-input-error for="gender" class="mt-1" />
        </div>

        <div class="mt-2">
            <x-label for="contactnumber" value="{{ __('Contact Number') }}" />
            <x-input wire:model="contactnumber" id="contactnumber" class="block mt-1 w-full" type="number"
                name="contactnumber" autocomplete="off" />
            <x-input-error for="contactnumber" class="mt-1" />
        </div>

        <div class="mt-2">
            <x-label for="civilstatus" value="{{ __('Civil Status') }}" />
            <x-combobox name="civilstatus" id="civilstatus" class="mt-1 block w-full" wire:model="civilstatus">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Civil Status-') }}</option>
                    <option value="SINGLE">{{ __('SINGLE') }}</option>
                    <option value="MARRIED">{{ __('MARRIED') }}</option>
                    @if ($gender == '')
                        <option value="WIDOW">{{ __('WIDOW') }}</option>
                        <option value="WIDOWER">{{ __('WIDOWER') }}</option>
                    @else
                        @if ($gender == 'FEMALE')
                            <option value="WIDOW">{{ __('WIDOW') }}</option>
                        @endif
                        @if ($gender == 'MALE')
                            <option value="WIDOWER">{{ __('WIDOWER') }}</option>
                        @endif
                    @endif
                    <option value="LEGALLY SEPARATED">{{ __('LEGALLY SEPARATED') }}</option>
                </x-slot>
            </x-combobox>
            <x-input-error for="civilstatus" class="mt-1" />
        </div>

        <div class="mt-2">
            <x-label for="numberoffamilymembers" value="{{ __('Number of Family Members') }}" />
            <x-input wire:model="numberoffamilymembers" id="numberoffamilymembers" class="block mt-1 w-full"
                type="number" name="numberoffamilymembers" min="1" autocomplete="off" />
            <x-input-error for="numberoffamilymembers" class="mt-1" />
        </div>

        <div class="mt-2">
            <x-label for="address1" value="{{ __('Zone/Village/Sector') }}" />
            <x-input id="address1" class="block mt-1 w-full" type="text" name="address1"
                wire:model.live="address1" autocomplete="off" />
            <x-input-error for="address1" class="mt-1" />
        </div>

        <!-- Region  -->
        <div class="mt-2">
            <x-label for="regionInput" value="{{ __('Region') }}" />
            <x-combobox name="regionInput" id="regionInput" class="mt-1 block w-full" wire:model.live="regionInput"
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
        <div class="mt-2">
            <x-label for="provinceInput" value="{{ __('Province') }}" />
            <x-combobox name="provinceInput" id="provinceInput" class="mt-1 block w-full"
                wire:model.live="provinceInput" wire:change="loadmunicipality(true)">
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
        <div class="mt-2">
            <x-label for="municipalityInput" value="{{ __('Municipality') }}" />
            <x-combobox name="municipalityInput" id="municipalityInput" class="mt-1 block w-full"
                wire:model.live="municipalityInput" wire:change="loadbarangay(true)">
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
        <div class="mt-2">
            <x-label for="barangayInput" value="{{ __('Barangay') }}" />
            <x-combobox name="barangayInput" id="barangayInput" class="mt-1 block w-full"
                wire:model.live="barangayInput" wire:change="loadPhaseName">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Barangay-') }}</option>
                    @foreach ($barangay as $item => $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </x-slot>
            </x-combobox>
            <x-input-error for="barangayInput" class="mt-2" />
        </div>

        <div class="mt-2">
            <x-label for="phaseInput" value="{{ __('Phase') }}" />
            <x-combobox name="phaseInput" id="phaseInput" class="mt-1 block w-full" wire:model.live="phaseInput">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Phase-') }}</option>
                    @foreach ($phase as $item => $value)
                        <option value="{{ $value->powas_id }}">
                            {{ $value->phase . __(' (') . $value->zone . __(')') }}</option>
                    @endforeach
                </x-slot>
            </x-combobox>
            <x-input-error for="phaseInput" class="mt-1" />
        </div>

        <div class="col-span-1 md:col-span-3 mt-2">
            <x-label for="presentaddress" value="{{ __('Present Address') }}" />
            <x-checkbox name="sameas" id="sameas" wire:model.live="sameas" />
            <label class="font-medium text-sm text-gray-700 dark:text-gray-300" for="sameas"
                value="{{ __('Same as above') }}">{{ __('Same as above') }}</label>
            <input id="presentaddress"
                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm uppercase"
                type="text" name="presentaddress" autocomplete="off"
                @if ($sameas) disabled @endif wire:model="presentaddress" />
            <x-input-error for="presentaddress" class="mt-1" />
        </div>

        <div class="col-span-1 md:col-span-3 mt-2">
            <x-label for="id_path" value="{{ __('Identification Card') }}" />
            <x-input id="id_path" class="block mt-1 w-full" type="file" wire:model="id_path"
                accept="image/*" />
            @if ($id_path)
                <div class="mt-3 w-full text-center">
                    <img class="w-96" src="{{ $id_path->temporaryUrl() }}" alt="Preview">
                </div>
            @endif
            <x-input-error for="id_path" class="mt-1" />
        </div>
    </div>

    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
        <div class="mt-4">
            <x-label for="terms">
                <div class="flex items-center">
                    <x-checkbox name="terms" id="terms" wire:model.live="terms" />

                    <div class="ms-2">
                        {!! __('I agree to the :terms_of_service and :privacy_policy', [
                            'terms_of_service' =>
                                '<a target="_blank" href="' .
                                route('terms.show') .
                                '" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">' .
                                __('Terms of Service') .
                                '</a>',
                            'privacy_policy' =>
                                '<a target="_blank" href="' .
                                route('policy.show') .
                                '" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">' .
                                __('Privacy Policy') .
                                '</a>',
                        ]) !!}
                    </div>
                </div>
            </x-label>
            <x-input-error for="terms" class="mt-1" />
        </div>
    @endif

    <div class="flex items-center justify-end mt-4">
        <x-alert-message class="me-3" on="app_exists" />
        <x-button class="ms-4" wire:loading.attr="disabled">
            <i class="fa-solid fa-file-arrow-up"></i>&nbsp;{{ __('Apply') }}
        </x-button>
    </div>

    <x-dialog-modal wire:model.live="showingMessageModal" maxWidth="md">
        <x-slot name="title">{{ $messageType }}</x-slot>
        <x-slot name="content">
            <p class="text-justify">{{ $message }}</p>

            @isset($refNum)
                <div class="w-full grid grid-cols-2 text-center mt-4 mb-2">
                    <a class="uppercase font-bold text-xs px-2 py-2 rounded-md bg-green-400 text-green-950 hover:bg-green-500 mr-2"
                        target="_blank" href="{{ route('application-form.view', ['applicationid' => $refNum]) }}">
                        <i class="fa-solid fa-file-pdf"></i>
                        &nbsp;
                        {{ __('View Application') }}
                    </a>
                    <a class="uppercase font-bold text-xs px-2 py-2 rounded-md bg-green-400 text-green-950 hover:bg-green-500"
                        href="{{ route('application-form.download', ['applicationid' => $refNum]) }}">
                        <i class="fa-solid fa-download"></i>
                        &nbsp;
                        {{ __('Download Application') }}
                    </a>
                </div>
            @endisset
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="modalActions(true)" wire:loading.attr="disabled">
                <i class="fa-regular fa-circle-check"></i>
                <span>&nbsp;{{ __('OK') }}</span>
            </x-button>
        </x-slot>
    </x-dialog-modal>
</form>
