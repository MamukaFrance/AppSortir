<?php

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class MailService
{
    private MailerInterface $mailer;
    private Security $security;
    public function __construct(MailerInterface $mailer, Security $security)
    {
        $this->mailer = $mailer;
        $this->security = $security;
    }

    public function sendEmailInscription(): void
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw new \LogicException('Utilisateur non connecté.');
        }

        $email = (new Email())
            ->from('appsortir@campus-eni.fr')
            ->to($user->getEmail())
            ->subject('Confirmation d\'inscription')
            ->text('Vous êtes inscrit à la sortie !')
            ->html('<p>Bonjour,</p><p>Vous êtes inscrit à la sortie !</p>');

        $this->mailer->send($email);
    }

    public function sendEmailDesInscription(): void
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw new \LogicException('Utilisateur non connecté.');
        }

        $email = (new Email())
            ->from('appsortir@campus-eni.fr')
            ->to($user->getEmail())
            ->subject('Confirmation desinscription')
            ->text('Vous êtes desinscrit à la sortie !')
            ->html('<p>Bonjour,</p><p>Vous n\'êtes pas inscrit à la sortie !</p>');

        $this->mailer->send($email);
    }

}