<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/store/product', name: 'product_store')]
    public function index(Request $request): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/store/product/create', name: 'product_create')]
    public function store(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        return $this->render('product/create.html.twig', [
            'form' => $form,
        ]);
    }
}
