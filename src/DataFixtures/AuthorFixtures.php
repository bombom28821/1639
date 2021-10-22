<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for($i = 0; $i < 5; $i++) {
            $author = new Author();
            $author->setName("Author $i");
            $author->setAvatar("avatar.jpg");
            $author->setDate(\DateTime::createFromFormat('Y-m-d', '1990-05-08'));
            $manager->persist($author);
        }
        $manager->flush();
    }
}
