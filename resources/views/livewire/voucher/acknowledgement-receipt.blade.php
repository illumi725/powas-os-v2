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
                overflow: hidden;
            }
            .no-print, header, nav, .banner, .min-h-screen > nav, footer {
                display: none !important;
            }
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
                width: 105mm;
                height: 148mm;
                margin: 0 !important;
                padding: 10mm !important;
                box-sizing: border-box;
                background: white;
                z-index: 9999;
                overflow: hidden;
            }
        }
    </style>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="no-print p-4 bg-gray-100 mb-4 rounded shadow">
            <h2 class="text-lg font-bold my-4">Acknowledgement Receipt Generator</h2>
            
            <div class="grid grid-cols-1 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" wire:model.live="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Recipient Name (Paid To)</label>
                    <input type="text" wire:model.live.debounce.250ms="recipient" placeholder="e.g. Geffrey A. De Jesus" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount</label>
                    <input type="number" step="0.01" wire:model.live.debounce.250ms="amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Purpose (Bilang kabayaran para sa...)</label>
                    <textarea wire:model.live.debounce.250ms="purpose" rows="3" placeholder="e.g. installation ng inverter para sa solar power system" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Print Receipt
                </button>
            </div>
        </div>
    </div>

    {{-- Printable Area --}}
    <div class="to-print bg-white p-4 font-sans text-black hidden print:block mx-auto" id="print-container">
        <div id="print-content" style="transform-origin: top left; width: 100%; display: flex; flex-direction: column; height: 100%;">
            
            <div class="mb-8 font-bold text-xs">
                {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
            </div>

            <div class="text-center mb-8">
                <h1 class="font-bold text-sm text-blue-900 uppercase">Acknowledgement of Payment Received</h1>
            </div>

            <div class="text-xs leading-relaxed text-justify flex-grow">
                <p>
                    Ako si <span class="font-bold">{{ $recipient ?? '____________________' }}</span>, ay nagpapatunay na natanggap ko ang halagang <span class="font-bold">&#8369;{{ number_format((float)$amount, 2) }}</span> (<span class="font-bold">{{ $amountInWords ?? 'Zero Pesos Only' }}</span>) mula sa Pinili POWAS Phase IV, bilang kabayaran para sa {{ $purpose }}.
                </p>
            </div>

            <div class="mt-12 text-right">
                <div class="inline-block text-center">
                    <p class="font-bold text-xs">{{ $recipient ?? 'Signature over Printed Name' }}</p>
                    {{-- <div class="border-t border-black mt-1 w-full"></div> --}}
                </div>
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
             
             content.style.transform = 'scale(1)';
             content.style.width = '100%';

             const containerHeight = container.clientHeight;
             const contentHeight = content.scrollHeight;
             
             if (contentHeight > containerHeight) {
                 const scale = containerHeight / contentHeight;
                 content.style.transform = `scale(${scale})`;
             }
        };

        resizeContent();

        Livewire.hook('morph.updated', ({ el, component }) => {
            resizeContent();
        });
        
        window.addEventListener('resize', resizeContent);
        window.onbeforeprint = resizeContent;
    });
</script>
