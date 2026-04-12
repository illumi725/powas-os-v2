<div x-data="{ show: false }">
    <img class="fixed bottom-4 right-4 p-1 cursor-pointer" src="{{ URL::asset('assets/poca.gif') }}" alt="POCA"
        width="100px" x-show="!show" x-on:click="show = ! show">

    <div class="w-full md:w-96 md:h-96 fixed bottom-4 md:right-4 bg-white border border-gray-300 rounded-lg shadow-sm shadow-gray-500"
        x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
        <div class="px-4 py-2 border-b border-gray-300 grid grid-cols-8">
            <div class="flex items-start text-lg font-semibold col-span-7">
                <img class="rounded-full inline mr-1" src="{{ URL::asset('assets/poca.gif') }}" alt=""
                    width="38px">
                POCA (POWAS Chat Assistant)
            </div>
            <div class="font-bold flex justify-end items-center w-full">
                <span x-on:click="show = ! show" class="cursor-pointer">
                    [X]
                </span>
            </div>
        </div>
        <div id="chatMessages" class="flex flex-col-reverse overflow-y-auto px-4 py-2" style="height: 295px;"
            wire:poll.500ms>

            @foreach (array_reverse($messages) as $message)
                <div class="flex justify-{{ $message['sender'] === 'POCA' ? 'start' : 'end' }} mb-6">
                    @if ($message['sender'] === 'POCA')
                        <div class="flex items-end justify-center font-bold uppercase mr-1"
                            style="padding-top: 1px; padding-bottom: 1px; padding-left: 2px; padding-right: 2px; font-size: 10px;">
                            <img {{-- class="rounded-full border border-slate-600 shadow-md shadow-gray-500" --}} src="{{ URL::asset('assets/poca.gif') }}" alt=""
                                width="48px" style="padding: 1.5px;">
                        </div>
                    @endif

                    <div>
                        <div
                            class="{{ $message['sender'] === 'POCA' ? 'bg-blue-800 text-blue-100 rounded-r-xl rounded-ss-xl' : 'bg-blue-100 text-blue-800 rounded-l-xl rounded-se-xl max-w-xs' }} px-2 py-2 max-w-xxs shadow shadow-gray-800">
                            <div class="inline">
                                {!! $message['message'] !!}
                            </div>
                        </div>

                        @if ($message['sender'] == 'POCA')
                            @if ($isResponseCorrect == 'OK')
                                <div class="mt-2 block" style="font-size: 10px">
                                    <div class="">
                                        <span class="uppercase font-mono font-bold">
                                            {{ __('Suggested inquiry:') }}
                                        </span>
                                    </div>
                                    <div class="text-center">
                                        @foreach ($suggestedQuery as $q)
                                            <div>
                                                <span wire:click="sendQuery('{{ $q }}')"
                                                    class="text-blue-800 cursor-pointer px-2 py-1 font-bold hover:underline">
                                                    {{ $q }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    @if ($message['sender'] !== 'POCA')
                        <div class="flex items-end justify-center font-bold uppercase ml-1"
                            style="padding-top: 1px; padding-bottom: 1px; padding-left: 2px; padding-right: 2px; font-size: 10px;">
                            <img class="rounded-full shadow shadow-gray-800"
                                src="{{ URL::asset('assets/chatuser.png') }}" alt="" width="32px"
                                style="padding: 1.5px;">
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <form wire:submit="sendMessage" class="flex">
            <input wire:model="userInput" type="text" placeholder="Type your message..."
                class="flex-1 px-4 py-2 border-t border-gray-300 focus:outline-none rounded-l-lg" autocomplete="off">
            <button type="button" wire:click="sendMessage" class="bg-blue-500 text-white px-4 py-2 rounded-r-lg">
                <i class="fa-regular fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>
