<div class="py-4 px-4 space-y-4">
    <x-alert-message class="me-3" on="alert" />
    <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 px-4">
        <div class="w-full grid grid-cols-1 md:flex md:items-center gap-4">
            <span class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Knowledge Base') }}
            </span>
        </div>
    </div>
    <div class="w-full px-4 pb-4">
        @if ($pocaData == null || count($pocaData) == 0)
            <div class="my-2 w-full text-center">
                <x-label class="text-xl font-black my-16" value="{{ __('Knowledge Base is currently empty!') }}" />
            </div>
        @else
            <div class="overflow-x-auto overflow-y-auto">
                <table class="border border-collapse min-w-max md:w-full">
                    <thead class="bg-gray-400">
                        <tr>
                            <th class="p-1">
                                <span>
                                    {{ __('SL#') }}
                                </span>
                            </th>
                            <th class="p-1" width="340">
                                <span>
                                    {{ __('QUESTION/INQUIRY') }}
                                </span>
                            </th>
                            <th class="p-1" width="560">
                                <span>
                                    {{ __('ANSWER/RESPONSE') }}
                                </span>
                            </th>
                            <th class="p-1" width="50">
                                <span>
                                    {{ __('NEED LOGIN?') }}
                                </span>
                            </th>
                            <th class="p-1">
                                <span>
                                    {{ __('ACTIONS') }}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach (array_reverse($pocaData['questions']) as $item)
                            <tr class="even:bg-gray-100 odd:bg-slate-200 hover:font-bold hover:bg-gray-300 cursor-pointer"
                                wire:key="{{ $item['id'] }}">
                                <th class="flex justify-center px-3 py-1 whitespace-nowrap">
                                    <span>
                                        {{ count($pocaData['questions']) - $item['id'] + 1 }}
                                    </span>
                                </th>
                                <td x-data="{
                                    question{{ $item['id'] }}: false
                                }" class="px-3 py-1 align-text-top">
                                    <span x-show="!question{{ $item['id'] }}" class="text-wrap"
                                        x-on:click="question{{ $item['id'] }} = ! question{{ $item['id'] }}"
                                        @click="$refs.question{{ $item['id'] }}.focus()">
                                        {!! $item['question'] !!}
                                    </span>
                                    <textarea x-ref="question{{ $item['id'] }}" class="block mt-1 w-full rounded-md"
                                        wire:model="question.{{ $item['id'] }}" type="text" autocomplete="off" style="resize: none"
                                        x-show="question{{ $item['id'] }}" x-on:blur="question{{ $item['id'] }} = false"
                                        wire:blur="editQuestion({{ $item['id'] }})" @keyup.escape="question{{ $item['id'] }} = false"></textarea>
                                </td>
                                <td x-data="{
                                    answer{{ $item['id'] }}: false
                                }" class="px-3 py-1 align-text-top">
                                    <span x-show="!answer{{ $item['id'] }}" class="text-wrap"
                                        x-on:click="answer{{ $item['id'] }} = ! answer{{ $item['id'] }}"
                                        @click="$refs.answer{{ $item['id'] }}.focus()">
                                        @if ($item['answer'] == '')
                                            {{ __('<no answer yet>') }}
                                        @else
                                            {!! $item['answer'] !!}
                                        @endif
                                    </span>
                                    <textarea x-ref="answer{{ $item['id'] }}" class="block mt-1 w-full rounded-md"
                                        wire:model="answer.{{ $item['id'] }}" type="text" autocomplete="off" style="resize: none"
                                        x-show="answer{{ $item['id'] }}" x-on:blur="answer{{ $item['id'] }} = false"
                                        wire:blur="editAnswer({{ $item['id'] }})" @keyup.escape="answer{{ $item['id'] }} = false"></textarea>
                                </td>
                                <td class="px-3 py-1 text-center">
                                    <span wire:click="editNeedLogin({{ $item['id'] }})"
                                        class="px-1 rounded-full shadow-sm shadow-gray-500 {{ $item['need-login'] === 'yes' ? 'text-green-800 bg-green-400 border border-green-800' : 'text-blue-800 bg-blue-400 border border-blue-800' }}">
                                        {!! $item['need-login'] !!}
                                    </span>
                                </td>
                                <td class="px-3 py-1 text-center gap-1">
                                    <span
                                        class="px-1 rounded-full text-red-800 bg-red-200 shadow-sm shadow-gray-500 border border-red-500 hover:bg-red-300"
                                        wire:click="showDeleteConfirmation({{ $item['id'] }})"
                                        style="font-size: 10px; padding-top: 2px; padding-bottom: 2px;">
                                        {{ __('DELETE') }}
                                    </span>
                                    <span
                                        class="px-1 ml-1 rounded-full text-green-800 bg-green-200 shadow-sm shadow-gray-500 border border-green-500 hover:bg-green-300"
                                        wire:click="duplicateEntry({{ $item['id'] }})"
                                        style="font-size: 10px; padding-top: 2px; padding-bottom: 2px;">
                                        {{ __('DUPLICATE') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <x-confirmation-modal wire:key="{{ $item['id'] }}" wire:model.live="showingDeleteConfirmation" maxWidth="sm">
        @slot('title')
            <span>
                {{ __('Delete Entry') }}
            </span>
        @endslot
        @slot('content')
            <div>
                {{ __('Are you sure to want to delete entry?') }}
            </div>
        @endslot
        @slot('footer')
            <x-secondary-button type="button" wire:click="deleteEntry({{ $item['id'] - 1 }})"
                wire:loading.attr="disabled">
                {{ __('Yes') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="$toggle('showingDeleteConfirmation')" wire:loading.attr="disabled">
                {{ __('No') }}
            </x-danger-button>
        @endslot
    </x-confirmation-modal>
</div>
