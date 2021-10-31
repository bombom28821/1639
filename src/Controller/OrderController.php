<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */
class OrderController extends AbstractController
{
    #[Route('/showorder', name: 'show_order')]
    public function index()
    {
        $user = $this->getUser();
        $orders = $this->getDoctrine()->getRepository(Order::class)->findBy(array('User' => $user));
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }
}
