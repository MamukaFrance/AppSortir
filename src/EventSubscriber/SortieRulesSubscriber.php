<?php

namespace App\EventSubscriber;

use App\Event\SortieInscriptionEvent;
use App\Entity\Sortie;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SortieRulesSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // On écoute notre événement métier d'inscription
        return [
            SortieInscriptionEvent::class => 'onSortieInscription',
        ];
    }

    /**
     * Règle #1 : empêcher l’inscription après la date limite
     */
    public function onSortieInscription(SortieInscriptionEvent $event): void
    {
        $sortie = $event->getSortie();

        // Si pas de date limite, on laisse passer
        $limit = $sortie->getDateLimiteInscription();
        if (!$limit) {
            return;
        }

        $now = new \DateTimeImmutable();
        if ($now > $limit) {
            // Tu peux aussi lever une HttpException 403 si tu préfères
            throw new \RuntimeException("⛔ Inscriptions fermées pour cette sortie.");
        }
    }

}
