<?php

declare(strict_types=1);

namespace App\Services\AppLifecycle\ArgumentResolver;

use App\Repository\ShopRepository;
use App\Services\AppLifecycle\Event;
use App\Services\Authenticator;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class EventResolver implements ArgumentValueResolverInterface
{
    public function __construct(private ShopRepository $shopRepository)
    {
    }

    /**
     * @throws Exception
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if (Event::class !== $argument->getType()) {
            return false;
        }

        if ('POST' !== $request->getMethod()) {
            return false;
        }

        $requestContent = json_decode($request->getContent(), true);

        if (!$requestContent) {
            return false;
        }

        $hasSource = array_key_exists('source', $requestContent);
        $hasData = array_key_exists('data', $requestContent);
        $hasSourceAndData = $hasSource && $hasData;

        if (!$hasSourceAndData) {
            return false;
        }

        $requiredKeys = ['url', 'appVersion', 'shopId'];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $requestContent['source'])) {
                return false;
            }
        }

        $shopSecret = $this->shopRepository->getSecretByShopId($requestContent['source']['shopId']);

        return Authenticator::authenticatePostRequest($request, $shopSecret);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $requestContent = json_decode($request->getContent(), true);

        $shopUrl = $requestContent['source']['url'];
        $shopId = $requestContent['source']['shopId'];
        $appVersion = (int) $requestContent['source']['appVersion'];
        $eventData = $requestContent['data'];

        yield new Event($shopUrl, $shopId, $appVersion, $eventData);
    }
}
