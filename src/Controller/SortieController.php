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

    #[Route('/sortie/{id}/register', name: 'sortie_register')]
    public function register(Sortie $sortie, SortieService $sortieService): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'vous devrez entrer au site');
            return $this->redirectToRoute('app_login');
        }

        try {
            $success = $sortieService->registerUserToSortie($user, $sortie);
            if ($success) {
                $this->addFlash('success', 'Vous avez resussit de inscit');
            } else {
                $this->addFlash('warning', 'vous avez deja inscrit');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'error' . $e->getMessage());
        }

        return $this->redirectToRoute('sortie_list'); // یا صفحه‌ای که لیست sortie ها هست
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

}
