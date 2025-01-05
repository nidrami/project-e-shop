<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/profile', name: 'user_profile')]
    public function profile(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('login');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/orders', name: 'user_orders')]
    public function orders(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('login');
        }

        return $this->render('user/orders.html.twig', [
            'orders' => $user->getOrders()
        ]);
    }
} 