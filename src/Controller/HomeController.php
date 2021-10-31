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
            'checkHome' => true
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
            'idCategory' => $id,
            'checkHome' => true,
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
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('cart/view', name: 'show_cart')]
    public function showCart()
    {
        $books = [];
        $user = $this->getUser();
        $idBooks = $user->getCart(); //[1,3,4,5,3]
        foreach ($idBooks as $idBook) {
            $book = $this->getDoctrine()->getRepository(Book::class)->find($idBook);
            array_push($books, $book);
        }
        return $this->render('home/cart.html.twig', [
            'books' => $books,
        ]);
    }
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('cart/order', name: 'order_cart')]
    public function orderCart()
    {
        
        $idBooks = $_POST['idBooks'];
        $orderQuantityForm = $_POST['orderQuantity'];

        $user = $this->getUser();
        $totalPrice = 0;
        $orderQuantity = [];
        foreach ($orderQuantityForm as $order) {
            if ($order != null) {
                array_push($orderQuantity, $order);
            }
        }

        for ($i = 0; $i < count($idBooks); $i++) {
            $book = $this->getDoctrine()->getRepository(Book::class)->find($idBooks[$i]);
            $user->removeCart($book->getId());
            $totalPrice += $book->getPrice() * $orderQuantity[$i];
            $book->addOrderQuantity($orderQuantity[$i]);
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();

        $order = $this->createOrder($user, $totalPrice);

        for ($i = 0; $i < count($idBooks); $i++) {
            $book = $this->getDoctrine()->getRepository(Book::class)->find($idBooks[$i]);
            $this->createOrderDetail($order, $book, $orderQuantity[$i]);
        }
        return $this->redirectToRoute('show_order');
    }
    public function createOrder($User, $totalPrice)
    {

        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $dateNow = new DateTime();

        $order = new Order();
        $order->setUser($User);
        $order->setTotalPrice($totalPrice);
        $order->setOrderDate($dateNow);
        $order->setStatus('To Pay');

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($order);
        $manager->flush();

        return $order;
    }
    public function createOrderDetail($order, $book, $quantity)
    {
        $orderDetail = new OrderDetail();
        $orderDetail->setOrder($order);
        $orderDetail->setBook($book);
        $orderDetail->setQuantity($quantity);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($orderDetail);
        $manager->flush();
    }
    #[Route('books/search', name: 'search_book')]
    public function searchBookAction(Request $request)
    {
        $name = $request->query->get('name');
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $books = $this->getDoctrine()->getRepository(Book::class)->findBy(array('name' => $name));

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'books' => $books,
            'idCategory' => false,
            'checkHome' => true,
        ]);
    }
}
