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
                height: 11in;
                page-break-inside: avoid;
            }
        }

        .to-print {
            width: 8.5in;
            height: 11in;
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <div>
        @livewire('accounting.fis', ['powasID' => $powasID, 'powas' => $powas, 'transactionMonth' => $transactionMonth])
    </div>

    @livewireScripts
</body>

</html>
