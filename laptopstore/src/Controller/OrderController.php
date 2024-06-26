<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $entityManager;
    private $orderRepository;


    public function __construct(OrderRepository $orderRepository, ManagerRegistry $doctrine){
        $this->productrepository = $orderRepository;
        $this->entityManager = $doctrine->getManager();   

    }

    #[Route('/user/orders', name: 'user_order_list')]
    public function userOrders(): Response
    {
        if (!this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('order/user.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    #[Route('/order/{product}', name: 'order_new')]
    public function ordernew(Product $product, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $order = new Order();

        $order->setPname($product->getName());
        $order->setPrice($product->getPrice());
        $order->setStatus('Processing...');
        $order->setUser($this->getUser());

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->addFlash('success','Order was saved');

    return $this->redirectToRoute('user_order_list');
    }
}
