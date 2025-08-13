<?php

namespace App\Controller;

use App\Form\UserType;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user', name: 'user_')]
final class UserController extends AbstractController
{


    #[Route('/view', name: 'view', methods: ['GET'])]
    public function view(Request $request): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        return $this->render('/user/view.html.twig');
    }

    #[Route('/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        dump($user);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setNom($user->getNom());
            $user->setPrenom($user->getPrenom());
            $user->setEmail($user->getEmail());
            $user->setTelephone($user->getTelephone());
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Profil modifié avec succès');
            return $this->redirectToRoute('user_view');
        }

        return $this->render('/user/edit.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }
    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(UserService $userService): Response
    {
        $users = $userService->list();
        return $this->render('/user/list.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('delete/{id}', name: 'delete', methods: ['GET'])]
    public function delete(int $id, UserService $userService, EntityManagerInterface $em): Response
    {
        $userService->delete($id);
        $em->flush();
        return $this->redirectToRoute('user_list');
    }
    #[Route('desactive/{id}', name: 'desactive', methods: ['GET'])]
    public function desactive(int $id, UserService $userService, EntityManagerInterface $em): Response
    {
        $userService->desactive($id);
        $em->flush();
        return $this->redirectToRoute('user_list');
    }
}
