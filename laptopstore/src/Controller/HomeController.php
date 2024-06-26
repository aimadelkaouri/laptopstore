<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;
    private $productrepository;
    private $categoryRepository;


    public function __construct(ProductRepository $productRepository, ManagerRegistry $doctrine, CategoryRepository $categoryRepository){
        $this->productrepository = $productRepository;
        $this->entityManager = $doctrine->getManager();   
        $this->categoryRepository = $categoryRepository;

    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $category = $this->categoryRepository->findAll();
        $product = $this->productrepository->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $product,
            'categories' => $category
        ]);
    }


    #[Route('/cat/{category}', name: 'product_category')]
    public function show(Category $category): Response
    {
        $products = $category->getProducts();
        $categories = $this->categoryRepository->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $category->getProducts(),
            'categories'=> $categories
        ]);
    }
}
