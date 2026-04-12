<div>
    <x-dialog-modal wire:model.live="showingModal" maxWidth="lg">
        <x-slot name="title">
            {{ __('Change Water Meter') }}
            @if($member)
                - {{ $member->applicationinfo->lastname }}, {{ $member->applicationinfo->firstname }}
            @endif
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="oldMeterNumber" value="{{ __('Old Meter Number') }}" />
                    <x-input id="oldMeterNumber" type="text" class="mt-1 block w-full bg-gray-100" wire:model="oldMeterNumber" disabled />
                </div>

                <div class="mt-4">
                    <label class="flex items-center">
                        <x-checkbox wire:model.live="isOldMeterBroken" />
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Old Meter is Broken / Unreadable') }}</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ __('If the meter is broken, its final reading will be ignored and average consumption will be used instead.') }}
                    </p>
                </div>

                @if(!$isOldMeterBroken)
                <div class="mt-4">
                    <x-label for="oldMeterFinalReading" value="{{ __('Old Meter Final Reading') }}" />
                    <x-input id="oldMeterFinalReading" type="number" step="0.01" class="mt-1 block w-full" wire:model="oldMeterFinalReading" />
                    <x-input-error for="oldMeterFinalReading" class="mt-2" />
                </div>
                @endif

                <div class="mt-4">
                    <x-label for="newMeterNumber" value="{{ __('New Meter Number') }}" />
                    <x-input id="newMeterNumber" type="text" class="mt-1 block w-full" wire:model="newMeterNumber" />
                    <x-input-error for="newMeterNumber" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-label for="newMeterStartReading" value="{{ __('New Meter Initial/Start Reading') }}" />
                    <x-input id="newMeterStartReading" type="number" step="0.01" class="mt-1 block w-full" wire:model="newMeterStartReading" />
                    <p class="mt-1 text-xs text-gray-500">
                        {{ __('Typically 0 for a brand new meter.') }}
                    </p>
                    <x-input-error for="newMeterStartReading" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-label for="changeDate" value="{{ __('Date Changed') }}" />
                    <x-input id="changeDate" type="date" class="mt-1 block w-full" wire:model="changeDate" />
                    <x-input-error for="changeDate" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-label for="reason" value="{{ __('Reason (Optional)') }}" />
                    <x-input id="reason" type="text" class="mt-1 block w-full" wire:model="reason" />
                    <x-input-error for="reason" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('showingModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ms-3" wire:click="save" wire:loading.attr="disabled">
                {{ __('Save Meter Change') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
