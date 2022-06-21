<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ShopRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ShopRepository::class)]
#[ORM\Table(name: '`shop`')]
class Shop implements UserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $shopId;

    #[ORM\Column(type: 'string')]
    private string $shopUrl;

    #[ORM\Column(type: 'string')]
    private string $shopSecret;

    #[ORM\Column(type: 'string', nullable : true)]
    private ?string $apiKey;

    #[ORM\Column(type: 'string', nullable : true)]
    private ?string $secretKey;

    public function getShopId(): string
    {
        return $this->shopId;
    }

    public function setShopId(string $shopId): self
    {
        $this->shopId = $shopId;

        return $this;
    }

    public function getShopUrl(): string
    {
        return $this->shopUrl;
    }

    public function setShopUrl(string $shopUrl): self
    {
        $this->shopUrl = $shopUrl;

        return $this;
    }

    public function getShopSecret(): string
    {
        return $this->shopSecret;
    }

    public function setShopSecret(string $shopSecret): self
    {
        $this->shopSecret = $shopSecret;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }

    public function setSecretKey(string $secretKey): self
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles[] = 'ROLE_SHOP';

        return array_unique($roles);
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->shopId;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
