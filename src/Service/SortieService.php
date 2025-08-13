<?php

namespace App\Service;

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

}}