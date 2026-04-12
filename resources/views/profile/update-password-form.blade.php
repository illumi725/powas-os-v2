<x-form-section submit="updatePassword">
    <x-slot name="title">
        @if (Auth::user()->getAccountStatus() == 'INACTIVE')
            {{ __('Change Password') }}
        @else
            {{ __('Update Password') }}
        @endif
    </x-slot>

    <x-slot name="description">
        @if (Auth::user()->getAccountStatus() == 'INACTIVE')
            {{ __('Your account is currently inactive. Please change your default password to continue using POWAS-OS.') }}
        @endif

        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </x-slot>

    <x-slot name="form" autocomplete="off">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="current_password" value="{{ __('Current Password') }}" />
            <x-input id="current_password" type="password" class="mt-1 block w-full" wire:model="state.current_password"
                autocomplete="off" />
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password" value="{{ __('New Password') }}" />
            <x-input id="password" type="password" class="mt-1 block w-full" wire:model="state.password"
                autocomplete="off" />
            <x-input-error for="password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
            <x-input id="password_confirmation" type="password" class="mt-1 block w-full"
                wire:model="state.password_confirmation" autocomplete="off" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>

        @if (Auth::user()->getAccountStatus() == 'INACTIVE')
            <div class="col-span-6 sm:col-span-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email"
                    autocomplete="off" />
                <x-input-error for="email" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-label for="contact_number" value="{{ __('Contact Number') }}" />
                <x-input id="contact_number" type="number" class="mt-1 block w-full" wire:model="state.contact_number"
                    autocomplete="off" />
                <x-input-error for="contact_number" class="mt-2" />
            </div>
        @endif
    </x-slot>

    <x-slot name="actions">
        <x-alert-message class="me-3" on="saved" />

        <x-button>
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
