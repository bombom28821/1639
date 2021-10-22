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
}