<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Service\SortieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sortie', name: 'sortie_')]
final class SortieController extends AbstractController
{

   #[Route('/create', name: 'create',methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response{

        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sortie->setIdOrganisateur($this->getUser());
            $sortie->setIdEtat("crée");
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Sortie crée');
            return $this->redirectToRoute('home');
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $form->createView(),
        ]);

   }
   #[Route('/listbysite/{id}',name: 'listbysite',requirements: ['id' => '\d+'],methods: ['GET','POST'])]
   public function listbysite(int $id, SortieService $sortieService): Response
   {
$sorties= $sortieService->listbysite($id);
       return $this->render('sortie/listbysite.html.twig', [
           'sorties' => $sorties,
       ]);
   }
}
