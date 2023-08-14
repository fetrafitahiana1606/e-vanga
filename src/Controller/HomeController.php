<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Service\Cart\CartService;
use App\Service\UserInfoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(ProduitRepository $produitRepository,UserInfoService $userInfoService, CartService $cartService): Response
    {   


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $userInfoService->getUserInfo(),
            'produits' => $produitRepository->findAll(),
            'items' => $cartService->getCart(),
            'total' => $cartService->getTotal(),
        ]);
    }

}
