<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index_category')]
    public function index()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('category/index.html.twig',
                        [
                            'categories' => $categories,
                        ]
        );
    }
    #[Route('/delete/{id}', name: 'delete_category')]
    public function deleteCategoryAction($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        if(!$category){
            $this->addFlash('Error', 'Category not found!');
            return $this->redirectToRoute('index_category');           
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($category);
        $manager->flush();
        $this->addFlash('Warn', 'Deleted category successfully!!');
        return $this->redirectToRoute('index_category');  
    }
}