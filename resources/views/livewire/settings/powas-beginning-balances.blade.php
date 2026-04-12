<x-form-section submit="confirmSave" method="post">
    @slot('title')
        {{ __('Beginning Balances') }}
    @endslot

    @slot('description')
        {{ __('Set all beginning balances and date.') }}
    @endslot

    <x-slot name="form" autocomplete="off">
        <div class="col-span-6 sm:col-span-4 mt-4">
            <x-label for="balanceDate" value="{{ __('Balance Date') }}" />
            @if ($lockFileExists == true)
                <x-input id="balanceDate" type="date" class="mt-1 block w-full" wire:model.live="balanceDate"
                    autocomplete="off" />
            @else
                <x-input id="balanceDate" type="date" class="mt-1 block w-full" wire:model.live="balanceDate"
                    wire:blur="initJSON" autocomplete="off" />
            @endif
            <x-input-error for="balanceDate" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4 mt-4 text-center">
            <x-label value="{{ __('ASSET') }}" />
        </div>

        @foreach ($chartOfAccounts as $account)
            @if ($account->account_type == 'ASSET')
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <x-label for="{{ $account->account_number }}" value="{!! $account->account_name !!}" />
                    <x-input id="{{ $account->account_number }}" type="number" class="mt-1 block w-full"
                        wire:model="balances.{{ $account->account_number }}" autocomplete="off" />
                    <x-input-error for="balances.{{ $account->account_number }}" class="mt-2" />
                </div>
            @endif
        @endforeach

        <div class="col-span-6 sm:col-span-4 mt-4 text-center">
            <x-label value="{{ __('LIABILITY') }}" />
        </div>

        @foreach ($chartOfAccounts as $account)
            @if ($account->account_type == 'LIABILITY')
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <x-label for="{{ $account->account_number }}" value="{!! $account->account_name !!}" />
                    <x-input id="{{ $account->account_number }}" type="number" class="mt-1 block w-full"
                        wire:model="balances.{{ $account->account_number }}" autocomplete="off" />
                    <x-input-error for="balances.{{ $account->account_number }}" class="mt-2" />
                </div>
            @endif
        @endforeach

        <div class="col-span-6 sm:col-span-4 mt-4 text-center">
            <x-label value="{{ __('EQUITY') }}" />
        </div>

        @foreach ($chartOfAccounts as $account)
            @if ($account->account_type == 'EQUITY')
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <x-label for="{{ $account->account_number }}" value="{!! $account->account_name !!}" />
                    <x-input id="{{ $account->account_number }}" type="number" class="mt-1 block w-full"
                        wire:model="balances.{{ $account->account_number }}" autocomplete="off" />
                    <x-input-error for="balances.{{ $account->account_number }}" class="mt-2" />
                </div>
            @endif
        @endforeach

        <div class="col-span-6 sm:col-span-4 mt-4 text-center">
            <x-label value="{{ __('REVENUE') }}" />
        </div>

        @foreach ($chartOfAccounts as $account)
            @if ($account->account_type == 'REVENUE')
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <x-label for="{{ $account->account_number }}" value="{!! $account->account_name !!}" />
                    <x-input id="{{ $account->account_number }}" type="number" class="mt-1 block w-full"
                        wire:model="balances.{{ $account->account_number }}" autocomplete="off" />
                    <x-input-error for="balances.{{ $account->account_number }}" class="mt-2" />
                </div>
            @endif
        @endforeach

        <div class="col-span-6 sm:col-span-4 mt-4 text-center">
            <x-label value="{{ __('EXPENSE') }}" />
        </div>

        @foreach ($chartOfAccounts as $account)
            @if ($account->account_type == 'EXPENSE')
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <x-label for="{{ $account->account_number }}" value="{!! $account->account_name !!}" />
                    <x-input id="{{ $account->account_number }}" type="number" class="mt-1 block w-full"
                        wire:model="balances.{{ $account->account_number }}" autocomplete="off" />
                    <x-input-error for="balances.{{ $account->account_number }}" class="mt-2" />
                </div>
            @endif
        @endforeach
    </x-slot>
    @slot('actions')
        @can('edit powas preferences')
            <x-alert-message class="me-3" on="saved" />
            @if ($lockFileExists == true && Auth::user()->hasRole('admin'))
                <x-button wire:loading.attr="disabled">
                    {{ __('Save and Lock') }}
                </x-button>
            @endif

            @if ($lockFileExists == false)
                <x-button wire:loading.attr="disabled">
                    {{ __('Save and Lock') }}
                </x-button>
            @endif
        @endcan

        <x-confirmation-modal wire:model.live="showingConfirmSaveModal" maxWidth="sm">
            @slot('title')
                <span>
                    {{ __('Confirm Add Payment') }}
                </span>
            @endslot
            @slot('content')
                <div>
                    {{ __('Are you sure to want to save beginning balances?') }}
                </div>

                @if (!Auth::user()->hasRole('admin'))
                    <div class="mt-2">
                        {{ __('Please note that this action is irreversible and any changes would require administrative approval.') }}
                    </div>
                @endif
            @endslot
            @slot('footer')
                <x-secondary-button type="button" wire:click="saveAndLock" wire:loading.attr="disabled">
                    {{ __('Yes') }}
                </x-secondary-button>

                <x-danger-button class="ms-3" wire:click="$toggle('showingConfirmSaveModal')" wire:loading.attr="disabled">
                    {{ __('No') }}
                </x-danger-button>
            @endslot
        </x-confirmation-modal>
    @endslot
</x-form-section>
