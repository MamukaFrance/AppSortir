<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\VilleType;
use App\Service\CoordonneesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    #[Route('/villes/{villeId}/lieux', name: 'lieux_list')]
    public function list(EntityManagerInterface $em, int $villeId): Response
    {
        $lieux = $em->getRepository(Lieu::class)->findBy(['idVille' => $villeId]);
        $villeId = $em->getRepository(Ville::class)->findBy(['idVille'=> $villeId]);

        return $this->render('lieu/list.html.twig', [
            'lieux' => $lieux,
            'villeId' => $villeId
        ]);
    }

    #[Route('/ville/{villeId}/lieux', name: 'lieux_list')]
    public function listLieux(EntityManagerInterface $em, int $villeId, Request $request): Response
    {
        $ville = $em->getRepository(Ville::class)->find($villeId);
        if (!$ville) {
            throw $this->createNotFoundException('Ville not found.');
        }

        $lieux = $em->getRepository(Lieu::class)->findBy(['idVille' => $ville]);

        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lieu->setIdVille($ville);

            $em->persist($lieu);
            $em->flush();

            return $this->redirectToRoute('lieux_list', ['villeId' => $villeId]);
        }

        return $this->render('lieux/list.html.twig', [
            'ville' => $ville,
            'lieux' => $lieux,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/villes/{villeId}/lieux/new', name: 'lieux_new')]
    public function newLieux(int $villeId, Request $request, EntityManagerInterface $em, CoordonneesService $coord): Response
    {
        $ville = $em->getRepository(Ville::class)->find($villeId);
        if (!$ville) {
            throw $this->createNotFoundException('Ville non trouvée');
        }

        $lieu = new Lieu();
        $lieu->setIdVille($ville);

        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lieu);
            $em->flush();
            return $this->redirectToRoute('lieux_list', ['villeId' => $ville->getId()]);
        }

        return $this->render('lieux/new.html.twig', [
            'lieuForm' => $form->createView(),
            'ville' => $ville
        ]);
    }

    #[Route('/api/coordonnees', name: 'api_coordonnees', methods: ['GET'])]
    public function getCoordonnees(Request $request, CoordonneesService $coordonneesService): JsonResponse
    {
        $place = $request->query->get('place');

        if (!$place) {
            return new JsonResponse(['error' => 'Paramètre "place" manquant'], 400);
        }

        $coordonnees = $coordonneesService->trouverCoord($place);

        if (!$coordonnees) {
            return new JsonResponse(['error' => 'Lieu non trouvé'], 404);
        }

        $nom = '';
        if (isset($coordonnees['display_name'])) {
            $nomAffiche = $coordonnees['display_name'];
            $part = explode(',', $nomAffiche);
            $nom = trim($part[0]);

        }

        // Construire l'adresse de rue à partir des données Nominatim
        $rue = '';
        if (isset($coordonnees['address']) && is_array($coordonnees['address'])) {
            $address = $coordonnees['address'];
            $rueComponents = [];

            // Récupérer les composants d'adresse dans l'ordre de priorité
            if (isset($address['house_number'])) {
                $rueComponents[] = $address['house_number'];
            }
            if (isset($address['road'])) {
                $rueComponents[] = $address['road'];
            }
//            elseif (isset($address['street'])) {
//                $rueComponents[] = $address['street'];
//            } elseif (isset($address['pedestrian'])) {
//                $rueComponents[] = $address['pedestrian'];
//            } elseif (isset($address['path'])) {
//                $rueComponents[] = $address['path'];
//            }

            $rue = implode(' ', $rueComponents);
        }

        return new JsonResponse([
            'lat' => $coordonnees['lat'],
            'lon' => $coordonnees['lon'],
            'rue' => $rue,
            'nom' => $nom,
            'display_name' => $coordonnees['display_name'] ?? '',
            'address' => $coordonnees['address'] ?? null
        ]);
    }
}

