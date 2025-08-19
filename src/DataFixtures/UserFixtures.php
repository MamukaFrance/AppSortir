<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {




        // Utilisateur par defaut
        $user = new User();
        $user->setNom("userNom");
        $user->setPrenom("UserPrenom");
        $user->setEmail("userEmail@campus-eni.fr");
        $hashedPassword = $this->passwordHasher->hashPassword($user, "123456");
        $user->setPassword($hashedPassword);
        $user->setActif(true);
        $user->setAdministrateur(false);
        $user->setTelephone("0612345678");

        $manager->persist($user);


        // Administrateur par defaut
        $user = new User();
        $user->setNom("adminNom");
        $user->setPrenom("adminPrenom");
        $user->setEmail("adminEmail@campus-eni.fr");
        $hashedPassword = $this->passwordHasher->hashPassword($user, "123456");
        $user->setPassword($hashedPassword);
        $user->setActif(true);
        $user->setAdministrateur(true);
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setTelephone("0612345678");

        $manager->persist($user);


        $manager->flush();

    }
}
