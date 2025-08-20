<?php

namespace App\Message;

class ChangeStatusMessage
{
    public function __construct(
        private int $sortieId,
        private string $contenu,
    ) {}

    public function getSortieId(): int
    {
        return $this->sortieId;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

}