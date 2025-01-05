<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/profile')]
class UserProfileController extends AbstractController
{
    #[Route('', name: 'user_profile')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        return $this->render('user_profile/index.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/edit', name: 'user_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            $user->setEmail($email);

            if ($password) {
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully');
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user_profile/edit.html.twig', [
            'user' => $user
        ]);
    }
} 