<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    {{-- <link rel="stylesheet" href="{{ asset('storage/css/pdf.css') }}"> --}}
    @livewireStyles
</head>
<body style="font-family: 'Arial', sans-serif;">
    <div style="margin-left: auto; margin-right: auto;">
        <div style="display: grid; grid-template-columns: repeat(1, minmax(0, 1fr));">
            <div style="background-color: #fff; color: #333;">
                <span>{{ __('REFERENCE NUMBER: ') }}</span>
                <div style="text-align: center; margin-top: 0.75rem;">
                    <span style="font-size: 1.5rem; font-weight: 700;">{{ __('APLIKASYON SA PAGSAPI') }}</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));">
                    <div style="grid-column: span 1; margin-top: 1rem;">
                        <p style="font-weight: 700">{{ __('PETSA:') }}</p>
                    </div>

                    <div class="col-span-2 mt-4" style="grid-column: span 2; margin-top: 1rem;">
                        <p>{{ __('PETSA') }}</p>
                    </div>

                    <div style="grid-column: span 3; text-align: center; margin-top: 1rem;">
                        <span style="font-style: italic; font-size: 1.125rem; text-decoration: underline;">{{ __('MGA PAGKAKAKILANLAN') }}</span>
                    </div>

                    <div style="grid-column: span 1; margin-top: 0rem;">
                        <p style="font-weight: 700">{{ __('PANGALAN:') }}</p>
                    </div>

                    <div class="col-span-2 mt-4" style="grid-column: span 2; margin-top: 0rem;">
                        <p>{{ __('PANGALAN') }}</p>
                    </div>

                    <div style="grid-column: span 1; margin-top: 0rem;">
                        <p style="font-weight: 700">{{ __('EDAD:') }}</p>
                    </div>

                    <div class="col-span-2 mt-4" style="grid-column: span 2; margin-top: 0rem;">
                        <p>{{ __('EDAD') }}</p>
                    </div>

                    <div style="grid-column: span 1; margin-top: 0rem;">
                        <p style="font-weight: 700">{{ __('ARAW NG KAPANGANAKAN:') }}</p>
                    </div>

                    <div class="col-span-2 mt-4" style="grid-column: span 2; margin-top: 0rem;">
                        <p>{{ __('ARAW NG KAPANGANAKAN') }}</p>
                    </div>

                    <div style="grid-column: span 1; margin-top: 0rem;">
                        <p style="font-weight: 700">{{ __('LUGAR NG KAPANGANAKAN:') }}</p>
                    </div>

                    <div class="col-span-2 mt-4" style="grid-column: span 2; margin-top: 0rem;">
                        <p>{{ __('LUGAR NG KAPANGANAKAN') }}</p>
                    </div>

                    <div style="grid-column: span 1; margin-top: 0rem;">
                        <p style="font-weight: 700">{{ __('TIRAHAN:') }}</p>
                    </div>

                    <div class="col-span-2 mt-4" style="grid-column: span 2; margin-top: 0rem;">
                        <p>{{ __('TIRAHAN') }}</p>
                    </div>

                    <div style="grid-column: span 1; margin-top: 0rem;">
                        <p style="font-weight: 700">{{ __('KATAYUANG SIBIL:') }}</p>
                    </div>

                    <div class="col-span-2 mt-4" style="grid-column: span 2; margin-top: 0rem;">
                        <p>{{ __('KATAYUANG SIBIL') }}</p>
                    </div>

                    <div style="grid-column: span 1; margin-top: 0rem;">
                        <p style="font-weight: 700">{{ __('BILANG NG KASAMBAHAY:') }}</p>
                    </div>

                    <div class="col-span-2 mt-4" style="grid-column: span 2; margin-top: 0rem;">
                        <p>{{ __('BILANG NG KASAMBAHAY') }}</p>
                    </div>
                </div>
                <div style="text-align: justify; margin-top: 2rem;">
                    <p>
                        {{ __('Magalang ko pong hinihiling na ako po ay tanggapin bilang kasapi ng ') }}<span class="underline">BARANGAY POWAS PHASE</span>{{ __('.')}}
                    </p>
                    <p class="mt-2" style="margin-top: 0.75rem;">
                        {{ __('Ako po ay nakahandang magbayad ng halagang nararapat para sa naturang kahilingan.') }}
                    </p>
                    <p class="mt-2" style="margin-top: 0.75rem;">
                        {{ __('Buong puso ko pong tutuparin ang lahat ng mga patakaran at alituntunin ng') }} <span class="underline">BARANGAY POWAS PHASE</span>{{ __('.') }}
                    </p>
                    <p class="mt-2" style="margin-top: 0.75rem;">
                        {{ __('Ang anumang paglabag sa mga alituntunin ay magbibigay daan sa pamunuan na pansamantalang putulin ang serbisyo ng tubig o patawan ng karampatang kaparusahan ang sinumang lumabag sa itinakda ng mga may kapangyarihan.') }}
                    </p>
                </div>
                <div style="margin-top: 3rem; display: grid; grid-template-columns: repeat(11, minmax(0, 1fr));">
                    <div style="text-align: center; grid-column: span 6;">
                        <hr>
                        <span style="font-size: 0.75rem;">{{ __('Buong Pangalan at Pirma ng Aplikante')}}</span>
                    </div>
                </div>
                <div style="margin-top: 3rem; display: grid; grid-template-columns: repeat(11, minmax(0, 1fr));">
                    <div style="grid-column: span 5;">
                        <span style="text-transform: uppercase;">{{ __('Inererekomendang Sang-ayunan:')}}</span>
                    </div>
                    <div style="grid-column: span 1;"></div>
                    <div style="grid-column: span 5;">
                        <span style="text-transform: uppercase;">{{ __('Sinasang-ayunan:')}}</span>
                    </div>
                </div>
                <div class="grid grid-cols-11 mt-12" style="margin-top: 3rem; display: grid; grid-template-columns: repeat(11, minmax(0, 1fr));">
                    <div style="grid-column: span 5; text-align: center;">
                        <hr>
                        <span style="font-size: 0.75rem;">{{ __('Operations Manager')}}</span>
                    </div>
                    <div style="grid-column: span 1"></div>
                    <div style="grid-column: span 5; text-align: center;">
                        <hr>
                        <span style="font-size: 0.75rem;">{{ __('Chairman')}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>
