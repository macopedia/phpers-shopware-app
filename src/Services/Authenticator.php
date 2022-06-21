<?php

declare(strict_types=1);

namespace App\Services;

use App\Exception\AuthenticationException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Symfony\Component\HttpFoundation\Request;

class Authenticator
{
    /**
     * @throws GuzzleException
     * @throws AuthenticationException
     */
    public static function authenticate(Credentials $credentials, HandlerStack $handlerStack = null): Credentials
    {
        $shopUrl = $credentials->getShopUrl();
        $key = $credentials->getKey();
        $secretKey = $credentials->getSecretKey();

        $authClient = new HttpClient(['base_uri' => $shopUrl, 'handler' => $handlerStack]);

        $header = ['Content-Type' => 'application/json'];
        $authJson = json_encode([
            'grant_type' => 'client_credentials',
            'client_id' => $key,
            'client_secret' => $secretKey,
        ]);

        $auth = new GuzzleRequest('POST', '/api/oauth/token', $header, $authJson);

        try {
            $authResponse = $authClient->send($auth);
        } catch (RequestException $e) {
            throw new AuthenticationException($shopUrl, $key, 'Something went wrong. Cannot connect to the server.');
        }

        if (200 !== $authResponse->getStatusCode()) {
            throw new AuthenticationException($shopUrl, $key, $authResponse->getBody()->getContents());
        }

        $token = json_decode($authResponse->getBody()->getContents(), true)['access_token'];

        return $credentials->withToken($token);
    }

    public static function authenticateRegisterRequest(Request $request): bool
    {
        $signature = $request->headers->get('shopware-app-signature');
        $queryString = rawurldecode($request->getQueryString());

        $hmac = \hash_hmac('sha256', $queryString, $_SERVER['APP_SECRET']);

        return hash_equals($hmac, $signature);
    }

    public static function authenticatePostRequest(Request $request, string $shopSecret): bool
    {
        if (!array_key_exists('shopware-shop-signature', $request->headers->all())) {
            return false;
        }
        $signature = $request->headers->get('shopware-shop-signature');

        // $this->logger->info('authenticatePostRequest-shopware-shop-signature' . $signature);
        $hmac = \hash_hmac('sha256', $request->getContent(), $shopSecret);

        return hash_equals($hmac, $signature);
    }

    public static function authenticateGetRequest(Request $request, string $shopSecret): bool
    {
        $query = $request->query->all();

        $queryString = sprintf(
            'shop-id=%s&shop-url=%s&timestamp=%s&sw-version=%s&sw-context-language=%s&sw-user-language=%s',
            $query['shop-id'],
            $query['shop-url'],
            $query['timestamp'],
            $query['sw-version'],
            $query['sw-context-language'],
            $query['sw-user-language']
        );

        $hmac = \hash_hmac('sha256', $queryString, $shopSecret);

        return hash_equals($hmac, $query['shopware-shop-signature']);
    }
}
