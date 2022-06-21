<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Shop;
use App\Repository\ShopRepository;
use App\Services\Authenticator;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ShopUserProvider implements UserProviderInterface
{
    public function __construct(private ShopRepository $shopRepository)
    {
    }

    /**
     * @throws UserNotFoundException if the user is not found
     * @throws Exception
     * @throws \Exception
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        list($shopId) = explode(':', $identifier);

        $request = Request::createFromGlobals();
        $secret = $this->shopRepository->getSecretByShopId($shopId);

        if (Authenticator::authenticateGetRequest($request, $secret)) {
            return $this->shopRepository->find($shopId);
        }

        throw new \Exception('User not found');
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof Shop) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return Shop::class === $class;
    }
}
