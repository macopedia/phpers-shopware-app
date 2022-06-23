<?php

namespace App\Controller\Shop;

use App\Repository\CurrencyRepository;
use App\Repository\ShopRepository;
use App\Services\CurrencyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\AbstractType;

#[Route('/shop')]
class CurrenciesController extends AbstractController
{
    public function __construct(
        private CurrencyRepository $currencyRepository
    ) {
    }

    #[Route('/currencies-list', name: 'currencies_list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $currencies = $this->currencyRepository->findByShopId($this->getUser()->getShopId());

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
}