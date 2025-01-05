<?php
namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface; // Add this line
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'category_list')]
    public function list(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'category_detail', requirements: ['id' => '\d+'])]
    public function detail(Category $category): Response
    {
        return $this->render('category/detail.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/category/add', name: 'category_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $categoryName = $request->request->get('name'); // Retrieve the name from the form

            if (!empty($categoryName)) {
                $category = new Category();
                $category->setName($categoryName); // Set the name

                $entityManager->persist($category); // Save the category to the database
                $entityManager->flush();

                // Redirect to the category list after saving
                return $this->redirectToRoute('category_list');
            } else {
                // Handle the case where the name is empty
                $this->addFlash('error', 'Category name cannot be empty.');
            }
        }

        return $this->render('category/add.html.twig');
    }
}
