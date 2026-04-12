<x-action-section>
    <x-slot name="title">
        {{ __('Application Status Inquiry') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Enter reference number to check status of your POWAS application.') }}
    </x-slot>
    <x-slot name="content">
        <form wire:submit="showReferenceResultModal" method="get">
            <div class="col-span-6">
                <x-label for="referencenumber" value="{{ __('Reference Number') }}" />
                <x-input class="mt-1 block w-full" placeholder="12345678" type="number" id="referencenumber"
                    name="referencenumber" wire:model="referencenumber" autocomplete="off" />
                <x-input-error for="referencenumber" class="mt-2" />
            </div>
        </form>
        <div class="mt-5 flex justify-center">
            <x-button wire:click="showReferenceResultModal" wire:loading.attr="disabled">
                <i class="fa-solid fa-magnifying-glass"></i>&nbsp;{{ __('Search') }}
            </x-button>

            <x-dialog-modal wire:model.live="showingReferenceResultModal" maxWidth="md">
                <x-slot name="title">
                    {{ __('Application Status Inquiry') }}
                </x-slot>
                <x-slot name="content">
                    <div>
                        <span class="block w-full">
                            {{ __('Reference Number: ') }}
                            @isset($applicationstatus)
                                <b>{{ $applicationstatus->application_id }}</b>
                            @endisset
                        </span>
                        @isset($applicationstatus)
                            <span class="block w-full">
                                {{ __('Application Status: ') }}
                                <b>{{ $applicationstatus->application_status }}</b>
                            </span>
                            @if ($applicationstatus->application_status == 'REJECTED')
                                <span class="block w-full">
                                    {{ __('Reason: ') }}
                                    <b>{{ $applicationstatus->reject_reason }}</b>
                                </span>
                            @endif
                        @endisset
                    </div>
                    <div class="w-full text-center mt-4">
                        @isset($applicationstatus)
                            @if ($applicationstatus->application_status == 'REJECTED')
                                <p class="font-bold cursor-pointer text-red-500 dark:text-red-600 text-xs hover:text-red-600 hover:dark:text-red-500"
                                    wire:click="showDeleteConfirmationModal">
                                    {{ __('CANCEL/DELETE APPLICATION') }}
                                </p>
                            @endif

                            <div class="w-full grid grid-cols-1 md:grid-cols-2 text-center mt-4 mb-2 gap-4">
                                <form action="{{ route('application-form.view', ['applicationid' => $referencenumber]) }}"
                                    method="post" target="_blank">
                                    @csrf
                                    <button type="submit"
                                        class="w-full uppercase font-bold text-xs px-2 py-2 rounded-md bg-green-400 text-green-950 hover:bg-green-500 mr-2">
                                        <i class="fa-solid fa-file-pdf"></i>
                                        &nbsp;
                                        {{ __('View Application') }}
                                    </button>
                                </form>

                                <form
                                    action="{{ route('application-form.download', ['applicationid' => $referencenumber]) }}"
                                    method="post">
                                    @csrf
                                    <button type="submit"
                                        class="w-full uppercase font-bold text-xs px-2 py-2 rounded-md bg-green-400 text-green-950 hover:bg-green-500">
                                        <i class="fa-solid fa-download"></i>
                                        &nbsp;
                                        {{ __('Download Application') }}
                                    </button>
                                </form>

                            </div>
                        @endisset
                    </div>
                </x-slot>
                <x-slot name="footer">
                    <x-danger-button wire:click="$toggle('showingReferenceResultModal')" wire:loading.attr="disabled">
                        {{ __('Close') }}
                    </x-danger-button>
                </x-slot>
            </x-dialog-modal>
        </div>
        @isset($applicationstatus->application_id)
            <x-dialog-modal wire:model.live="showingDeleteConfirmationModal" maxWidth="sm">
                <x-slot name="title">
                    <div class="text-left">
                        {{ __('Delete POWAS Application') }}
                    </div>
                </x-slot>

                <x-slot name="content" class="text-left">
                    <div class="text-left">
                        {{ __('Type \'' . $applicationstatus->application_id) . '\' to confirm deletion:' }}
                        <x-input type="number" name="inputconfirmation" class="mt-1 block w-full"
                            wire:model="inputconfirmation" autocomplete="off" />
                        <x-input-error for="inputconfirmation" class="mt-2" />
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <x-secondary-button wire:click="$toggle('showingDeleteConfirmationModal')" wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button class="ms-3" wire:click="deleteApplication" wire:loading.attr="disabled">
                        {{ __('Delete') }}
                    </x-danger-button>
                </x-slot>
            </x-dialog-modal>
        @endisset

        <x-alert-message class="me-3" on="notfound" />
    </x-slot>
</x-action-section>
