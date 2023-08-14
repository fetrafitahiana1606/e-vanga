<?php

namespace App\Service\Order;

use App\Entity\Commande;
use App\Entity\ProduitCommande;
use App\Service\UserInfoService;
use Doctrine\ORM\EntityManagerInterface;

class OrderManager
{
    public function __construct(private EntityManagerInterface $entityManager, private UserInfoService $userInfoService){
    
    
    }
    public function validateOrder(array $cartItems): bool
    {
        // Vérifier si les quantités commandées sont disponibles en stock
        foreach ($cartItems as $cartItem) {
            $produit = $cartItem['produit'];
            $quantiteCommandee = $cartItem['quantite'];  
            if ($produit->getStock() < $quantiteCommandee) {
                // Le stock est insuffisant pour le produit
                return false;
            }
        }
    return true;
    }

    public function createOrder(array $cartItems, int $total): Commande
    {
        
        $commande = new Commande();
        $commande->setUser($this->userInfoService->getUserInfo());
        $commande->setTotal($total);

        foreach ($cartItems as $cartItem) {
            $produit = $cartItem['produit'];
            $quantiteCommandee = $cartItem['quantite'];

            $produitCommande = new ProduitCommande();
            $produitCommande->setCommande($commande);
            $produitCommande->setProduit($produit);
            $produitCommande->setQuantite($quantiteCommandee);

            // Soustraire la quantité commandée du stock du produit
            $nouveauStock = $produit->getStock() - $quantiteCommandee;
            $produit->setStock($nouveauStock);

            $this->entityManager->persist($produitCommande);
        }

        $this->entityManager->persist($commande);
        $this->entityManager->flush();
        return $commande;
    }

}