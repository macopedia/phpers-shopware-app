<?php

namespace App\Services;

use App\Entity\Currency;
use App\Entity\Shop;
use App\Repository\CurrencyRepository;

class CurrencyService
{
    public function __construct(
        private CurrencyRepository $currencyRepository
    ) {
    }

    const CURRENCIES_API_URL = 'https://api.exchangerate.host/latest';

    public function importCurrencies(Shop $shop): void
    {
        $currencies = $this->getCurrenciesDataFromApi();

        foreach ($currencies['rates'] as $code => $rate) {
            $currency = $this->currencyRepository->findOneBy(
                [
                    'shop' => $shop->getShopId(),
                    'code' => $code
                ]
            );

            if (empty($currency)) {
                $currency = new Currency();
            }
            $currency->setShop($shop);
            $currency->setCode($code);
            $currency->setRate($rate);
            $this->currencyRepository->add($currency, true);
        }
    }

    public function getCurrenciesDataFromApi(): array
    {
        $responseJson = file_get_contents(self::CURRENCIES_API_URL);
        if (false !== $responseJson) {
            $response = json_decode(json: $responseJson, flags: JSON_OBJECT_AS_ARRAY);
            if ($response['success'] === true) {
                return $response;
            }
        }

        return [];
    }
}