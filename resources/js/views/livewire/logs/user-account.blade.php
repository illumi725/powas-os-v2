<div class="inline">
    <x-label class="inline hover:font-bold cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700 rounded-xl px-1 py-1"
        wire:click="showLogsModal">
        &nbsp;<span>{{ __('Changes Logs') }}</span>
    </x-label>

    {{-- Logs Modal --}}
    <x-logs-viewer wire:model.live="showingLogsModal" maxWidth="2xl">
        <x-slot name="title">
            {{ __('User Account Logs') }}
        </x-slot>

        <x-slot name="content">
            <div class="mb-4">
                @forelse ($changesLogs as $log)
                    <div class="py-1 px-1 hover:bg-gray-300 dark:hover:bg-gray-100 rounded-lg hover:text-gray-900 group">
                        <label class="text-xs">
                            <span
                                class="bg-gray-800 text-gray-200 dark:bg-gray-200 dark:text-gray-800 px-1 font-mono dark:group-hover:bg-gray-700 dark:group-hover:text-gray-200">{{ __('[') . $log->created_at . __(']') }}</span>
                            {!! $log->log_message !!}
                        </label>
                    </div>
                @empty
                    <x-label value="{{ __('No logs found!') }}" />
                @endforelse
            </div>
            <div>
                {{ $changesLogs->links() }}
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-danger-button class="ms-3" wire:click="$toggle('showingLogsModal')" wire:loading.attr="disabled">
                <i class="fa-solid fa-circle-xmark"></i>&nbsp;
                {{ __('Close') }}
            </x-danger-button>
        </x-slot>
    </x-logs-viewer>
</div>
