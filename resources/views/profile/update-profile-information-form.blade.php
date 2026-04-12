<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form" autocomplete="off">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="{{ __('Photo') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url ?? asset('assets/user.png') }}" alt="{{ $this->user->username }}" class="rounded-full h-20 w-20 object-cover">
                    {{-- <img src="{{ URL::asset('assets/user.png') }}" alt="{{ $this->user->username }}" class="rounded-full h-20 w-20 object-cover"> --}}
                    <span x-text="{{ $this->user->profile_photo_url ?? asset('assets/user.png') }}"></span>
                </div>

                {{-- <span>{{ $this->user->profile_photo_url }}</span>

                <img src="{{ asset('storage/profile-photos/Baprmlm1z7wnimk4giyVGx5qVVpfOIv4pQrAHaTY.png') }}" alt=""> --}}

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Remove Photo') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Username -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="username" value="{{ __('Username') }}" />
            <x-input id="username" type="text" class="mt-1 block w-full" wire:model="state.username" autocomplete="off"/>
            <x-input-error for="username" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="lastname" value="{{ __('Last Name') }}" />
            <x-input id="lastname" type="text" class="mt-1 block w-full" wire:model="state.lastname" autocomplete="off" />
            <x-input-error for="lastname" class="mt-2" />
        </div>

        <!-- First Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="firstname" value="{{ __('First Name') }}" />
            <x-input id="firstname" type="text" class="mt-1 block w-full" wire:model="state.firstname" autocomplete="off" />
            <x-input-error for="firstname" class="mt-2" />
        </div>

        <!-- Middle Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="middlename" value="{{ __('Middle Name') }}" />
            <x-input id="middlename" type="text" class="mt-1 block w-full" wire:model="state.middlename" autocomplete="off" />
            <x-input-error for="middlename" class="mt-2" />
        </div>

        <!-- Birthday -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="birthday" value="{{ __('Birthday') }}" />
            <x-input id="birthday" type="date" class="mt-1 block w-full" wire:model="state.birthday" autocomplete="off" />
            <x-input-error for="birthday" class="mt-2" />
        </div>

        <!-- Address 1 -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="address1" value="{{ __('Address 1 (House #/Street/Zone/Village)') }}" />
            <x-input id="address1" type="text" class="mt-1 block w-full" wire:model="state.address1" autocomplete="off" />
            <x-input-error for="address1" class="mt-2" />
        </div>

        <!-- Region  -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="region" value="{{ __('Region') }}" />
            <x-combobox name="region" id="region" class="mt-1 block w-full" wire:model="state.region" wire:change="loadprovince(true)">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Region-') }}</option>
                    @foreach ($region as $item => $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </x-slot>
            </x-combobox>
            <x-input-error for="region" class="mt-2" />
        </div>

        <!-- Province  -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="province" value="{{ __('Province') }}" />
            <x-combobox name="province" id="province" class="mt-1 block w-full" wire:model="state.province" wire:change="loadmunicipality(true)">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Province-') }}</option>
                    @foreach ($province as $item => $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </x-slot>
            </x-combobox>
            <x-input-error for="province" class="mt-2" />
        </div>

        <!-- Municipality  -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="municipality" value="{{ __('Municipality') }}" />
            <x-combobox name="municipality" id="municipality" class="mt-1 block w-full" wire:model="state.municipality" wire:change="loadbarangay(true)">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Municipality-') }}</option>
                    @foreach ($municipality as $item => $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </x-slot>
            </x-combobox>
            <x-input-error for="municipality" class="mt-2" />
        </div>

        <!-- Barangay  -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="barangay" value="{{ __('Barangay') }}" />
            <x-combobox name="barangay" id="barangay" class="mt-1 block w-full" wire:model="state.barangay">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Barangay-') }}</option>
                    @foreach ($barangay as $item => $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </x-slot>
            </x-combobox>
            <x-input-error for="barangay" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" autocomplete="off" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 dark:text-white">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-alert-message class="me-3" on="saved" />

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
