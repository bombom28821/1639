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
<<<<<<< HEAD

=======
>>>>>>> c6a27dd96bc86ddc984f4d1477398310887ff0b1
    #[Route('/add', name: 'add_book')]
    public function addbookAction(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //code x??? l?? ???nh upload
            //B1: L???y d??? li???u ???nh t??? file upload
            $image = $book->getCover();
            //B2: t???o t??n m???i cho ???nh => t??n file ???nh l?? duy nh???t
            $imgName = uniqid(); //unique ID
            //B3: l???y ra ??u??i (extension) c???a ???nh
            $imgExtension = $image->guessExtension();
            //B4: g???p t??n m???i + ??u??i t???o th??nh t??n file ???nh ho??n thi???n
            $imageName = $imgName . "." . $imgExtension;
            //B5: di chuy???n file ???nh upload v??o th?? m???c ch??? ?????nh
            try {
                $image->move(
                    $this->getParameter('book_cover'),
                    $imageName
                    //L??u ??: c???n khai b??o tham s??? ???????ng d???n c???a th?? m???c cho "book_cover" ??? file config/services.yaml
                );
            } catch (FileException $e) {
                // throwException($e);
            }
            //B6: l??u t??n v??o database
            $book->setCover($imageName);
<<<<<<< HEAD
            $book->setOrderQuantity(0);
=======
            $book->addOrderQuantity(0);
>>>>>>> c6a27dd96bc86ddc984f4d1477398310887ff0b1
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
<<<<<<< HEAD

=======
>>>>>>> c6a27dd96bc86ddc984f4d1477398310887ff0b1
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
                //code x??? l?? ???nh upload
                //B1: L???y d??? li???u t??? form
                $file = $form['cover']->getData();
                //B2: check xem file ???nh upload c?? null kh??ng
                if ($file) {
                    //B3: L???y d??? li???u ???nh t??? file upload
                    $image = $book->getCover();
                    //B4: t???o t??n m???i cho ???nh => t??n file ???nh l?? duy nh???t
                    $imgName = uniqid(); //unique ID
                    //B5: l???y ra ??u??i (extension) c???a ???nh
                    $imgExtension = $image->guessExtension();
                    //B6: g???p t??n m???i + ??u??i t???o th??nh t??n file ???nh ho??n thi???n
                    $imageName = $imgName . "." . $imgExtension;
                    //B7: di chuy???n file ???nh upload v??o th?? m???c ch??? ?????nh
                    try {
                        $image->move(
                            $this->getParameter('book_cover'),
                            $imageName
                            //L??u ??: c???n khai b??o tham s??? ???????ng d???n c???a th?? m???c cho "book_cover" ??? file config/services.yaml
                        );
                    } catch (FileException $e) {
                        //throwException($e);
                    }
                    //B8: l??u t??n v??o database
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
<<<<<<< HEAD
}
=======
}
>>>>>>> c6a27dd96bc86ddc984f4d1477398310887ff0b1
