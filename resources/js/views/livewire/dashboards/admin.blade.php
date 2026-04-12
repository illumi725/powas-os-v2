<div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8" x-data="{ expanded: '' }">
    <div class="col-span-1 md:col-span-3">
        <span class="font-bold cursor-pointer uppercase dark:text-white"
            @click="expanded = ('filter' === expanded) ? '' : 'filter'">
            {{ __('Filter') }}
            &nbsp;
            <span x-show="expanded !== 'filter'"><i class="fa-solid fa-chevron-right"></i></span>
            <span x-show="expanded === 'filter'"><i class="fa-solid fa-chevron-down"></i></span>
        </span>
    </div>

    <div x-show="expanded === 'filter'" {{-- x-transition:enter="transition-all ease-out duration-300"
        x-transition:enter-start="transform scale-y-0"
        x-transition:enter-end="transform scale-y-100"
        x-transition:leave="transition-all ease-in duration-300"
        x-transition:leave-start="transform scale-y-100"
        x-transition:leave-end="transform scale-y-0" --}}
        class="grid grid-cols-1 md:grid-cols-4 md:col-span-3 gap-4 overflow-hidden" x-collapse>
        <div class="w-full">
            <x-label for="region" value="{{ __('Region: ') }}" />
            <x-combobox class="mt-1 block w-full" id="region" name="region" wire:model.live="region"
                wire:change="loadprovince">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Region-') }}</option>
                    @foreach ($regionlist as $regionname)
                        <option value="{{ $regionname }}">{{ $regionname }}</option>
                    @endforeach
                </x-slot>
            </x-combobox>
        </div>
        <div class="w-full">
            <x-label for="province" value="{{ __('Province: ') }}" />
            <x-combobox class="mt-1 block w-full" id="province" name="province" wire:model.live="province"
                wire:change="loadmunicipality">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Province-') }}</option>
                    @foreach ($provincelist as $provincename)
                        <option value="{{ $provincename }}">{{ $provincename }}</option>
                    @endforeach
                </x-slot>
            </x-combobox>
        </div>
        <div class="w-full">
            <x-label for="municipality" value="{{ __('Municipality: ') }}" />
            <x-combobox class="mt-1 block w-full" id="municipality" name="municipality" wire:model.live="municipality"
                wire:change="loadpowas">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select Municipality-') }}</option>
                    @foreach ($municipalitylist as $municipalityname)
                        <option value="{{ $municipalityname }}">{{ $municipalityname }}</option>
                    @endforeach
                </x-slot>
            </x-combobox>
        </div>
        <div class="w-full">
            <x-label for="powas" value="{{ __('POWAS: ') }}" />
            <x-combobox class="mt-1 block w-full" id="powas" name="powas" wire:model.live="powas">
                <x-slot name="options">
                    <option value="" disabled>{{ __('-Select POWAS-') }}</option>
                    @forelse ($powaslist as $powas)
                        <option value="{{ $powas->powas_id }}">
                            {{ $powas->barangay . ' POWAS ' . $powas->phase . ' (' . $powas->zone . ')' }}
                        </option>
                    @empty
                    @endforelse
                </x-slot>
            </x-combobox>
            <x-input-error for="powas" class="mt-1" />
        </div>
    </div>

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
            <span class="number">{{ $powasCount }}</span>
        </x-slot>
        <x-slot name="icon">
            <i class="fa-solid fa-network-wired"></i>
        </x-slot>
        <x-slot name="title">
            {{ __('POWAS Cooperatives') }}
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
