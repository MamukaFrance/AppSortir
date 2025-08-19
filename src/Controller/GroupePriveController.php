<?php

namespace App\Controller;

use App\Entity\GroupePrive;
use App\Form\GroupePriveType;
use App\Repository\GroupePriveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/groupe/prive')]
final class GroupePriveController extends AbstractController
{
    #[Route(name: 'app_groupe_prive_index', methods: ['GET'])]
    public function index(GroupePriveRepository $groupePriveRepository): Response
    {
        return $this->render('groupe_prive/index.html.twig', [
            'groupe_prives' => $groupePriveRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_groupe_prive_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $groupePrive = new GroupePrive();
        $form = $this->createForm(GroupePriveType::class, $groupePrive);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupePrive->setCreateur($this->getUser());
            $groupePrive->setDateCreation(new \DateTimeImmutable());
            $entityManager->persist($groupePrive);
            $entityManager->flush();

            return $this->redirectToRoute('app_groupe_prive_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe_prive/new.html.twig', [
            'groupe_prive' => $groupePrive,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_groupe_prive_show', methods: ['GET'])]
    public function show(GroupePrive $groupePrive): Response
    {
        return $this->render('groupe_prive/show.html.twig', [
            'groupe_prive' => $groupePrive,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_groupe_prive_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GroupePrive $groupePrive, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroupePriveType::class, $groupePrive);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_groupe_prive_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe_prive/edit.html.twig', [
            'groupe_prive' => $groupePrive,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_groupe_prive_delete', methods: ['POST'])]
    public function delete(Request $request, GroupePrive $groupePrive, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$groupePrive->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($groupePrive);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_groupe_prive_index', [], Response::HTTP_SEE_OTHER);
    }
}
