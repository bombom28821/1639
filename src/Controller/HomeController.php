<?php

namespace App\Controller;

use DateTime;
use App\Entity\Book;
use App\Entity\Order;
use App\Entity\Category;
use App\Entity\OrderDetail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'books' => $books,
            'idCategory' => false,
        ]);
    }
    #[Route('/book/{id}', name: 'detail_book_user')]
    public function bookDetailAction($id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        if (!$book) {
            $this->addFlash('Error', 'Book not found !');
            return $this->redirectToRoute('index');
        } else {
            return $this->render(
                'home/bookDetail.html.twig',
                [
                    'book' => $book,
                ]
            );
        }
    }
}
