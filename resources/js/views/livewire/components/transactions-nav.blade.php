<div>
    <div>
        <x-trans-nav active="{{ $currentView == 'journal-entries' }}" wire:click="changeView('journal-entries')">
            <span>
                {{ __('Journal Entries') }}
            </span>
        </x-trans-nav>
        <x-trans-nav active="{{ $currentView == 'cash' }}" wire:click="changeView('cash')">
            <span>
                {{ __('Cash') }}
            </span>
        </x-trans-nav>
        <x-trans-nav active="{{ $currentView == 'bank' }}" wire:click="changeView('bank')">
            <span>
                {{ __('Bank') }}
            </span>
        </x-trans-nav>
        <x-trans-nav active="{{ $currentView == 'revenues' }}" wire:click="changeView('revenues')">
            <span>
                {{ __('Revenues') }}
            </span>
        </x-trans-nav>
        <x-trans-nav active="{{ $currentView == 'expenses' }}" wire:click="changeView('expenses')">
            <span>
                {{ __('Expenses and Payments') }}
            </span>
        </x-trans-nav>
        {{-- <x-trans-nav active="{{ $currentView == 'payments' }}" wire:click="changeView('payments')">
            <span>
                {{ __('Payments') }}
            </span>
        </x-trans-nav> --}}
    </div>

    <x-loading-data on='show-loading' />

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mt-2 {{ $showData }}">
        @if ($currentView == 'journal-entries')
            @livewire('transactions.transactions-list', ['powasID' => $powasID, 'powas' => $powas])
        @endif
        @if ($currentView == 'revenues')
            @livewire('transactions.revenues-list', ['powasID' => $powasID, 'powas' => $powas])
        @endif
        @if ($currentView == 'expenses')
            @livewire('transactions.expenses-list', ['powasID' => $powasID, 'powas' => $powas])
        @endif
        @if ($currentView == 'cash')
            @livewire('transactions.cash-list', ['powasID' => $powasID, 'powas' => $powas])
        @endif
        @if ($currentView == 'bank')
            @livewire('transactions.bank-list', ['powasID' => $powasID, 'powas' => $powas])
        @endif
        {{-- @if ($currentView == 'payments')
            @livewire('transactions.expenses-list', ['powasID' => $powasID, 'powas' => $powas])
        @endif --}}
    </div>
</div>
