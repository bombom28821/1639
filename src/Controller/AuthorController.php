<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @IsGranted("ROLE_STAFF")
 */
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
            //code x??? l?? ???nh upload
            //B1: L???y d??? li???u ???nh t??? file upload
            $image = $author->getAvatar();
            //B2: t???o t??n m???i cho ???nh => t??n file ???nh l?? duy nh???t
            $imgName = uniqid(); //unique ID
            //B3: l???y ra ??u??i (extension) c???a ???nh
            $imgExtension = $image->guessExtension();
            //B4: g???p t??n m???i + ??u??i t???o th??nh t??n file ???nh ho??n thi???n
            $imageName = $imgName . "." . $imgExtension;
            //B5: di chuy???n file ???nh upload v??o th?? m???c ch??? ?????nh
            try {
                $image->move(
                    $this->getParameter('author_avatar'),
                    $imageName
                    //L??u ??: c???n khai b??o tham s??? ???????ng d???n c???a th?? m???c cho "author_cover" ??? file config/services.yaml
                );
            } catch (FileException $e) {
                // throwException($e);
            }
            //B6: l??u t??n v??o database
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
                //code x??? l?? ???nh upload
                //B1: L???y d??? li???u t??? form
                $file = $form['avatar']->getData();
                //B2: check xem file ???nh upload c?? null kh??ng
                if ($file) {
                    //B3: L???y d??? li???u ???nh t??? file upload
                    $image = $author->getAvatar();
                    //B4: t???o t??n m???i cho ???nh => t??n file ???nh l?? duy nh???t
                    $imgName = uniqid(); //unique ID
                    //B5: l???y ra ??u??i (extension) c???a ???nh
                    $imgExtension = $image->guessExtension();
                    //B6: g???p t??n m???i + ??u??i t???o th??nh t??n file ???nh ho??n thi???n
                    $imageName = $imgName . "." . $imgExtension;
                    //B7: di chuy???n file ???nh upload v??o th?? m???c ch??? ?????nh
                    try {
                        $image->move(
                            $this->getParameter('author_avatar'),
                            $imageName
                            //L??u ??: c???n khai b??o tham s??? ???????ng d???n c???a th?? m???c cho "author_cover" ??? file config/services.yaml
                        );
                    } catch (FileException $e) {
                        //throwException($e);
                    }
                    //B8: l??u t??n v??o database
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
