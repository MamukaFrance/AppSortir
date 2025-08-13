<?php

namespace App\Event;

use App\Entity\Sortie;
use Symfony\Contracts\EventDispatcher\Event;

class SortieInscriptionEvent extends Event
{
    public function __construct(private Sortie $sortie) {}

    public function getSortie(): Sortie
    {
        return $this->sortie;
    }
}
