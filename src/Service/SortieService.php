<?php

namespace App\Service;

use App\Repository\SortieRepository;

class SortieService
{
public function __construct(
    private SortieRepository $sortieRepository
    ) {}
    public function listbysite(int $id)
    {
     $sorties = $this->sortieRepository->findBySite(['site' => $id]);
     return $sorties;
    }
}