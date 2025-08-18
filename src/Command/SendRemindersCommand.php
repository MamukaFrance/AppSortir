<?php

namespace App\Command;

use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


//on crée une commande pour la console Php
#[AsCommand(
    name: 'app:send-reminders',
    description: 'Envoie un email de rappel 48h avant chaque sortie',
)]
class SendRemindersCommand extends Command
{
    public function __construct(
        private SortieRepository $sortieRepo,
        private MailerInterface  $mailer,
        private EntityManagerInterface $em,
    )
    {
        parent::__construct();
    }

    //Fonction appelé avec la commande de console créé au dessus
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //renvoie la liste des sorties 48h avant la date de début,
        // en filtrant les sorties où le rappel n'a pas été envoyé
        $sorties = $this->sortieRepo->findStartingIn48Hours();

        //pour chaque sortie filtré on envoie un mail aux participants et a l'organisateur
        foreach ($sorties as $sortie) {
            foreach ($sortie->getListParticipant() as $participant) {
                $email = (new Email())
                    ->from('no-reply@campus-eni.fr')
                    ->to($participant->getEmail())
                    ->subject('Rappel : ' . $sortie->getNom())
                    ->text(sprintf(
                        "Bonjour %s,\nVotre sortie « %s » est prévue le %s.",
                        $participant->getNom(),
                        $sortie->getNom(),
                        $sortie->getDateHeureDebut()->format('d/m/Y H:i')
                    ));

                $this->mailer->send($email);
            }
            $organisateur=$sortie->getIdOrganisateur();
            $email=(new Email())
                ->from('no-reply@campus-eni.fr')
                ->to($organisateur->getEmail())
                ->subject('Rappel : ' . $sortie->getNom())
                ->text(sprintf( "Bonjour %s,\nVotre sortie « %s » est prévue le %s.",
                    $organisateur->getNom(),
                    $sortie->getNom(),
                    $sortie->getDateHeureDebut()->format('d/m/Y H:i')));
            $this->mailer->send($email);

            //on change dans la table, le fait que le mail est envoyé
            $sortie->setRappelEnvoye(true);

        }

        //on enregistre en BBD que le rappel est envoyé
        $this->em->flush();

        $output->writeln('Rappels envoyés avec succès.');

        return Command::SUCCESS;
    }
}
