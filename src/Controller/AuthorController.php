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
}