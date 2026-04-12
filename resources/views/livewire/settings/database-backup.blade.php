<x-settings-box>
    <x-slot name="title">
        {{ __('Database Backup') }}
    </x-slot>
    <x-slot name="description">
        {{ __('Create, download, and restore backups of your entire application database securely.') }}
        <p class="mt-2 text-xs text-red-600 dark:text-red-400">
            <b>{{ __('Warning:') }}</b> {{ __('Restoring a backup will overwrite your current database completely.') }}
        </p>
    </x-slot>

    <x-slot name="contents">
        <div class="w-full">
            <x-alert-message class="mb-4" on="backupMessage" />

            <div class="flex justify-start mb-4">
                <x-button wire:click="createBackup" wire:loading.attr="disabled" class="bg-blue-600 hover:bg-blue-500">
                    <span wire:loading.remove wire:target="createBackup">
                        <i class="fa-solid fa-database"></i>&nbsp;{{ __('Create New Backup') }}
                    </span>
                    <span wire:loading wire:target="createBackup">
                        <i class="fa-solid fa-spinner fa-spin"></i>&nbsp;{{ __('Backing up...') }}
                    </span>
                </x-button>
            </div>

            <x-table.table class="text-sm">
                <x-slot name="thead">
                    <x-table.thead>
                        <x-table.thead-th>{{ __('Date') }}</x-table.thead-th>
                        <x-table.thead-th>{{ __('File Name') }}</x-table.thead-th>
                        <x-table.thead-th class="text-center">{{ __('Size') }}</x-table.thead-th>
                        <x-table.thead-th class="text-center">{{ __('Actions') }}</x-table.thead-th>
                    </x-table.thead>
                </x-slot>
                <x-slot name="tbody">
                    <tbody>
                        @forelse ($backups as $backup)
                            <x-table.tbody-tr>
                                <x-table.tbody-td class="pl-2">{{ $backup['date'] }}</x-table.tbody-td>
                                <x-table.tbody-td class="pl-2">{{ $backup['name'] }}</x-table.tbody-td>
                                <x-table.tbody-td class="text-center">{{ $backup['size'] }}</x-table.tbody-td>
                                <x-table.tbody-td class="text-center">
                                    <button wire:click="downloadBackup('{{ $backup['name'] }}')" class="text-blue-500 hover:text-blue-700 mx-1" title="Download">
                                        <i class="fa-solid fa-download"></i>
                                    </button>
                                    <button wire:click="confirmRestore('{{ $backup['name'] }}')" class="text-green-500 hover:text-green-700 mx-1" title="Restore">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </button>
                                    <button wire:click="confirmDelete('{{ $backup['name'] }}')" class="text-red-500 hover:text-red-700 mx-1" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </x-table.tbody-td>
                            </x-table.tbody-tr>
                        @empty
                            <x-table.tbody-tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">
                                    {{ __('No backups found.') }}
                                </td>
                            </x-table.tbody-tr>
                        @endforelse
                    </tbody>
                </x-slot>
            </x-table.table>
        </div>

        {{-- Confirm Restore Modal --}}
        <x-confirmation-modal wire:model.live="showingConfirmRestoreModal" maxWidth="md">
            <x-slot name="title">
                <span class="text-red-600 font-bold"><i class="fa-solid fa-triangle-exclamation"></i> {{ __('Caution: Overwrite Database') }}</span>
            </x-slot>

            <x-slot name="content">
                {{ __('Are you sure you want to restore ') }} <b>{{ $selectedBackup }}</b>?
                <br><br>
                <div class="p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ __('This will WIPE all your current data and replace it with the records from this backup! This action cannot be easily undone.') }}
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-danger-button wire:click="restoreBackup" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="restoreBackup">
                        <i class="fa-solid fa-check"></i>&nbsp;{{ __('Yes, Restore Now') }}
                    </span>
                    <span wire:loading wire:target="restoreBackup">
                        <i class="fa-solid fa-spinner fa-spin"></i>&nbsp;{{ __('Restoring...') }}
                    </span>
                </x-danger-button>

                <x-secondary-button class="ms-3" wire:click="$toggle('showingConfirmRestoreModal')" wire:loading.attr="disabled">
                    <i class="fa-solid fa-circle-xmark"></i>&nbsp;{{ __('Cancel') }}
                </x-secondary-button>
            </x-slot>
        </x-confirmation-modal>

        {{-- Confirm Delete Modal --}}
        <x-confirmation-modal wire:model.live="showingConfirmDeleteModal" maxWidth="sm">
            <x-slot name="title">
                {{ __('Delete Backup') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Are you sure you want to delete ') }} <b>{{ $selectedBackup }}</b>?
            </x-slot>

            <x-slot name="footer">
                <x-danger-button wire:click="deleteBackup" wire:loading.attr="disabled">
                    <i class="fa-solid fa-trash"></i>&nbsp;{{ __('Delete') }}
                </x-danger-button>

                <x-secondary-button class="ms-3" wire:click="$toggle('showingConfirmDeleteModal')" wire:loading.attr="disabled">
                    <i class="fa-solid fa-circle-xmark"></i>&nbsp;{{ __('Cancel') }}
                </x-secondary-button>
            </x-slot>
        </x-confirmation-modal>

    </x-slot>
</x-settings-box>
