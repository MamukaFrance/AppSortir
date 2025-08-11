<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
class Etat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'idEtat')]
    private Collection $ListSortie;

    public function __construct()
    {
        $this->ListSortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getListSortie(): Collection
    {
        return $this->ListSortie;
    }

    public function addListSortie(Sortie $listSortie): static
    {
        if (!$this->ListSortie->contains($listSortie)) {
            $this->ListSortie->add($listSortie);
            $listSortie->setIdEtat($this);
        }

        return $this;
    }

    public function removeListSortie(Sortie $listSortie): static
    {
        if ($this->ListSortie->removeElement($listSortie)) {
            // set the owning side to null (unless already changed)
            if ($listSortie->getIdEtat() === $this) {
                $listSortie->setIdEtat(null);
            }
        }

        return $this;
    }
}
