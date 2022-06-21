<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ShopRepository;
use App\Services\Authenticator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function index(Request $request, ShopRepository $shopRepository, LoggerInterface $logger): Response
    {
        if (!Authenticator::authenticateRegisterRequest($request)) {
            return new Response(null, 401);
        }

        $shopUrl = $request->query->get('shop-url');
        $shopId = $request->query->get('shop-id');
        $name = $_SERVER['APP_NAME'];
        $secret = bin2hex(random_bytes(64));

        $shopRepository->createShop($this->getShopId($request), $this->getShopUrl($request), $secret);

        $proof = \hash_hmac('sha256', $shopId.$shopUrl.$name, $_SERVER['APP_SECRET']);

        $body = [
            'proof' => $proof,
            'secret' => $secret,
            'confirmation_url' => $this->generateUrl(
                'register_confirm',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ];

        return new JsonResponse($body);
    }

    #[Route('/register/confirm', name: 'register_confirm')]
    public function confirm(Request $request, ShopRepository $shopRepository, LoggerInterface $logger): Response
    {
        $requestContent = json_decode($request->getContent(), true);

        $shopSecret = $shopRepository->getSecretByShopId($requestContent['shopId']);

        $logger->info('request_content:'.var_export($request->getContent(), true));

        if (!Authenticator::authenticatePostRequest($request, $shopSecret)) {
            return new Response(null, 401);
        }

        $shopRepository->updateAccessKeysForShop(
            $requestContent['shopId'],
            $requestContent['apiKey'],
            $requestContent['secretKey']
        );

        return new Response();
    }

    private function getShopUrl(Request $request): string
    {
        return $request->query->get('shop-url');
    }

    private function getShopId(Request $request): string
    {
        return $request->query->get('shop-id');
    }
}
