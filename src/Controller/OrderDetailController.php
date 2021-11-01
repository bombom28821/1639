<?php

namespace App\Controller;

use App\Entity\OrderDetail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class OrderDetailController extends AbstractController
{
    #[Route('/showorderdetail/{id}', name: 'show_order_detail')]
    public function index($id)
    {
        $orderDetails = $this->getDoctrine()->getRepository(OrderDetail::class)->findBy(array('Order' => $id));
        return $this->render('order_detail/index.html.twig', [
            'orderDetails' => $orderDetails,
        ]);
    }
}
