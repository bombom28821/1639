<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @IsGranted("ROLE_STAFF")
 */
#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('/', name: 'index_book')]
    public function index(): Response
    {
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }
    #[Route('/book/detail/{id}', name: 'detail_book')]
    public function bookDetailAction($id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        if (!$book) {
            $this->addFlash('Error', 'Book not found !');
            return $this->redirectToRoute('index_book');
        } else {
            return $this->render(
                'book/detail.html.twig',
                [
                    'book' => $book,
                ]
            );
        }
    }
    #[Route('/delete/{id}', name: 'delete_book')]
    public function deleteBookAction($id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        $orderQuantity = $book->getOrderQuantity();
        if (!$book) {
            $this->addFlash('Error', 'Book not found!');
        } else {
            if($orderQuantity > 0){
                $this->addFlash('Error', "Can't delete!Book already has an order");
                return $this->redirectToRoute('index_book');
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($book);
            $manager->flush();
            $this->addFlash('Warn', 'Deleted book successfully!!');
        }
        return $this->redirectToRoute('index_book');
    }
    #[Route('/add', name: 'add_book')]
    public function addbookAction(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //code xử lý ảnh upload
            //B1: Lấy dữ liệu ảnh từ file upload
            $image = $book->getCover();
            //B2: tạo tên mới cho ảnh => tên file ảnh là duy nhất
            $imgName = uniqid(); //unique ID
            //B3: lấy ra đuôi (extension) của ảnh
            $imgExtension = $image->guessExtension();
            //B4: gộp tên mới + đuôi tạo thành tên file ảnh hoàn thiện
            $imageName = $imgName . "." . $imgExtension;
            //B5: di chuyển file ảnh upload vào thư mục chỉ định
            try {
                $image->move(
                    $this->getParameter('book_cover'),
                    $imageName
                    //Lưu ý: cần khai báo tham số đường dẫn của thư mục cho "book_cover" ở file config/services.yaml
                );
            } catch (FileException $e) {
                // throwException($e);
            }
            //B6: lưu tên vào database
            $book->setCover($imageName);
            $book->addOrderQuantity(0);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($book);
            $manager->flush();

            $this->addFlash('Success', 'Add book successfully!');
            return $this->redirectToRoute('index_book');
        }
        return $this->render(
            'book/add-edit.html.twig',
            [
                'form' => $form->createView(),
                'edit' => false,
            ]
        );
    }
    #[Route('/edit/{id}', name: 'edit_book')]
    public function editBookAction(Request $request, $id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        $orderQuantity = $book->getOrderQuantity();
        if (!$book) {
            $this->addFlash('Error', 'Book not found!');
            return $this->redirectToRoute('index_book');
        } else {
            $form = $this->createForm(BookType::class, $book);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                //code xử lý ảnh upload
                //B1: Lấy dữ liệu từ form
                $file = $form['cover']->getData();
                //B2: check xem file ảnh upload có null không
                if ($file) {
                    //B3: Lấy dữ liệu ảnh từ file upload
                    $image = $book->getCover();
                    //B4: tạo tên mới cho ảnh => tên file ảnh là duy nhất
                    $imgName = uniqid(); //unique ID
                    //B5: lấy ra đuôi (extension) của ảnh
                    $imgExtension = $image->guessExtension();
                    //B6: gộp tên mới + đuôi tạo thành tên file ảnh hoàn thiện
                    $imageName = $imgName . "." . $imgExtension;
                    //B7: di chuyển file ảnh upload vào thư mục chỉ định
                    try {
                        $image->move(
                            $this->getParameter('book_cover'),
                            $imageName
                            //Lưu ý: cần khai báo tham số đường dẫn của thư mục cho "book_cover" ở file config/services.yaml
                        );
                    } catch (FileException $e) {
                        //throwException($e);
                    }
                    //B8: lưu tên vào database
                    $book->setCover($imageName);
                }
                $quantityForm = $form['quantity']->getData();
                if($quantityForm < $orderQuantity){
                    $this->addFlash('Error', 'Quantity invalid!');
                    return $this->redirectToRoute('edit_book',[
                        'id' => $id
                    ]);
                }
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($book);
                $manager->flush();

                $this->addFlash('Success', 'Edit book successfully!');
                return $this->redirectToRoute('index_book');
            }
            return $this->render(
                'book/add-edit.html.twig',
                [
                    'form' => $form->createView(),
                    'edit' => true,
                    'orderQuantity' => $orderQuantity,
                ]
            );
        }
    }
}
