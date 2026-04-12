<div class="py-4 px-8 space-y-4">
    <x-alert-message class="me-3" on="alert" />
    <div class="w-full grid grid-cols-1 md:grid-cols-2">
        <div class="w-full grid grid-cols-1 md:grid-cols-2">
            <div class="flex items-center">
                <h3 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Reading Input') }}
                </h3>
                {{-- <div class="inline ml-2">
                    <x-label
                        class="inline hover:font-bold cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700 rounded-xl px-1 py-1"
                        wire:click="showDatePicker">
                        &nbsp;<span>{{ __('Set Reading Date') }}</span>
                    </x-label> --}}

                {{-- Logs Modal --}}
                {{-- <x-logs-viewer wire:model.live="showingReadingDatePicker" maxWidth="sm">
                        <x-slot name="title">
                            {{ __('Set Reading Date') }}
                        </x-slot>

                        <x-slot name="content">
                            <x-input type="date" class="w-full block rounded-md" wire:model="readingDateInput" />
                            <x-input-error class="normal-case" for="readingDateInput" />
                        </x-slot>

                        <x-slot name="footer">
                            <x-secondary-button type="button" wire:click="setReadingDate" wire:loading.attr="disabled">
                                {{ __('Set') }}
                            </x-secondary-button>
                            <x-danger-button class="ms-3" wire:click="$toggle('showingReadingDatePicker')"
                                wire:loading.attr="disabled">
                                {{ __('Close') }}
                            </x-danger-button>
                        </x-slot>
                    </x-logs-viewer>
                </div> --}}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-0">
            <div class="mt-2 md:mt-0 md:col-span-2">
                <x-input class="md:inline w-full" id="search" name="search" wire:model.live.debounce.250ms="search"
                    autocomplete="off" placeholder="Search..." />
            </div>
            <div class="flex justify-end items-center">
                <x-label class="text-xs inline mr-2"
                    value="{{ __('Saved: ' . $savedCount . '/' . $membersList->count()) }}" />

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger" class="text-right">
                        <button
                            class="py-1 px-2 text-xs rounded-xl bg-blue-300 md:text-blue-800 hover:bg-blue-400 shadow font-bold">
                            {{ __('ACTIONS') }}
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="not-italic font-normal">
                            <x-dropdown-link wire:click="saveAll" href="#" class="text-xs py-1 my-0 uppercase"
                                wire:loading.attr="disabled">
                                <span>&nbsp;{{ __('Save All') }}</span>
                            </x-dropdown-link>
                        </div>
                    </x-slot>
                </x-dropdown>

                {{-- <x-button wire:click="saveAll" class="inline" wire:loading.attr="disabled">
                    <i class="fa-regular fa-floppy-disk"></i>
                    <span>&nbsp;{{ __('Save All') }}</span>
                </x-button> --}}
            </div>
        </div>
    </div>

    <div class="mt-4">
        @php
            $ctr = 0;
        @endphp

        {{-- @dd($readingIDs) --}}

        @forelse ($membersList as $item => $value)
            @php
                $ctr++;
            @endphp
            <x-reading-card>
                @slot('readingIDs')
                    {{ __('Reading ID: ') }}
                    <span class="font-bold">
                        {{ $readingIDs[$value->member_id] }}
                    </span>
                @endslot
                @slot('counter')
                    {{ $ctr }}
                @endslot
                @slot('memberName')
                    {{ $value->lastname . ', ' . $value->firstname }}
                @endslot
                @slot('readCount')
                    <div class="w-full">
                        <label class="text-xs block text-slate-800"
                            for="readingCounts.{{ $value->member_id }}">{{ __('Reading Number:') }}</label>
                        <input class="w-full block rounded-md" type="number"
                            wire:model="readingCounts.{{ $value->member_id }}"
                            {{ $isInitialReading[$value->member_id] ? '' : 'disabled' }} />
                        <x-input-error class="normal-case" for="readingCounts.{{ $value->member_id }}" />
                    </div>
                @endslot
                @slot('previousReading')
                    <div class="w-full">
                        <label class="text-xs block text-slate-800"
                            for="previousReading.{{ $value->member_id }}">{{ __('Previous Reading:') }}</label>
                        <input type="number" class="w-full block rounded-md"
                            wire:model="previousReading.{{ $value->member_id }}" disabled />
                        <x-input-error class="normal-case" for="previousReading.{{ $value->member_id }}" />
                    </div>
                @endslot
                @slot('presentReading')
                    <div class="w-full">
                        <label class="text-xs block text-slate-800"
                            for="presentReading.{{ $value->member_id }}">{{ __('Present Reading:') }}</label>
                        <input type="number" class="w-full block rounded-md"
                            wire:model="presentReading.{{ $value->member_id }}" />
                        <x-input-error class="normal-case" for="presentReading.{{ $value->member_id }}" />
                    </div>
                @endslot
                @slot('readingDate')
                    <div class="w-full">
                        <label class="text-xs block text-slate-800"
                            for="readingDate.{{ $value->member_id }}">{{ __('Reading Date:') }}</label>
                        <input type="date" class="w-full block rounded-md"
                            wire:model="readingDate.{{ $value->member_id }}" />
                        <x-input-error class="normal-case" for="readingDate.{{ $value->member_id }}" />
                    </div>
                @endslot
                @slot('buttons')
                    <div class="w-full text-end">
                        <x-action-message class="normal-case inline mr-2" on="saved_{{ $readingIDs[$value->member_id] }}">
                            <i class="fa-solid fa-floppy-disk font-bold text-xl text-green-800"></i>
                        </x-action-message>

                        <x-button
                            wire:click="saveReading('{{ $readingIDs[$value->member_id] }}', '{{ $value->member_id }}')"
                            class="inline" wire:loading.attr="disabled">
                            <i class="fa-regular fa-floppy-disk"></i>
                            <span>&nbsp;{{ __('Save') }}</span>
                        </x-button>
                    </div>
                @endslot
            </x-reading-card>
        @empty
            <div class="my-2 text-center">
                <x-label class="text-xl font-black my-16" value="{{ __('No records found!') }}" />
            </div>
        @endforelse
    </div>
</div>
