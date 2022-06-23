<?php

namespace App\Repository;

use App\Entity\Shop;
use Vin\ShopwareSdk\Client\AdminAuthenticator;
use Vin\ShopwareSdk\Client\GrantType\ClientCredentialsGrantType;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Entity\Currency\CurrencyDefinition;
use Vin\ShopwareSdk\Exception\AuthorizationFailedException;
use Vin\ShopwareSdk\Factory\RepositoryFactory;

class ShopwareCurrencyRepository
{
    /**
     * @throws AuthorizationFailedException
     */
    public function getCurrenciesList(Shop $shop): array
    {
        $repositoryFactory = RepositoryFactory::create(CurrencyDefinition::ENTITY_NAME);

        $searchResults = $repositoryFactory->search(new Criteria(), $this->getContext($shop));

        return $searchResults->getEntities()->getElements();
    }

    public function updateCurrency(array $data, Shop $shop)
    {
        $repositoryFactory = RepositoryFactory::create(CurrencyDefinition::ENTITY_NAME);

        $repositoryFactory->update($data, $this->getContext($shop));
    }

    private function authenticate(string $apiKey, string $secretKey, string $shopUrl): AdminAuthenticator
    {
        $grantType = new ClientCredentialsGrantType($apiKey, $secretKey);

        return new AdminAuthenticator($grantType, $shopUrl);
    }

    /**
     * @throws AuthorizationFailedException
     */
    public function getContext(Shop $shop): ?Context
    {
        if (empty($shop)) {
            return null;
        }

        $adminAuthenticate = $this->authenticate(
            $shop->getApiKey(),
            $shop->getSecretKey(),
            $shop->getShopUrl()
        );

        return new Context($shop->getShopUrl(), $adminAuthenticate->fetchAccessToken());
    }
}