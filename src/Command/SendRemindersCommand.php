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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sorties = $this->sortieRepo->findStartingIn48Hours();
        $count = 0;

        foreach ($sorties as $sortie) {
            // Envoi aux participants
            foreach ($sortie->getListParticipant() as $participant) {
                try {
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
                    $count++;
                } catch (\Throwable $e) {
                    $output->writeln('<error>Erreur envoi à '.$participant->getEmail().' : '.$e->getMessage().'</error>');
                }
            }

            // Envoi à l’organisateur
            $organisateur = $sortie->getIdOrganisateur();
            if ($organisateur && $organisateur->getEmail()) {
                try {
                    $email = (new Email())
                        ->from('no-reply@campus-eni.fr')
                        ->to($organisateur->getEmail())
                        ->subject('Rappel : ' . $sortie->getNom())
                        ->text(sprintf(
                            "Bonjour %s,\nVotre sortie « %s » est prévue le %s.",
                            $organisateur->getNom(),
                            $sortie->getNom(),
                            $sortie->getDateHeureDebut()->format('d/m/Y H:i')
                        ));

                    $this->mailer->send($email);
                    $count++;
                } catch (\Throwable $e) {
                    $output->writeln('<error>Erreur envoi à l’organisateur '.$organisateur->getEmail().' : '.$e->getMessage().'</error>');
                }
            }

            // Marquer la sortie comme rappel envoyé
            $sortie->setRappelEnvoye(true);
        }

        $this->em->flush();

        $output->writeln(sprintf('<info>%d rappels envoyés avec succès.</info>', $count));

        return Command::SUCCESS;
    }

}
