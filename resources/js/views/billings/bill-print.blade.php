<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'POWAS-OS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" /> --}}
    <link rel="icon" href="{{ asset('assets/powas.ico') }}" type="image/x-icon">

    <!-- Styles -->
    @livewireStyles

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        hr {
            border: 1px dashed black;
        }

        .receipt-border {
            border: 1px solid black;
        }

        .powas-address {
            font-size: 10px;
        }

        .custom-border {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }

        @media print {

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    @livewire('billings.bill-printer', ['billingIDs' => $billingIDs])

    @livewireScripts
</body>

</html>
