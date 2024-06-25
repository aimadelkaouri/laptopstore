<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;

class ProductController extends AbstractController
{

    private $productrepository;
    private $entityManager;

    public function __construct(ProductRepository $productRepository, \Doctrine\Persistence\ManagerRegistry $doctrine){
        $this->productrepository = $productRepository;
        $this->entityManager = $doctrine->getManager();
    }

    #[Route('/product/show', name: 'product_list')]
    public function index(Request $request): Response
    {
        $products = $this->productrepository->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }


    #[Route('/product/show/{id}', name: 'product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'products' => $product,
        ]);
    }

    #[Route('/product/delete/{id}', name: 'product_delete')]
    public function delete(Product $product): Response
    {
        $filesystem = new Filesystem();
        $imagePath = './uploads/'.$product->getImage();
        if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        $this->addFlash('success','product was removed');

        return $this->redirectToRoute('product_list');
    }


    #[Route('/product/edit/{id}', name: 'product_edit')]
    public function edit(Product $product, Request $request): Response
    {
        $form= $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form ->isValid()) {
            $form->getData();
            if ($request->files->get('product')['image']) {
                $image = $request->files->get('product')['image'];
                $image_name = time().'_'.$image->getClientOriginalName();
                $image->move($this->getParameter('image_directory', $image_name));
                $product->setImage($image_name);
            }


            $this->entityManager->persist($product);
            $this->entityManager->flush();
            

            $this->addFlash('succes','Your Product was updated');

            return $this->redirectToRoute('product_list');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/product/create', name: 'product_store')]
    public function store(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        //traiter les données :
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $product= $form->getData();

            if ($request->files->get('product')['image']) {
                $image= $request->files->get('product')['image'];
                $image_name = time().'_'.$image->getClientOriginalName(); 
                $image->move($this->getParameter('image_directory'), $image_name);
                $product->setImage($image_name);
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash('success','Your product was saved');

            return $this->redirectToRoute('product_list');

        }

        return $this->render('product/create.html.twig', [
            'form' => $form,
        ]);
    }
}
