<?php

namespace App\Controller\Shop;

use App\Repository\CurrencyRepository;
use App\Repository\ShopRepository;
use App\Services\CurrencyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shop')]
class CurrenciesController extends AbstractController
{
    #[Route('/currencies-list', name: 'currencies_list', methods: ['GET'])]
    public function list(CurrencyRepository $currencyRepository): Response
    {
        $currencies = $currencyRepository->findByShopId($this->getUser()->getShopId());

        return $this->renderForm('currencies/list.html.twig', [
            'currencies' => $currencies
        ]);
    }

    #[Route('/currencies-import', name: 'currencies_import', methods: ['GET'])]
    public function import(CurrencyService $currencyService, ShopRepository $shopRepository): RedirectResponse
    {
        $currencyService->importCurrencies($shopRepository->find($this->getUser()->getShopId()));

        return $this->redirectToRoute('currencies_list');
    }

    #[Route('/currencies-export', name: 'currencies_export', methods: ['GET'])]
    public function export(CurrencyService $currencyService, ShopRepository $shopRepository): RedirectResponse
    {
        $currencyService->exportCurrencies($shopRepository->find($this->getUser()->getShopId()));

        return $this->redirectToRoute('currencies_list');
    }
}