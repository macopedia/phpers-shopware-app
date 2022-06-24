<?php

declare(strict_types=1);

namespace App\Controller\AppLifecycle;

use App\Repository\ShopRepository;
use App\Services\AppLifecycle\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/applifecycle')]
class EventController extends AbstractController
{
    #[Route('/event/app_deleted', name: 'applifecycle_event_app_deleted')]
    public function appDeleted(
        Event $event,
        ShopRepository $shopRepository
    ): Response {
        $shopRepository->removeShop($event->getShopId());

        return new Response();
    }
}
