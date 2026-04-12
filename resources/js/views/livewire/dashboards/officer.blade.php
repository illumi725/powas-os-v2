<div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
    <x-dashboard-card>
        <x-slot name="content">
            <span class="number">{{ $applicationCount }}</span>
        </x-slot>
        <x-slot name="icon">
            <i class="fa-solid fa-file-lines"></i>
        </x-slot>
        <x-slot name="title">
            {{ __('Applications') }}
        </x-slot>
        <x-slot name="actions">
            <x-button type="button" wire:click="applicationsView" wire:loading.attr="disabled"><i
                    class="fa-regular fa-eye"></i>&nbsp;{{ __('View') }}</x-button>
        </x-slot>
    </x-dashboard-card>

    <x-dashboard-card>
        <x-slot name="content">
            @if ($recordsCount > 999999)
                <span class="number">{{ '999999+' }}</span>
            @else
                <span class="number">{{ $recordsCount }}</span>
            @endif

        </x-slot>
        <x-slot name="icon">
            <i class="fa-solid fa-folder-tree"></i>
        </x-slot>
        <x-slot name="title">
            {{ __('POWAS Records') }}
        </x-slot>
        <x-slot name="actions">
            <x-button type="button" wire:click="powasView" wire:loading.attr="disabled"><i
                    class="fa-regular fa-eye"></i>&nbsp;{{ __('View') }}</x-button>
        </x-slot>
    </x-dashboard-card>

    <x-dashboard-card>
        <x-slot name="content">
            <span class="number">{{ $membersCount }}</span>
        </x-slot>
        <x-slot name="icon">
            <i class="fa-solid fa-people-roof"></i>
        </x-slot>
        <x-slot name="title">
            {{ __('Members') }}
        </x-slot>
        <x-slot name="actions">
            <x-button type="button" wire:click="membersView" wire:loading.attr="disabled"><i
                    class="fa-regular fa-eye"></i>&nbsp;{{ __('View') }}</x-button>
        </x-slot>
    </x-dashboard-card>
</div>
