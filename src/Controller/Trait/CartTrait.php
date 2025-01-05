<?php

namespace App\Controller\Trait;

use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;

trait CartTrait
{
    private function getCart(EntityManagerInterface $entityManager): Cart
    {
        $user = $this->getUser();
        $cart = $user->getCart();

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $entityManager->persist($cart);
            $entityManager->flush();
        }

        return $cart;
    }
} 