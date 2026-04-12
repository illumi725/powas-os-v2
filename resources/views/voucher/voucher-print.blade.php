<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    {{-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> --}}
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'POWAS-OS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" /> --}}
    <link rel="icon" href="{{ asset('assets/powas.ico') }}" type="image/x-icon">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    <style>
        @media print {

            .no-print {
                display: none;
            }

            .page-break {
                page-break-after: always;
            }

            .to-print {
                width: 8.5in;
                height: 5.5in;
                page-break-inside: avoid;
            }
        }

        .segoeUI {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .grid-cols-16 {
            grid-template-columns: repeat(16, minmax(0, 1fr));
        }

        .grid-cols-15 {
            grid-template-columns: repeat(15, minmax(0, 1fr));
        }

        .col-span-13 {
            grid-column: span 13 / span 13;
        }

        .col-span-14 {
            grid-column: span 14 / span 14;
        }

        .col-span-15 {
            grid-column: span 15 / span 15;
        }

        .col-span-16 {
            grid-column: span 16 / span 16;
        }
    </style>
</head>

<body>
    <div>
        @livewire('voucher.voucher-print', ['powasID' => $powasID, 'powas' => $powas, 'voucherID' => $voucherID])
    </div>

    @livewireScripts
</body>

</html>
