<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user', name: 'user_')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'profil', methods: ['GET'])]
    public function profil(): Response
    {
        return $this->render('user/profil.html.twig');
    }
}
