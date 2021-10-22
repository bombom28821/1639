<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    // private $hasher;
    // public function __construct(UserPasswordHasherInterface $hasher)
    // {
    //     $this->hasher = $hasher;
    // }
    public function load(ObjectManager $manager)
    {
        //tạo tài khoản của ROLE_USER
        $user = new User();
        $user->setName("user 1");
        // $user->setPassword($this->hasher->hashPassword($user, "123456"));
        $user->setPassword('123456');
        $user->setDate(\DateTime::createFromFormat('Y-m-d', '1990-05-08'));
        $user->setAvatar("avatar.jpg");
        $user->setEmail("user1@fpt.edu.vn");
        $user->setAddress("Lao Cai Kingdom");

        $user->setRole('ROLE_USER');
        $manager->persist($user);

        $user = new User();
        $user->setName("user 2");
        // $user->setPassword($this->hasher->hashPassword($user, "123456"));
        $user->setPassword('123456');
        $user->setDate(\DateTime::createFromFormat('Y-m-d', '1990-05-08'));
        $user->setAvatar("avatar.jpg");
        $user->setEmail("user2@fpt.edu.vn");
        $user->setAddress("Ha Noi");

        $user->setRole('ROLE_USER');
        $manager->persist($user);

        //tạo tài khoản của ROLE_STAFF
        $user = new User();
        $user->setName("staff");
        // $user->setPassword($this->hasher->hashPassword($user, "123456"));
        $user->setPassword('123456');
        $user->setDate(\DateTime::createFromFormat('Y-m-d', '1990-05-08'));
        $user->setAvatar("avatar.jpg");
        $user->setEmail("staff@fpt.edu.vn");
        $user->setAddress("Ha Noi");

        $user->setRole('ROLE_STAFF');
        $manager->persist($user);

        //tạo tài khoản của ROLE_ADMIN
        $user = new User();
        $user->setName("admin");
        // $user->setPassword($this->hasher->hashPassword($user, "123456"));
        $user->setPassword('123456');
        $user->setDate(\DateTime::createFromFormat('Y-m-d', '1990-05-08'));
        $user->setAvatar("avatar.jpg");
        $user->setEmail("admin@fpt.edu.vn");
        $user->setAddress("Ha Noi");

        $user->setRole('ROLE_ADMIN');
        $manager->persist($user);


        $manager->flush();
    }
}
