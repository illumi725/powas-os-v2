<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
            <span>
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <x-button type="button" disabled>
                        {!! __('pagination.previous') !!}
                    </x-button>
                @else
                    <x-button type="button" wire:click="previousPage" wire:loading.attr="disabled" rel="prev">
                        {!! __('pagination.previous') !!}
                    </x-button>
                @endif
            </span>

            <span>
                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <x-button type="button" wire:click="nextPage" wire:loading.attr="disabled" rel="next">
                        {!! __('pagination.next') !!}
                    </x-button>
                @else
                    <x-button type="button" disabled>
                        {!! __('pagination.next') !!}
                    </x-button>
                @endif
            </span>
        </nav>
    @endif
</div>
