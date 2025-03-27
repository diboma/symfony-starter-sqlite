<?php

namespace App\DataFixtures;

use App\Entity\User\User;
use App\Entity\Product\Product;
use App\Entity\Product\Category;
use App\DataFixtures\UserFixtures;
use App\Repository\User\UserRepository;
use App\Repository\Category\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductAndCategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private HttpClientInterface $client,
        private CategoryRepository $categoryRepository,
        private UserRepository $userRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {

        // Get data from API
        $response = $this->client->request(
            'GET',
            'https://fakestoreapi.com/products'
        );
        $data = $response->toArray();

        // Get categories from data
        $categoryNames = array_unique(
            array_map(
                function ($product) {
                    return $product['category'];
                }, $data
            )
        );

        // Get all users from database
        $users = $manager->getRepository(User::class)->findAll();
        $userIds = array_map(
            function ($user) {
                return $user->getId();
            }, $users
        );

        // Create categories
        foreach ($categoryNames as $categoryName) {
            if ($this->categoryRepository->findOneBy(['name' => $categoryName])) {
                continue;
            }

            // Create category
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);

            // Filter products by category
            $products = array_filter(
                $data, function ($product) use ($categoryName) {
                    return $product['category'] === $categoryName;
                }
            );

            // Add products to category
            foreach ($products as $product) {
                  // Create product
                  $newProduct = new Product();
                  $newProduct->setName($product['title']);
                  $newProduct->setPrice($product['price']);
                  $newProduct->setDescription($product['description']);
                  $newProduct->setImage($product['image']);
                  $newProduct->setCategory($category);
                  $newProduct->setOwner($this->userRepository->find(array_rand($userIds)));

                  $manager->persist($newProduct);
                  $manager->flush();

                  // Add product to category
                  $category->addProduct($newProduct);
            }
        }
    }

    public function getDependencies()
    {
        return [
        UserFixtures::class,
        ];
    }
}
