<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SiteRepository;
use App\Service\SortieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    #[Route('/{id}/register', name: 'register', methods: ['GET'])]
    public function register(Sortie $sortie, SortieService $sortieService, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez vous connecter pour vous inscrire');
            return $this->redirectToRoute('app_login');
        }

        try {
            $success = $sortieService->registerUserToSortie($sortie, $user);
            if ($success) {
                $em->flush();
                $this->addFlash('success', 'Inscription réussie !');

                return $this->redirectToRoute('sortie_listbysite', [
                    'id' => $sortie->getIdSite()->getId(),
                    'registered' => $sortie->getId()
                ]);
            } else {
                $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'inscription: ' . $e->getMessage());
        }

        return $this->redirectToRoute('sortie_listbysite', [
            'id' => $sortie->getIdSite()->getId()
        ]);
    }
    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(
        Request $request,
        SortieService $sortieService,
        SiteRepository $siteRepo
    ): Response {
        $siteId = $request->query->getInt('site', 0);
        $sites = $siteRepo->findAll();

        if ($siteId > 0) {
            $sorties = $sortieService->listbysite($siteId);
        } else {
            $sorties = $sortieService->list();
        }

        return $this->render('sortie/list.html.twig', [
            'sites' => $sites,
            'sorties' => $sorties,
        ]);
    }
    #[Route('show/{id}', name: 'show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        $participants = $sortie->getListParticipant();
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,'participants' => $participants
        ]);
    }

}
