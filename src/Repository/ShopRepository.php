<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Shop;
use App\Services\Credentials;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shop[]    findAll()
 * @method Shop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopRepository extends ServiceEntityRepository
{
    public function __construct(private Connection $connection, ManagerRegistry $registry)
    {
        parent::__construct($registry, Shop::class);
    }

    /**
     * @throws Exception
     */
    public function getSecretByShopId(string $shopId): string
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('shop_secret')
            ->from('shop')
            ->where('shop_id = :shop_id')
            ->setParameter('shop_id', $shopId);
        $query = $queryBuilder->execute();

        $data = $query->fetch();

        return $data['shop_secret'];
    }

    /**
     * @throws Exception
     */
    public function updateAccessKeysForShop(string $shopId, string $apiKey, string $secretKey): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->update('shop')
            ->set('api_key', ':api_key')
            ->set('secret_key', ':secret_key')
            ->where('shop_id = :shop_id')
            ->setParameter('api_key', $apiKey)
            ->setParameter('secret_key', $secretKey)
            ->setParameter('shop_id', $shopId);
        $queryBuilder->execute();
    }

    /**
     * @throws Exception
     */
    public function createShop(string $shopId, string $shopUrl, string $shopSecret): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->insert('shop')
            ->setValue('shop_id', ':shop_id')
            ->setValue('shop_url', ':shop_url')
            ->setValue('shop_secret', ':shop_secret')
            ->setParameter('shop_id', $shopId)
            ->setParameter('shop_url', $shopUrl)
            ->setParameter('shop_secret', $shopSecret);
        $queryBuilder->execute();
    }

    /**
     * @throws Exception
     */
    public function removeShop(string $shopId): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->delete('shop')
            ->where('shop_id = :shop_id')
            ->setParameter('shop_id', $shopId);
        $queryBuilder->execute();
    }

    /**
     * @throws Exception
     */
    public function getCredentialsForShopId(string $shopId): Credentials
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('shop_url', 'api_key', 'secret_key')
            ->from('shop')
            ->where('shop_id = :shop_id')
            ->setParameter('shop_id', $shopId);
        $query = $queryBuilder->execute();

        $data = $query->fetch();

        return Credentials::fromKeys($data['shop_url'], $data['api_key'], $data['secret_key']);
    }
}
