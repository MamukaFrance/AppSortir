<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Event\SortieInscriptionEvent;
use App\Form\SortieType;
use App\Message\ChangeStatusMessage;
use App\Repository\LieuRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Service\MailService;
use App\Service\SortieService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sortie', name: 'sortie_')]
final class SortieController extends AbstractController
{

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request,
                           EntityManagerInterface $entityManager,
                           SortieService $sortieService,
                           MessageBusInterface $bus): Response
    {

        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ville = $form->get('ville')->getData();


            $sortie->setIdOrganisateur($this->getUser());
            $sortie->setIdEtat($entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Créée']));
            $entityManager->persist($sortie);
            $user =  $this->getUser();
            $sortieService->registerUserToSortie($sortie, $user);
            $entityManager->flush();

            // 1️⃣ Passage à "Activité en cours" à la date de début
            $now = new \DateTimeImmutable();
            $delayToStart = max(0, ($sortie->getDateHeureDebut()->getTimestamp() - $now->getTimestamp()) * 1000);
            $bus->dispatch(
                new ChangeStatusMessage($sortie->getId(), "Activité en cours"),
                [new DelayStamp($delayToStart)]
            );

            // 2️⃣ Passage à "Passée" après date début + durée
            $dateFin = $sortie->getDateHeureDebut()->modify('+' . $sortie->getDuree() . ' minutes');
            $delayToEnd = max(0, ($dateFin->getTimestamp() - $now->getTimestamp()) * 1000);
            $bus->dispatch(
                new ChangeStatusMessage($sortie->getId(), "Passée"),
                [new DelayStamp($delayToEnd)]
            );


            $this->addFlash('success', 'Sortie crée');
            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $form->createView(),
        ]);

    }

    #[Route('/lieux/by-ville/{id}', name: 'lieux_by_ville', methods: ['GET'])]
    public function getLieuxByVille(int $id, LieuRepository $lieuRepo): JsonResponse
    {
        $lieux = $lieuRepo->findBy(['idVille' => $id]);

        $data = [];
        foreach ($lieux as $lieu) {
            $data[] = [
                'id' => $lieu->getId(),
                'nom' => $lieu->getNom()
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(
        Request        $request,
        SortieService  $sortieService,
        SiteRepository $siteRepo
    ): Response
    {
        $siteId = $request->query->getInt('site', 0);
        $sites = $siteRepo->findAll();

        $sorties = $sortieService->list($siteId > 0 ? $siteId : null);

        return $this->render('sortie/list.html.twig', [
            'sites' => $sites,
            'sorties' => $sorties,
        ]);
    }

    #[Route('/{id}/register', name: 'register', methods: ['GET'])]
    public function register(Sortie $sortie, SortieService $sortieService, EntityManagerInterface $em, EventDispatcherInterface $dispatcher, MailService $mailService): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez vous connecter pour vous inscrire');
            return $this->redirectToRoute('app_login');
        }

        try {
//            $dispatcher->dispatch(new SortieInscriptionEvent($sortie));
            $nb = $sortie->getListParticipant()->count();
            if ($nb >= $sortie->getNbInscriptionsMax()) {
                $this->addFlash('warning', 'Pas de place dans cette sortie');
                return $this->redirectToRoute('sortie_list', [
                    'id' => $sortie->getId()
                ]);
            }
            $success = $sortieService->registerUserToSortie($sortie, $user);
            if ($success) {
                $em->flush();
                $this->addFlash('success', 'Inscription réussie !');
                $mailService->sendEmailInscription();
                $this->addFlash('success', 'Mail d\'inscriprtion est envoyé');

                return $this->redirectToRoute('sortie_show', [
                    'id' => $sortie->getId(),
                    'registered' => $sortie->getId()
                ]);
            } else {
                $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'inscription: ' . $e->getMessage());
        }

        return $this->redirectToRoute('sortie_list', [
            'id' => $sortie->getId()
        ]);
    }

    #[Route('/{id}/desinscrire', name: 'desinscrire', methods: ['GET'])]
    public function desinscrire(Sortie $sortie, SortieService $sortieService,MailService $mailService): RedirectResponse
    {
        $user = $this->getUser();
        $success = $sortieService->desinscrireDeSortie($sortie, $user);
        if($success){
            $this->addFlash('success', 'Vous êtes désinscrit');
            $mailService->sendEmailDesInscription();
            $this->addFlash('success', 'Mail de désinscriprtion est envoyé');
        }else{
            $this->addFlash('warning', 'Vous êtes déjà desinscrit !');
        }
        return $this->redirectToRoute('sortie_list', [
            'id' => $sortie->getId()
        ]);
    }


    #[Route('/show/{id}', name: 'show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        $participants = $sortie->getListParticipant();
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie, 'participants' => $participants
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['GET'])]
    public function delete(Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'Sortie supprimée');
        return $this->redirectToRoute('sortie_list');
    }
    #[Route('/mes-sorties', name: 'mes-sorties', methods: ['GET'])]
    public function mesSorties(Request $request, SortieService $sortieService): Response
    {
        $userID = $this->getUser()->getId();

        $sorties = $sortieService->mesSorties($userID);

        return $this->render('/user/mes-sorties.html.twig', ['sorties' => $sorties]);
    }

    #[Route('/annuler/{id}', name: 'annuler', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function annuler(int $id, Request $request,SiteRepository $siteRepo, SortieService $sortieService, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        if ($sortie->getDateHeureDebut() > new \DateTime()) {
            $sortieService->annulee($id);
            $siteId = $request->query->getInt('site', 0);
            $sorties = $sortieService->list($siteId > 0 ? $siteId : null);
            $sites = $siteRepo->findAll();


        }

        return $this->render('sortie/list.html.twig', ['sites' => $sites,'sorties' => $sorties]);
    }

    #[Route('/publier/{id}', name: 'publier', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function publier(int $id, Request $request,SiteRepository $siteRepo, SortieService $sortieService, SortieRepository $sortieRepository): Response
    {
            $sortieService->publier($id);
            $siteId = $request->query->getInt('site', 0);
            $sorties = $sortieService->list($siteId > 0 ? $siteId : null);
            $sites = $siteRepo->findAll();





        return $this->render('sortie/list.html.twig', ['sites' => $sites,'sorties' => $sorties]);
    }

}
