<?php

namespace App\Service;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\User;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class SortieService
{
    public function __construct(
        private SortieRepository $sortieRepository,
        private EntityManagerInterface $entityManager
    ) {}

    // Récupère la liste des sorties pour un site donné
    // src/Service/SortieService.php
    public function list(?int $siteId = null): array
    {
        return $this->sortieRepository->findRecent($siteId);
    }


    // Inscrit un utilisateur à une sortie
    public function registerUserToSortie(Sortie $sortie, User $user): bool
    {

        if ($sortie->getListParticipant()->contains($user)) {
            return false;
        }

        // Ajoute l'utilisateur à la liste des participants de la sortie
        $sortie->addListParticipant($user);

        // Persiste la sortie modifiée en base de données
        $this->entityManager->persist($sortie);

        // Applique les changements en base
        $this->entityManager->flush();

        return true;
    }

    public function desinscrireDeSortie(Sortie $sortie, User $user): bool
    {
        if ($sortie->getListParticipant()->contains($user)) {
            $sortie->removeListParticipant($user);
            $this->entityManager->persist($sortie);
            $this->entityManager->flush();
            return true;
        }
        return false;
    }

    public function mesSorties($userID)
    {
        return $this->sortieRepository->mesSorties($userID);
    }

    public function annulee(int $id): void
    {
        $sortie = $this->sortieRepository->find($id);
        if (!$sortie) {
            throw new \Exception("Sortie avec l'id $id non trouvée.");
        }

        // Récupérer l'entité Etat avec l'id
        $etatAnnulee = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Annulée']);
        dump($etatAnnulee);

        if (!$etatAnnulee) {
            throw new \Exception("État avec l'id 5 non trouvé.");
        }

        // Utiliser le setter correspondant au champ
        $sortie->setIdEtat($etatAnnulee);

        $this->entityManager->flush();
    }

    public function publier(int $id): void
    {
        $sortie = $this->sortieRepository->find($id);
        if (!$sortie) {
            throw new \Exception("Sortie avec l'id $id non trouvée.");
        }
        $etatPublier = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouvert']);

        if (!$etatPublier) {
            throw new \Exception("État avec l'id 5 non trouvé.");
        }
        // Utiliser le setter correspondant au champ
        $sortie->setIdEtat($etatPublier);
        $this->entityManager->flush();
    }

    public function changeStatusEnCours($sortie)
    {
     $statusEnCours = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Activité en cours']);
        if (!$statusEnCours) {
            throw new \Exception("État non trouvé.");
        }
        $sortie->setIdEtat($statusEnCours);
        $this->entityManager->flush();
    }

    public function changeStatusCloture($sortie)
    {
        $statusCloture = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Clôturé']);
        if (!$statusCloture) {
            throw new \Exception("État non trouvé.");
        }
        $sortie->setIdEtat($statusCloture);
        $this->entityManager->flush();
    }




}