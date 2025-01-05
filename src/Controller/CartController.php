<?php
namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Trait\CartTrait;

#[Route('/cart')]
class CartController extends AbstractController
{
    use CartTrait;

    #[Route('', name: 'cart_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('login');
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $user->getCart(),
        ]);
    }

    #[Route('/add/{id}', name: 'cart_add', methods: ['GET', 'POST'])]
    public function add(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $cart = $this->getCart($entityManager);
        
        // Check if product already exists in cart
        $cartItem = $entityManager->getRepository(CartItem::class)
            ->findOneBy(['cart' => $cart, 'product' => $product]);
        
        if ($cartItem) {
            // Increment quantity if product exists
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } else {
            // Create new cart item if product doesn't exist
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setProduct($product);
            $cartItem->setQuantity(1);
            $cartItem->setPrice($product->getPrice());
            $entityManager->persist($cartItem);
        }
        
        $entityManager->flush();
        
        $this->addFlash('success', 'Product added to cart!');
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function remove(CartItem $cartItem, EntityManagerInterface $entityManager): Response
    {
        if ($cartItem->getCart()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $entityManager->remove($cartItem);
        $entityManager->flush();

        $this->addFlash('success', 'Item removed from cart!');
        return $this->redirectToRoute('cart_index');
    }
} 