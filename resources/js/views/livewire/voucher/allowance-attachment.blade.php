<div class="w-full">
    <style>
        @media print {
            @page {
                size: 105mm 148mm;
                margin: 0;
            }
            body, html {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                overflow: hidden; /* Prevent extra pages from scroll */
            }
            .no-print, header, nav, .banner, .min-h-screen > nav, footer {
                display: none !important;
            }
            /* Reset layout wrappers to avoid taking space */
            .min-h-screen, main, div.py-12 {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                min-height: auto !important;
                height: auto !important;
            }

            .to-print {
                display: block !important;
                position: absolute;
                top: 0;
                left: 0;
                width: 105mm; /* A6 Portrait width */
                height: 148mm; /* A6 Portrait height */
                margin: 0 !important;
                padding: 10mm !important; /* Add some padding for the content itself */
                box-sizing: border-box;
                background: white;
                z-index: 9999;
                overflow: hidden;
            }
        }
        .to-print {
             /* Visible for preview but can be hidden or styled differently as needed */
        }
    </style>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="no-print p-4 bg-gray-100 mb-4 rounded shadow">
            <h2 class="text-lg font-bold mb-4">Allowance Disbursement Attachment Generator</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date of Disbursement</label>
                    <input type="date" wire:model.live="date_of_disbursement" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Print Attachment
                    </button>
                </div>
            </div>

            <div class="space-y-2">
                @foreach($rows as $index => $row)
                    <div class="flex gap-2 items-start">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500">Name</label>
                            <input type="text" wire:model.live.debounce.250ms="rows.{{ $index }}.name" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs">
                        </div>
                        <div class="flex-1">
                             <label class="block text-xs font-medium text-gray-500">Designation</label>
                            <select wire:model.live="rows.{{ $index }}.designation" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs">
                                <option value="">Select Role</option>
                                @foreach($availableRoles as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-24">
                             <label class="block text-xs font-medium text-gray-500">Amount</label>
                            <input type="number" step="0.01" wire:model.live.debounce.250ms="rows.{{ $index }}.amount" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs">
                        </div>
                        <div class="flex items-end pb-1">
                            <button type="button" wire:click="removeRow({{ $index }})" class="text-red-600 hover:text-red-900">
                                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-4">
                 <button type="button" wire:click="addRow" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Person
                </button>
            </div>
            <div class="mt-2 text-right font-bold text-sm">
                Total: {{ number_format($grandTotal, 2) }}
            </div>
        </div>
    </div>

    {{-- Printable Area --}}
    <div class="to-print bg-white p-4 text-xs font-sans text-black hidden print:block mx-auto border border-gray-200" id="print-container">
        <div id="print-content" style="transform-origin: top left; width: 100%;">
            <div class="text-center mb-4">
                <h1 class="font-bold uppercase text-sm">Allowance Disbursement</h1>
                <p class="text-[10px]">Date: {{ \Carbon\Carbon::parse($date_of_disbursement)->format('F d, Y') }}</p>
            </div>

            <table class="w-full border-collapse border border-black text-[10px]">
                <thead>
                    <tr>
                        <th class="border border-black px-1 py-1 text-left w-1/2">Name</th>
                        <th class="border border-black px-1 py-1 text-center w-1/4">Designation</th>
                        <th class="border border-black px-1 py-1 text-right w-1/4">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                    <tr>
                        <td class="border border-black px-1 py-1">{{ strtoupper($row['name']) }}</td>
                        <td class="border border-black px-1 py-1 text-center">{{ strtoupper($row['designation']) }}</td>
                        <td class="border border-black px-1 py-1 text-right">{{ number_format((float)$row['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                 <tfoot>
                    <tr>
                        <td colspan="2" class="border border-black px-1 py-1 text-right font-bold">Total</td>
                        <td class="border border-black px-1 py-1 text-right font-bold">{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
             <div class="mt-8 text-[10px]">
                <p>Prepared by:</p>
                <div class="mt-6 border-b border-black w-1/2"></div>
             </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        const resizeContent = () => {
             const container = document.getElementById('print-container');
             const content = document.getElementById('print-content');
             
             if (!container || !content) return;
             
             // Reset scale first to get true dimensions
             content.style.transform = 'scale(1)';
             content.style.width = '100%';

             // Dimensions (A6 Portrait: 105mm x 148mm)
             // We can use the container's computed size since CSS sets it.
             const containerHeight = container.clientHeight;
             const contentHeight = content.scrollHeight;
             
             // Add a small buffer for padding if needed, but the container has p-4 (approx 16px or 4mm)
             // The containerHeight includes padding if box-sizing is border-box.
             // We want to ensure content fits inside the content box of the container.
             // Actually, the container has 'box-sizing: border-box' and 'padding: 10mm'.
             // So available height is containerHeight - padding.
             // Let's just compare scrollHeight vs clientHeight. 
             // If content overflows, clientHeight will be smaller than scrollHeight (if overflow visible/hidden)
             // Wait, if overflow is hidden, clientHeight is the visible height.
             
             // Let's explicitly compute available height based on CSS rules we set (height: 105mm, padding: 10mm).
             // 105mm - 20mm = 85mm available height.
             
             // Safest way: check if contentHeight > containerHeight (since container is fixed).
             // Ideally we want to prevent any clipping.
             
             if (contentHeight > containerHeight) {
                 const scale = containerHeight / contentHeight;
                 content.style.transform = `scale(${scale})`;
             }
        };

        // Run on load
        resizeContent();

        // Run when Livewire updates the DOM
        Livewire.hook('morph.updated', ({ el, component }) => {
            resizeContent();
        });
        
        // Also bind to window resize/print just in case
        window.addEventListener('resize', resizeContent);
        window.onbeforeprint = resizeContent;
    });
</script>
