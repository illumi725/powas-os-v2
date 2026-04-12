<div class="w-full">
    <div class="no-print sticky top-0 bg-black text-white py-4">
        <div class="mx-auto text-center">
            <x-button type="button" onclick="window.print()" title="Print" wire:loading.attr="disabled">
                {{ __('Print') }}
            </x-button>
        </div>
    </div>
    <div>
        <div class="mx-auto jetbrains" style="width: 215.9mm;">
            @php
                $ctr = 1;
            @endphp

            <div class="text-center">
                <span class="font-bold text-xl block">{{ $powas->barangay . ' POWAS ' . $powas->phase }}</span>
                <span class="font-bold text-xs block">
                    {{ $powas->zone . ', ' . $powas->barangay . ', ' . $powas->municipality . ', ' . $powas->province }}
                </span>
            </div>

            <div class="text-center">
                <span class="font-bold text-lg block my-2">
                    {{ 'Reading Sheet for the Month of ' . $billingMonth }}
                </span>
            </div>

            <table class="border border-gray-600 table-auto w-full">
                <thead class="bg-green-600 text-white">
                    <tr>
                        <th class="border border-gray-600 px-2">
                            {{ __('SL #') }}
                        </th>
                        {{-- <th class="border px-2">
                            {{ __('QR Code') }}
                        </th> --}}
                        <th class="border border-gray-600 px-2">
                            {{ __('Name') }}
                        </th>
                        <th class="border border-gray-600 px-2 whitespace-nowrap">
                            {{ __('Meter Number') }}
                        </th>
                        <th class="border border-gray-600 px-2">
                            {{ __('Previous Reading') }}
                        </th>
                        <th class="border border-gray-600 px-2">
                            {{ __('Present Reading') }}
                        </th>
                        <th class="border border-gray-600 px-2">
                            {{ __('Reading Date') }}
                        </th>
                        <th class="border border-gray-600 px-2 whitespace-nowrap">
                            {{ __('Remarks') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($readingInfos as $readingInfo)
                        <tr class="border border-gray-600 odd:bg-gray-50 even:bg-gray-100">
                            <th class="border border-gray-600">
                                {{ $ctr++ }}
                            </th>
                            <td class="border border-gray-600 whitespace-nowrap pl-2 pr-2">
                                {{ $readingInfo['member_name'] }}
                            </td>
                            <td class="border border-gray-600 whitespace-nowrap pl-2 pr-2">
                                {{ $readingInfo['meter_number'] }}
                            </td>
                            <td class="border border-gray-600 whitespace-nowrap pl-2 pr-2 text-right">
                                {{ number_format($readingInfo['previous_reading'], 2) }}
                            </td>
                            <td class="border border-gray-600 whitespace-nowrap pl-2 pr-2 text-right">
                                @if ($readingInfo['present_reading'] != '')
                                    {{ number_format($readingInfo['present_reading'], 2) }}
                                @else
                                    {{ $readingInfo['present_reading'] }}
                                @endif
                            </td>
                            <td class="border border-gray-600 whitespace-nowrap pl-2 pr-2 text-right">
                                {{ $readingInfo['reading_date'] }}
                            </td>
                            <td class="border border-gray-600 whitespace-nowrap pl-2 pr-2 text-right">

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="w-full grid grid-cols-3 text-sm px-2 mt-12 pb-2 gap-4">
                <div>
                    <div class="text-left font-bold">
                        {{ __('Checked by:') }}
                    </div>
                    <div class="text-center w-full border-b border-black h-12">

                    </div>
                    <div class="text-center">
                        {{ __('President') }}
                    </div>
                </div>
                <div>
                    <div class="text-left font-bold">
                        {{ __('Read by:') }}
                    </div>
                    <div class="text-center w-full border-b border-black h-12">

                    </div>
                    <div class="text-center">
                        {{ __('Meter Reader') }}
                    </div>
                </div>
                <div>
                    <div class="text-left font-bold">
                        {{ __('Received/Verified by:') }}
                    </div>
                    <div class="text-center w-full border-b border-black h-12">

                    </div>
                    <div class="text-center">
                        {{ __('Treasurer') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
