<?php

namespace App\classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    private $requestStack;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager ,RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function add($id)
{
    $request = $this->requestStack->getCurrentRequest();
    $session = $request->getSession();
    
    $cart = $session->get('cart', []);

    if (!empty($cart[$id])) {
        $cart[$id]++;
    } else {
        $cart[$id] = 1;
    }

    $session->set('cart', $cart);
}

 
    public function get()
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();

        return $session->get('cart');
    }
 
    public function remove()
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();

        return $session->remove('cart');
    }

    public function delete($id)
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
    
        $cart = $session->get('cart', []);

        unset($cart[$id]);

        return $session->set('cart', $cart);
    }

    public function decrease($id)
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
    
        $cart = $session->get('cart', []);

        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }
        return $session->set('cart', $cart);
    }

    public function getFull()
    {
        $cartComplete = [];
        
        if ($this->get()) {
            foreach ($this->get() as $id => $quantity) {
                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);
                if (!$product_object) {
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }
        } 
        return $cartComplete;
    }

} 