<?php

namespace App\Controller\Admin;

use App\Entity\ProduitCommande;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use App\Service\Cart\CartService;
use App\Service\UserInfoService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route("/admin/dashboard", name: "app_dashboard", methods: "GET")]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(UserRepository $userRepository,
                              CommandeRepository $commandeRepository,
                              ProduitRepository $produitRepository,
                              UserInfoService $userInfoService, CartService $cartService): Response
    {
        $nombreUtilisateurs = $userRepository->count([]);
        $nombreCommandes = $commandeRepository->count([]);
        $produitsEnStock = $produitRepository->sumStock();
        $produitsVendus = $commandeRepository->sumQuantiteProduits();
        $totalArgentOctroye = $commandeRepository->sumTotalCommandes();

        return $this->render('dashboard/dashboard.html.twig', [
            'nombreUtilisateurs' => $nombreUtilisateurs,
            'nombreCommandes' => $nombreCommandes,
            'nombreProduitsEnStock' => $produitsEnStock,
            'nombreProduitsVendus' => $produitsVendus,
            'totalArgentOctroye' => $totalArgentOctroye,
            'user' => $userInfoService->getUserInfo(),
            'items' => $cartService->getCart(),
        ]);

    }
}