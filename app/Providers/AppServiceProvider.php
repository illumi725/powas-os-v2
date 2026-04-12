<?php

namespace App\Providers;

use App\Models\Powas;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Livewire\Component;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Builder::macro('search', function ($field, $string) {
        //     return $string ? $this->where($field, 'like', '%' . $string . '%') : $this;
        // });

        // Validator::extend('unique_powas', function ($attribute, $value, $parameters, $validator) {
        //     $region = $parameters[0];
        //     $province = $parameters[1];
        //     $municipality = $parameters[2];
        //     $barangay = $parameters[3];
        //     $phase = $parameters[4];

        //     return !Powas::isExists(region: $region, province: $province, municipality: $municipality, barangay: $barangay, phase: $phase);
        // });

        // Validator::replacer('unique_powas', function ($message, $attribute, $rule, $parameters) {
        //     return str_replace(':attribute', $parameters[3], ':attribute POWAS ' . $parameters[4] . ' already exists!');
        // });
        app()->singleton('getGoogleToken', function () {
            $client_id = \Config('services.google.client_id');
            $client_secret = \Config('services.google.client_secret');
            $refresh_token = \Config('services.google.refresh_token');
            $url = 'https://oauth2.googleapis.com/token';

            $response = Http::post($url, [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token',
            ]);

            $accessToken = json_decode((string)$response->getBody(), true)['access_token'];

            return $accessToken;
        });
    }
}
