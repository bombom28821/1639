<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/author')]
class AuthorController extends AbstractController
{
    #[Route('/', name: 'index_author')]
    public function index(): Response
    {
        $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();
        return $this->render('author/index.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/author/detail/{id}', name: 'detail_author')]
    public function authorDetailAction($id)
    {
        $author = $this->getDoctrine()->getRepository(Author::class)->find($id);
        if (!$author) {
            $this->addFlash('Error', 'Author not found !');
            return $this->redirectToRoute('index_author');
        } else {
            return $this->render(
                'author/detail.html.twig',
                [
                    'author' => $author,
                ]
            );
        }
    }

    #[Route('/delete/{id}', name: 'delete_author')]
    public function deleteAuthorAction($id)
    {
        $author = $this->getDoctrine()->getRepository(Author::class)->find($id);
        if (!$author) {
            $this->addFlash('Error', 'Author not found!');
        } else {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($author);
            $manager->flush();
            $this->addFlash('Warn', 'Deleted author successfully!!');
        }
        return $this->redirectToRoute('index_author');
    }

    #[Route('/add', name: 'add_author')]
    public function addAuthorAction(Request $request)
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //code xử lý ảnh upload
            //B1: Lấy dữ liệu ảnh từ file upload
            $image = $author->getAvatar();
            //B2: tạo tên mới cho ảnh => tên file ảnh là duy nhất
            $imgName = uniqid(); //unique ID
            //B3: lấy ra đuôi (extension) của ảnh
            $imgExtension = $image->guessExtension();
            //B4: gộp tên mới + đuôi tạo thành tên file ảnh hoàn thiện
            $imageName = $imgName . "." . $imgExtension;
            //B5: di chuyển file ảnh upload vào thư mục chỉ định
            try {
                $image->move(
                    $this->getParameter('author_avatar'),
                    $imageName
                    //Lưu ý: cần khai báo tham số đường dẫn của thư mục cho "author_cover" ở file config/services.yaml
                );
            } catch (FileException $e) {
                // throwException($e);
            }
            //B6: lưu tên vào database
            $author->setAvatar($imageName);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($author);
            $manager->flush();

            $this->addFlash('Success', 'Add author successfully!');
            return $this->redirectToRoute('index_author');
        }
        return $this->render(
            'author/add-edit.html.twig',
            [
                'form' => $form->createView(),
                'edit' => false
            ]
        );
    }
    
    #[Route('/edit/{id}', name: 'edit_author')]
    public function editAuthorAction(Request $request, $id)
    {
        $author = $this->getDoctrine()->getRepository(Author::class)->find($id);
        if (!$author) {
            $this->addFlash('Error', 'Author not found!');
            return $this->redirectToRoute('index_author');
        } else {
            $form = $this->createForm(AuthorType::class, $author);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                //code xử lý ảnh upload
                //B1: Lấy dữ liệu từ form
                $file = $form['avatar']->getData();
                //B2: check xem file ảnh upload có null không
                if ($file) {
                    //B3: Lấy dữ liệu ảnh từ file upload
                    $image = $author->getAvatar();
                    //B4: tạo tên mới cho ảnh => tên file ảnh là duy nhất
                    $imgName = uniqid(); //unique ID
                    //B5: lấy ra đuôi (extension) của ảnh
                    $imgExtension = $image->guessExtension();
                    //B6: gộp tên mới + đuôi tạo thành tên file ảnh hoàn thiện
                    $imageName = $imgName . "." . $imgExtension;
                    //B7: di chuyển file ảnh upload vào thư mục chỉ định
                    try {
                        $image->move(
                            $this->getParameter('author_avatar'),
                            $imageName
                            //Lưu ý: cần khai báo tham số đường dẫn của thư mục cho "author_cover" ở file config/services.yaml
                        );
                    } catch (FileException $e) {
                        //throwException($e);
                    }
                    //B8: lưu tên vào database
                    $author->setAvatar($imageName);
                }
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($author);
                $manager->flush();

                $this->addFlash('Success', 'Edit author successfully!');
                return $this->redirectToRoute('index_author');
            }
            return $this->render(
                'author/add-edit.html.twig',
                [
                    'form' => $form->createView(),
                    'edit' => true
                ]
            );
        }
    }
}