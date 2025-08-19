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
    name: 'app:changeStatus-enCours',
    description: 'Changer l\'état en "Activite en cours"',
)]
class ChangeStatusEnCoursCommand extends Command
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new \DateTimeImmutable('now');  // date actuelle

        $sorties = $this->sortieRepo->findAll();
        foreach ($sorties as $sortie) {
            $dateDebut = $sortie->getDateHeureDebut();
            $datePlusDuree = $dateDebut->add(new \DateInterval('PT' . $sortie->getDuree() . 'M'));

            if ($dateDebut <= $now && $now <= $datePlusDuree) {
                $this->sortieService->changeStatusEnCours($sortie);
            }
        }
        return Command::SUCCESS;
    }
}
