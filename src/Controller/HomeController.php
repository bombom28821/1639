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
    #[Route('books/category/{id}', name: 'show_book_by_category')]
    public function showBookByCategoryAction($id)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $books = $this->getDoctrine()->getRepository(Book::class)->findBy(array('Category' => $id));

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'books' => $books,
            'idCategory' => $id
        ]);
    }
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('cart/add/{id}', name: 'add_cart')]
    public function addBookInCart(Request $request, $id)
    {
        $user = $this->getUser();
        $user->addCart($id);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();

        $refere = $request->headers->get('referer');
        return $this->redirect($refere);
    }
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('cart/remove/{id}', name: 'remove_cart')]
    public function removeBookInCart(Request $request, $id)
    {
        $user = $this->getUser();
        $user->removeCart($id);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();
        $refere = $request->headers->get('referer');
        return $this->redirect($refere);
    }
}
