<?php


namespace App\Service\Cart;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;


class CartService
{
    protected $session;

    public function __construct(private RequestStack $requestStack, private ProduitRepository $produitRepository){
    
    }
    
    public function add(int $id){
        $session = $this->requestStack->getSession();
        $panier = $session->get('panier');

        if(!empty($panier[$id])) {
            $panier[$id]++;
        
        } else {
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);
        $this->addFlash(
            'notice',
            'Projout ajouter a votre panier'
        );
    }

    public function remove(int $id){
        $session = $this->requestStack->getSession();
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
           unset($panier[$id]);
        }

        $session->set('panier', $panier);
        $this->addFlash(
            'notice',
            'Projout supprimer de votre panier'
        );
    }

    public function getCart(): array
    {
        $session = $this->requestStack->getSession();

        $panier = $session->get('panier', []);

        $panierWithData = [];

        foreach ($panier as $id => $quantite) {
            $panierWithData[] = [
                    'produit' => $this->produitRepository->find($id),
                    'quantite' => $quantite,
            ];
        }
        return $panierWithData;
    }

    public function getTotal(): float
    {
        $total = 0;
        $panierWithData = $this->getCart();


        foreach ($panierWithData as $item) {
            $totalItem = $item['produit']->getPrix() * $item['quantite'];
            $total += $totalItem;
        }
        return $total;
    }

    public function clearCart()
    {
        $session = $this->requestStack->getSession();
        $session->remove('panier');
    }
    
}
