<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\CheckoutType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Trait\CartTrait;

class CheckoutController extends AbstractController
{
    use CartTrait;

    #[Route('/checkout', name: 'checkout')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cart = $this->getCart($entityManager);
        if (!$cart || $cart->getItems()->isEmpty()) {
            return $this->redirectToRoute('cart_index');
        }

        $order = new Order();
        $form = $this->createForm(CheckoutType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->setUser($this->getUser());
            $order->setStatus('pending');
            $total = 0;

            // Transfer cart items to order items
            foreach ($cart->getItems() as $cartItem) {
                $orderItem = new OrderItem();
                $orderItem->setOrder($order);
                $orderItem->setProduct($cartItem->getProduct());
                $orderItem->setQuantity($cartItem->getQuantity());
                $orderItem->setPrice($cartItem->getPrice());
                $entityManager->persist($orderItem);
                
                $total += $cartItem->getQuantity() * $cartItem->getPrice();
            }

            $order->setTotal($total);
            $entityManager->persist($order);

            // Clear the cart
            foreach ($cart->getItems() as $item) {
                $entityManager->remove($item);
            }
            $entityManager->remove($cart);
            
            $entityManager->flush();

            return $this->redirectToRoute('order_confirmation', ['id' => $order->getId()]);
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart
        ]);
    }

    #[Route('/checkout/confirmation/{id}', name: 'order_confirmation')]
    public function confirmation(Order $order): Response
    {
        // Ensure the order belongs to the current user
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('checkout/confirmation.html.twig', [
            'order' => $order
        ]);
    }

    #[Route('/checkout/process', name: 'checkout_process', methods: ['POST'])]
    public function process(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cart = $this->getCart($entityManager);
        if (!$cart || $cart->getItems()->isEmpty()) {
            return $this->redirectToRoute('cart_index');
        }

        // Create new order
        $order = new Order();
        $order->setUser($this->getUser());
        $order->setStatus('pending');
        
        // Transfer cart items to order items
        foreach ($cart->getItems() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setOrder($order);
            $orderItem->setProduct($cartItem->getProduct());
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setPrice($cartItem->getPrice());
            $entityManager->persist($orderItem);
        }

        $entityManager->persist($order);
        
        // Clear the cart
        foreach ($cart->getItems() as $item) {
            $entityManager->remove($item);
        }
        $entityManager->remove($cart);
        
        $entityManager->flush();

        $this->addFlash('success', 'Your order has been placed successfully!');
        return $this->redirectToRoute('order_confirmation', ['id' => $order->getId()]);
    }
} 