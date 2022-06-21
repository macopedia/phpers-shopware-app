<?php declare(strict_types=1);

namespace App\Controller\Shop;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shop')]
class WelcomeController extends AbstractController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/welcome', name: 'welcome')]
    public function welcome(Request $request): Response
    {
        return $this->render('hello.html.twig', ['shopId' => $this->getUser()->getShopId()]);
    }
}