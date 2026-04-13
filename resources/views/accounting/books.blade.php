<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Books of Accounts | {{ config('app.name', 'POWAS-OS') }}</title>
    <meta name="description" content="BIR CAS Compliant Books of Accounts - General Journal, Cash Receipts Journal, Cash Disbursements Journal, and General Ledger.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('assets/powas.ico') }}" type="image/x-icon">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="bg-gray-100 antialiased">
    <div>
        @livewire('accounting.books-of-accounts', ['powasID' => $powasID, 'powas' => $powas])
    </div>

    @livewireScripts
</body>

</html>
