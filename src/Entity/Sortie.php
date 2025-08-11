<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $infosSortie = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateHeureDebut = null;

    #[ORM\Column(nullable: true)]
    private ?float $duree = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbInscriptionsMax = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateLimiteInscription = null;

    #[ORM\ManyToOne(inversedBy: 'ListSortie')]
    private ?Site $idSite = null;

    #[ORM\ManyToOne(inversedBy: 'ListOrganisateur')]
    private ?User $idOrganisateur = null;

    #[ORM\ManyToOne(inversedBy: 'ListSortie')]
    private ?Etat $idEtat = null;

    #[ORM\ManyToOne(inversedBy: 'ListSortie')]
    private ?Lieu $idLieu = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'ListSortie')]
    private Collection $ListParticipant;

    public function __construct()
    {
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

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(string $infosSortie): static
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeImmutable
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(?\DateTimeImmutable $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?float
    {
        return $this->duree;
    }

    public function setDuree(?float $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(?int $nbInscriptionsMax): static
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeImmutable
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(?\DateTimeImmutable $dateLimiteInscription): static
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getIdSite(): ?Site
    {
        return $this->idSite;
    }

    public function setIdSite(?Site $idSite): static
    {
        $this->idSite = $idSite;

        return $this;
    }

    public function getIdOrganisateur(): ?User
    {
        return $this->idOrganisateur;
    }

    public function setIdOrganisateur(?User $idOrganisateur): static
    {
        $this->idOrganisateur = $idOrganisateur;

        return $this;
    }

    public function getIdEtat(): ?Etat
    {
        return $this->idEtat;
    }

    public function setIdEtat(?Etat $idEtat): static
    {
        $this->idEtat = $idEtat;

        return $this;
    }

    public function getIdLieu(): ?Lieu
    {
        return $this->idLieu;
    }

    public function setIdLieu(?Lieu $idLieu): static
    {
        $this->idLieu = $idLieu;

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
        }

        return $this;
    }

    public function removeListParticipant(User $listParticipant): static
    {
        $this->ListParticipant->removeElement($listParticipant);

        return $this;
    }

}
