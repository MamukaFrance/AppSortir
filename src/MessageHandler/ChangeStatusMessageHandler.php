<?php

// src/MessageHandler/MessageHandler.php
namespace App\MessageHandler;

use App\Message\ChangeStatusMessage;
use App\Repository\SortieRepository;
use App\Service\SortieService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ChangeStatusMessageHandler
{
    public function __construct(
        private SortieRepository $sortieRepository,
        private SortieService $sortieService
    ) {}

    public function __invoke(ChangeStatusMessage $message)
    {
        $sortie = $this->sortieRepository->find($message->getSortieId());

        if (!$sortie) {
            return;
        }

        switch ($message->getContenu()) {
            case "Activité en cours":
                $this->sortieService->changeStatusEnCours($sortie);
                break;

            case "Passée":
                $this->sortieService->changeStatusPassee($sortie);
                break;

            default:
                return;
        }

    }
}

