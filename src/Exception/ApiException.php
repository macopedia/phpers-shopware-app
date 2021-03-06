<?php

declare(strict_types=1);

namespace App\Exception;

use Psr\Http\Message\ResponseInterface;

class ApiException extends \Exception
{
    private string $requestPath;
    private string $shopUrl;
    private ResponseInterface $response;

    public function __construct(string $shopUrl, string $requestPath, ResponseInterface $response)
    {
        $this->requestPath = $requestPath;
        $this->shopUrl = $shopUrl;
        $this->response = $response;

        $message = sprintf('Error occurred while requesting %s from shop %s, got status %s and response was %s', $requestPath, $shopUrl, $response->getStatusCode(), $response->getBody()->getContents());

        parent::__construct($message, 0, null);
    }

    public function getRequestPath(): string
    {
        return $this->requestPath;
    }

    public function getShopUrl(): string
    {
        return $this->shopUrl;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
