<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create categories
        $categories = [];
        $categoryNames = ['Electronics', 'Books', 'Clothing', 'Home & Garden'];
        
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Create sample products
        $products = [
            ['name' => 'Laptop', 'price' => 999.99, 'category' => 0, 'stock' => 10],
            ['name' => 'Smartphone', 'price' => 499.99, 'category' => 0, 'stock' => 15],
            ['name' => 'Novel', 'price' => 19.99, 'category' => 1, 'stock' => 50],
            ['name' => 'T-Shirt', 'price' => 29.99, 'category' => 2, 'stock' => 100],
            ['name' => 'Garden Tools', 'price' => 149.99, 'category' => 3, 'stock' => 5],
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setPrice($productData['price']);
            $product->setCategory($categories[$productData['category']]);
            $product->setStock($productData['stock']);
            $product->setDescription('Sample description for ' . $productData['name']);
            $manager->persist($product);
        }

        $manager->flush();
    }
} 