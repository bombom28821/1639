<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        if (!$book) {
            $this->addFlash('Error', 'Book not found!');
        } else {
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
            $book->setOrderQuantity(0);
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
}