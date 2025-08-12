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
    public function listbysite(int $id)
    {
        return $this->sortieRepository->findBySite($id);
    }

    public function list()
    {
        $sorties = $this->sortieRepository->findAll();
        return $sorties;
    }

    // Inscrit un utilisateur à une sortie
    public function registerUserToSortie(Sortie $sortie, User $user)
    {
        // Ajoute l'utilisateur à la liste des participants de la sortie
        $sortie->addListParticipant($user);

        // Persiste la sortie modifiée en base de données
        $this->entityManager->persist($sortie);

        // Applique les changements en base
        $this->entityManager->flush();
    }


}