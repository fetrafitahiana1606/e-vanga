<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Entity\ProduitCommande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Service\Cart\CartService;
use App\Service\Order\OrderManager;
use App\Service\UserInfoService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')] // Restriction d'accès admin seulement
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')] // Restriction d'accès admin seulement
    public function new(Request $request, OrderManager $orderManager, CartService $cartService)
    {
        $cartItems = $cartService->getCart();
        $total = $cartService->getTotal();

        // Vérifier si les quantités commandées sont disponibles en stock
        $orderManager->validateOrder($cartItems);

        // Récupérer l'ID de la dernière commande enregistrée
        $commande = $orderManager->createOrder($cartItems,$total);

        // Effacer le contenu du panier après la validation de la commande
        $cartService->clearCart();

        // Rediriger vers la page de la commande en utilisant l'ID de la dernière commande enregistrée
        return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
    }



    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')] // Restriction d'accès admin seulement
    public function show(Commande $commande, UserInfoService $userInfoService, CartService $cartService): Response
    {

        $commande->getProduitCommande()->initialize(); // Charge les entités associées
        $userCommande=$commande->getUser();
        // dd($userCommande);
        $produitCommande = $commande->getProduitCommande();
        

        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
            'produitCommande'  =>  $produitCommande,
            'userCommande'  => $userCommande,
            'user' => $userInfoService->getUserInfo(),
            'items' => $cartService->getCart(),
            'total' => $cartService->getTotal(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Restriction d'accès admin seulement
    public function edit(Request $request, Commande $commande, CommandeRepository $commandeRepository, UserInfoService $userInfoService): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandeRepository->add($commande, true);

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
            'user' => $userInfoService->getUserInfo(),
        ]);
    }

    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')] // Restriction d'accès admin seulement
    public function delete(Request $request, Commande $commande, CommandeRepository $commandeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            $commandeRepository->remove($commande, true);
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}