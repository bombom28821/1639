<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for($i = 0; $i < 15; $i++) {
            $book = new Book();
            $book->setName("Book $i");
            $book->setDescription("Book is my life $i");
            $book->setCover("cover.jpg");
            $book->setQuantity(rand(10,20));
            $book->setPrice(23.12);
            $book->addOrderQuantity(0);
            $book->setManufacturer("Kim Dong");

            $manager->persist($book);
        }
        $manager->flush();
    }
}
