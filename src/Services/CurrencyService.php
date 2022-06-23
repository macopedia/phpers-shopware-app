<?php

namespace App\Services;

class CurrencyService
{
    const CURRENCIES_API_URL = 'https://api.exchangerate.host/latest';

    public function getCurrenciesDataFromApi()
    {
        $responseJson = file_get_contents(self::CURRENCIES_API_URL);
        if (false !== $responseJson) {
            $response = json_decode($responseJson);
            if ($response->success === true) {
                return $response;
            }
        }

        return [];
    }
}