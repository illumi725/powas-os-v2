<div class="py-4 px-4 space-y-4">
    <x-alert-message class="me-3" on="alert" />

    {{-- Search and Pagination Control --}}
    <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="w-full">
            <x-label class="inline" for="search" value="{{ __('Search: ') }}" />
            <x-input class="w-full md:w-auto" id="search" name="search" wire:model.live="search" autocomplete="off"
                placeholder="Search..." />
        </div>

        @php
            $grid = '';
            $colspan = '';
        @endphp

        @can('add powas')
            @php
                $grid = 'grid grid-cols-1 md:grid-cols-4 gap-4';
                $colspan = 'md:col-span-3';
            @endphp
        @endcan

        <div class="w-full {{ $grid }}">
            <div class="w-full md:text-right {{ $colspan }}">
                <x-label class="inline" for="pagination" value="{{ __('# of rows per page: ') }}" />
                <x-combobox class="w-full md:w-auto" id="pagination" name="pagination" wire:model.live="pagination">
                    <x-slot name="options">
                        @for ($i = 12; $i <= 120; $i = $i + 12)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </x-slot>
                </x-combobox>
            </div>

            @can('add powas')
                <div class="w-full flex justify-end md:items-center">
                    <x-button type="button" wire:click="showAddPOWASModal" wire:loading.attr="disabled">
                        <i class="fa-solid fa-file-circle-plus"></i>
                        <span>&nbsp;{{ __('New') }}</span>
                    </x-button>
                </div>

                {{-- Add POWAS Dialog --}}
                <x-dialog-modal wire:model.live="showingAddPOWASModal" maxWidth="md">
                    <x-slot name="title">
                        {{ __('Add POWAS') }}
                    </x-slot>
                    <x-slot name="content">
                        <form wire:submit="addPOWAS">
                            @csrf
                            <!-- Region  -->
                            <div class="col-span-6 sm:col-span-4">
                                <x-label for="regionInput" value="{{ __('Region') }}" />
                                <x-combobox name="regionInput" id="regionInput" class="mt-1 block w-full"
                                    wire:model="regionInput" wire:change="loadprovince(true)">
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
                                <x-combobox name="provinceInput" id="provinceInput" class="mt-1 block w-full"
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
                                <x-combobox name="municipalityInput" id="municipalityInput" class="mt-1 block w-full"
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
                                <x-combobox name="barangayInput" id="barangayInput" class="mt-1 block w-full"
                                    wire:model="barangayInput" wire:change="loadPhaseName">
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
                                <x-input id="zoneInput" type="text" class="mt-1 block w-full" wire:model="zoneInput"
                                    autocomplete="off" />
                                <x-input-error for="zoneInput" class="mt-2" />
                            </div>

                            <!-- Phase -->
                            <div class="col-span-6 sm:col-span-4 mt-4">
                                <x-label for="phaseInput" value="{{ __('Phase Name') }}" />
                                <x-input id="phaseInput" type="text" class="mt-1 block w-full"
                                    wire:model="phaseInput" autocomplete="off" />
                                <x-input-error for="phaseInput" class="mt-2" />
                            </div>

                            <!-- Inauguation Date -->
                            <div class="col-span-6 sm:col-span-4 mt-4">
                                <x-label for="inaugurationInput" value="{{ __('Inauguration Date') }}" />
                                <x-input id="inaugurationInput" type="date" class="mt-1 block w-full"
                                    wire:model="inaugurationInput" autocomplete="off" />
                                <x-input-error for="inaugurationInput" class="mt-2" />
                            </div>

                            <!-- Status  -->
                            <div class="col-span-6 sm:col-span-4 mt-4">
                                <x-label for="statusInput" value="{{ __('Status') }}" />
                                <x-combobox name="statusInput" id="statusInput" class="mt-1 block w-full"
                                    wire:model="statusInput">
                                    <x-slot name="options">
                                        <option value="" disabled>{{ __('-Select Status-') }}</option>
                                        <option value="ACTIVE">{{ __('ACTIVE') }}</option>
                                        <option value="INACTIVE">{{ __('INACTIVE') }}</option>
                                    </x-slot>
                                </x-combobox>
                                <x-input-error for="statusInput" class="mt-2" />
                            </div>
                        </form>
                    </x-slot>

                    <x-slot name="footer">
                        <x-button type="button" wire:click="addPOWAS" class="mr-2" wire:loading.attr="disabled">
                            <i class="fa-regular fa-floppy-disk"></i>
                            <span>&nbsp;{{ __('Save') }}</span>
                        </x-button>

                        <x-danger-button wire:click="$toggle('showingAddPOWASModal')" wire:loading.attr="disabled">
                            <i class="fa-regular fa-circle-xmark"></i>
                            <span>&nbsp;{{ __('Cancel') }}</span>
                        </x-danger-button>
                    </x-slot>
                </x-dialog-modal>
            @endcan
        </div>
    </div>

    <div class="mt-4">
        @can('view logs')
            @livewire('logs.powas-logs')
        @endcan
    </div>

    {{-- POWAS Cards List --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pb-4">
        @forelse ($powaslist as $powas)
            <div class="text-slate-800 border border-slate-400 dark:border-none dark:bg-slate-300 shadow-md hover:scale-105 dark:hover:scale-105 rounded-lg"
                wire:key="{{ $powas->powas_id }}">
                <div class="py-4 rounded-lg border-b-8 border-slate-400 dark:border-slate-600">
                    <div class="mx-4 text-sm italic font-black grid grid-cols-2">
                        <div class="inline">
                            {{ $powas->powas_id }}
                        </div>

                        @canany(['edit powas', 'delete powas', 'view powas records', 'view transactions'])
                            <div class="inline w-full">
                                <div class="flex justify-end">
                                    <div class="ms-3 relative">
                                        <x-dropdown align="right" width="48">
                                            <x-slot name="trigger" class="text-right">
                                                <button
                                                    class="py-1 px-2 text-xs rounded-xl bg-blue-300 md:text-blue-800 hover:bg-blue-400 shadow font-bold">
                                                    {{ __('ACTIONS') }}
                                                </button>
                                            </x-slot>

                                            <x-slot name="content">
                                                <div class="not-italic font-normal">
                                                    @can('view powas records')
                                                        <x-dropdown-link
                                                            href="{{ route('powas.records', ['powasID' => $powas->powas_id]) }}"
                                                            class="text-xs py-1 my-0">
                                                            {{ __('BILLING SYSTEM') }}
                                                        </x-dropdown-link>
                                                    @endcan
                                                    @can('view transactions')
                                                        <x-dropdown-link
                                                            href="{{ route('view-transactions', ['powasID' => $powas->powas_id]) }}"
                                                            class="text-xs py-1 my-0">
                                                            {{ __('TRANSACTIONS') }}
                                                        </x-dropdown-link>
                                                    @endcan
                                                    @can('edit powas')
                                                        <x-dropdown-link
                                                            href="{{ route('powas.show', ['powas_id' => $powas->powas_id]) }}"
                                                            class="text-xs py-1 my-0">{{ __('EDIT POWAS') }}</x-dropdown-link>
                                                    @endcan

                                                    @php
                                                        $applicationCounter = \App\Models\PowasApplications::where(
                                                            'application_status',
                                                            'PENDING',
                                                        )
                                                            ->orWhere('application_status', 'VERIFIED')
                                                            ->where('powas_id', $powas->powas_id)
                                                            // ->get();
                                                            ->count();

                                                        $membersCounter = \App\Models\PowasApplications::where(
                                                            'application_status',
                                                            'APPROVED',
                                                        )
                                                            ->where('powas_id', $powas->powas_id)
                                                            ->count();
                                                    @endphp

                                                    @if ($applicationCounter == 0 && $membersCounter == 0)
                                                        @can('delete powas')
                                                            <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                                            <x-dropdown-link href="#"
                                                                wire:click="showDeleteConfirmationModal({{ $powas }})"
                                                                class="text-xs py-1 my-0">{{ __('DELETE POWAS') }}</x-dropdown-link>
                                                        @endcan
                                                    @endif
                                                </div>
                                            </x-slot>
                                        </x-dropdown>
                                    </div>
                                </div>
                            </div>
                        @endcanany
                    </div>
                    <div class="mx-4 font-black whitespace-nowrap overflow-hidden">
                        {{ $powas->barangay . ' POWAS ' . $powas->phase }}
                    </div>
                    <div class="mx-4 text-xs italic whitespace-nowrap overflow-hidden">
                        {{ $powas->zone . ', ' . $powas->barangay . ', ' . $powas->municipality . ', ' . $powas->province }}
                    </div>

                    @php
                        if ($powas->status == 'ACTIVE') {
                            $statusStyle = 'bg-green-400 text-green';
                            $icon = '<i class="fa-regular fa-circle-check"></i>';
                        } else {
                            $statusStyle = 'bg-red-400 text-red';
                            $icon = '<i class="fa-regular fa-circle-xmark"></i>';
                        }
                    @endphp

                    <div class="mx-4 grid grid-cols-2">
                        <div>
                            <span
                                class="py-1 px-2 rounded-xl text-xs font-bold shadow {!! $statusStyle !!}">{!! $icon . '&nbsp;' . $powas->status !!}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-3 text-center text-slate-800 my-16">
                <x-label class="text-xl font-black" value="{{ __('No records found!') }}" />
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>
        {{ $powaslist->links() }}
    </div>

    {{-- Delete POWAS Confirmation --}}
    <x-dialog-modal wire:model.live="showingDeleteConfirmationModal" maxWidth="sm">
        <x-slot name="title">
            <div class="text-left">
                {{ __('Delete POWAS') }}
            </div>
        </x-slot>

        <x-slot name="content" class="text-left">
            <div class="text-left">
                @if (isset($selectedpowas))
                    {{ __('Type \'') . $selectedpowas->powas_id . __('\' to confirm POWAS deletion:') }}
                @endif

                <x-input type="text" name="inputconfirmation" class="mt-1 block w-full"
                    wire:model="inputconfirmation" autocomplete="off" />
                <x-input-error for="inputconfirmation" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('showingDeleteConfirmationModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>
            @if (isset($selectedpowas))
                <x-danger-button class="ms-3" wire:click="delete('{{ $selectedpowas->powas_id }}')"
                    wire:loading.attr="disabled">
                    {{ __('Delete') }}
                </x-danger-button>
            @endif
        </x-slot>
    </x-dialog-modal>
</div>
