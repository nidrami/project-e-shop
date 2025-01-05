<?php

// src/Controller/ProductController.php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\ProductType;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

#[Route('/product', name: 'app_product_')]
class ProductController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request, ProductRepository $productRepository): Response
    {
        $query = $request->query->get('q');
        $products = [];
        
        if ($query) {
            $products = $productRepository->createQueryBuilder('p')
                ->where('p.name LIKE :query')
                ->orWhere('p.description LIKE :query')
                ->setParameter('query', '%'.$query.'%')
                ->getQuery()
                ->getResult();
        }

        return $this->render('product/search.html.twig', [
            'products' => $products,
            'query' => $query
        ]);
    }

    #[Route('/', name: 'index')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        if ($request->isMethod('POST')) {
            $token = $request->request->get('_csrf_token');
            if (!$this->isCsrfTokenValid('add_product', $token)) {
                throw new InvalidCsrfTokenException('Invalid CSRF token');
            }

            /** @var UploadedFile $imageFile */
            $imageFile = $request->files->get('image');
            
            $product = new Product();
            $product->setName($request->request->get('name'));
            $product->setPrice((float) $request->request->get('price'));
            $product->setDescription($request->request->get('description'));
            $product->setStock((int) $request->request->get('stock'));
            
            // Handle category
            $categoryId = $request->request->get('category');
            $category = $this->entityManager->getRepository(Category::class)->find($categoryId);
            if (!$category) {
                throw $this->createNotFoundException('Category not found');
            }
            $product->setCategory($category);

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $uploadDir = $this->getParameter('product_images_directory');
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $imageFile->move($uploadDir, $newFilename);
                    $product->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload image: ' . $e->getMessage());
                }
            }

            try {
                $this->entityManager->persist($product);
                $this->entityManager->flush();
                
                $this->addFlash('success', 'Product created successfully!');
                return $this->redirectToRoute('app_product_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Failed to create product: ' . $e->getMessage());
            }
        }

        return $this->render('product/new.html.twig', [
            'categories' => $this->entityManager->getRepository(Category::class)->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'detail')]
    public function detail(Product $product): Response
    {
        return $this->render('product/detail.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, SluggerInterface $slugger): Response
    {
        if ($request->isMethod('POST')) {
            /** @var UploadedFile $imageFile */
            $imageFile = $request->files->get('image');
            
            $product = new Product();
            $product->setName($request->request->get('name'));
            $product->setPrice((float) $request->request->get('price'));
            $product->setDescription($request->request->get('description'));
            $product->setStock((int) $request->request->get('stock'));
            
            // Handle category
            $categoryId = $request->request->get('category');
            $category = $this->entityManager->getRepository(Category::class)->find($categoryId);
            $product->setCategory($category);

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('product_images_directory'),
                        $newFilename
                    );
                    
                    $product->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload image');
                }
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/add.html.twig', [
            'categories' => $this->entityManager->getRepository(Category::class)->findAll(),
        ]);
    }

}
