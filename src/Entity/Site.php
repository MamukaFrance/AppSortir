<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'idSite')]
    private Collection $ListSortie;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'idSite')]
    private Collection $ListParticipant;

    public function __construct()
    {
        $this->ListSortie = new ArrayCollection();
        $this->ListParticipant = new ArrayCollection();
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
            $listSortie->setIdSite($this);
        }

        return $this;
    }

    public function removeListSortie(Sortie $listSortie): static
    {
        if ($this->ListSortie->removeElement($listSortie)) {
            // set the owning side to null (unless already changed)
            if ($listSortie->getIdSite() === $this) {
                $listSortie->setIdSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getListParticipant(): Collection
    {
        return $this->ListParticipant;
    }

    public function addListParticipant(User $listParticipant): static
    {
        if (!$this->ListParticipant->contains($listParticipant)) {
            $this->ListParticipant->add($listParticipant);
            $listParticipant->setIdSite($this);
        }

        return $this;
    }

    public function removeListParticipant(User $listParticipant): static
    {
        if ($this->ListParticipant->removeElement($listParticipant)) {
            // set the owning side to null (unless already changed)
            if ($listParticipant->getIdSite() === $this) {
                $listParticipant->setIdSite(null);
            }
        }

        return $this;
    }
}
