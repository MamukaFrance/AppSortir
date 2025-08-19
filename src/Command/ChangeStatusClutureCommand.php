<?php

namespace App\Command;

use App\Repository\SortieRepository;
use App\Service\SortieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;




//on crée une commande pour la console Php
#[AsCommand(
    name: 'app:changeStatus-cloture',
    description: 'Changer l\'état en "Cloturé" ',
)]
class ChangeStatusClutureCommand extends Command
{
    public function __construct(
        private SortieRepository $sortieRepo,
//        private MailerInterface  $mailer,
//        private EntityManagerInterface $em,
        private SortieService $sortieService,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
    $sorties = $this->sortieRepo->findAll();
    foreach ($sorties as $sortie) {
        if ($sortie->getNbInscriptionsMax() >= $sortie->getListParticipant()->count()) {
            $this->sortieService->changeStatusCloture($sortie);
        }

}




    }

}
