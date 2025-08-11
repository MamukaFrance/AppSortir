<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $codePostal = null;

    /**
     * @var Collection<int, Lieu>
     */
    #[ORM\OneToMany(targetEntity: Lieu::class, mappedBy: 'idVille')]
    private Collection $ListLieu;

    public function __construct()
    {
        $this->ListLieu = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection<int, Lieu>
     */
    public function getListLieu(): Collection
    {
        return $this->ListLieu;
    }

    public function addListLieu(Lieu $listLieu): static
    {
        if (!$this->ListLieu->contains($listLieu)) {
            $this->ListLieu->add($listLieu);
            $listLieu->setIdVille($this);
        }

        return $this;
    }

    public function removeListLieu(Lieu $listLieu): static
    {
        if ($this->ListLieu->removeElement($listLieu)) {
            // set the owning side to null (unless already changed)
            if ($listLieu->getIdVille() === $this) {
                $listLieu->setIdVille(null);
            }
        }

        return $this;
    }
}
