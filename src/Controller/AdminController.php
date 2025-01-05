<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Order;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/products', name: 'admin_products')]
    public function products(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();
        return $this->render('admin/products.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/product/new', name: 'admin_product_new')]
    public function newProduct(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product created successfully');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'New Product'
        ]);
    }

    #[Route('/product/{id}/edit', name: 'admin_product_edit')]
    public function editProduct(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Product updated successfully');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit Product'
        ]);
    }

    #[Route('/product/{id}/delete', name: 'admin_product_delete', methods: ['POST'])]
    public function deleteProduct(Product $product, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Product deleted successfully');
        return $this->redirectToRoute('admin_products');
    }

    #[Route('/categories', name: 'admin_categories')]
    public function categories(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();
        return $this->render('admin/categories.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/category/new', name: 'admin_category_new')]
    public function newCategory(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created successfully');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'New Category'
        ]);
    }

    #[Route('/category/{id}/edit', name: 'admin_category_edit')]
    public function editCategory(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Category updated successfully');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit Category'
        ]);
    }

    #[Route('/category/{id}/delete', name: 'admin_category_delete', methods: ['POST'])]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'Category deleted successfully');
        return $this->redirectToRoute('admin_categories');
    }

    #[Route('/orders', name: 'admin_orders')]
    public function orders(EntityManagerInterface $entityManager): Response
    {
        $orders = $entityManager->getRepository(Order::class)->findAll();
        return $this->render('admin/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/order/{id}', name: 'admin_order_details')]
    public function orderDetails(Order $order): Response
    {
        return $this->render('admin/order_details.html.twig', [
            'order' => $order
        ]);
    }

    #[Route('/order/{id}/validate', name: 'admin_order_validate', methods: ['POST'])]
    public function validateOrder(Order $order, EntityManagerInterface $entityManager): Response
    {
        $order->setStatus('validated');
        $entityManager->flush();

        // You could add email notification here
        // $this->dispatchMessage(new OrderStatusChangedNotification($order));

        $this->addFlash('success', 'Order has been validated');
        return $this->redirectToRoute('admin_order_details', ['id' => $order->getId()]);
    }

    #[Route('/order/{id}/decline', name: 'admin_order_decline', methods: ['POST'])]
    public function declineOrder(Order $order, EntityManagerInterface $entityManager): Response
    {
        $order->setStatus('declined');
        $entityManager->flush();

        // You could add email notification here
        // $this->dispatchMessage(new OrderStatusChangedNotification($order));

        $this->addFlash('success', 'Order has been declined');
        return $this->redirectToRoute('admin_order_details', ['id' => $order->getId()]);
    }
} 