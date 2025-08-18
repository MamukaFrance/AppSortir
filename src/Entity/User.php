<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Regex(
        pattern: '/@campus-eni\.fr$/',
        message: "L'adresse email doit se terminer par @campus-eni.fr."
    )]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column(length: 30)]
    private ?string $prenom = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(options:['default'=> false])]
    private ?bool $administrateur = null;

    #[ORM\Column(options:['default'=> true])]
    private ?bool $actif = null;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'idOrganisateur')]
    private Collection $ListOrganisateur;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'ListParticipant')]
    private Collection $ListSortie;

    #[ORM\ManyToOne(inversedBy: 'ListParticipant')]
    private ?Site $idSite = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $photo = null;

    /**
     * @var Collection<int, GroupePrive>
     */
    #[ORM\OneToMany(targetEntity: GroupePrive::class, mappedBy: 'createur')]
    private Collection $groupePrives;

    /**
     * @var Collection<int, GroupePrive>
     */
    #[ORM\ManyToMany(targetEntity: GroupePrive::class, mappedBy: 'participants')]
    private Collection $listeParticipantGroupePrive;

    public function __construct()
    {
        $this->administrateur = false;
        $this->actif = true;
        $this->ListOrganisateur = new ArrayCollection();
        $this->ListSortie = new ArrayCollection();
        $this->groupePrives = new ArrayCollection();
        $this->ListeParticipantGroupePrive = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function isAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): static
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getListOrganisateur(): Collection
    {
        return $this->ListOrganisateur;
    }

    public function addListOrganisateur(Sortie $listOrganisateur): static
    {
        if (!$this->ListOrganisateur->contains($listOrganisateur)) {
            $this->ListOrganisateur->add($listOrganisateur);
            $listOrganisateur->setIdOrganisateur($this);
        }

        return $this;
    }

    public function removeListOrganisateur(Sortie $listOrganisateur): static
    {
        if ($this->ListOrganisateur->removeElement($listOrganisateur)) {
            // set the owning side to null (unless already changed)
            if ($listOrganisateur->getIdOrganisateur() === $this) {
                $listOrganisateur->setIdOrganisateur(null);
            }
        }

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
            $listSortie->addListParticipant($this);
        }

        return $this;
    }

    public function removeListSortie(Sortie $listSortie): static
    {
        if ($this->ListSortie->removeElement($listSortie)) {
            $listSortie->removeListParticipant($this);
        }

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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection<int, GroupePrive>
     */
    public function getGroupePrives(): Collection
    {
        return $this->groupePrives;
    }

    public function addGroupePrife(GroupePrive $groupePrife): static
    {
        if (!$this->groupePrives->contains($groupePrife)) {
            $this->groupePrives->add($groupePrife);
            $groupePrife->setCreateur($this);
        }

        return $this;
    }

    public function removeGroupePrife(GroupePrive $groupePrife): static
    {
        if ($this->groupePrives->removeElement($groupePrife)) {
            // set the owning side to null (unless already changed)
            if ($groupePrife->getCreateur() === $this) {
                $groupePrife->setCreateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupePrive>
     */
    public function getListeParticipantGroupePrive(): Collection
    {
        return $this->ListeParticipantGroupePrive;
    }

    public function addListeParticipantGroupePrive(GroupePrive $listeParticipantGroupePrive): static
    {
        if (!$this->ListeParticipantGroupePrive->contains($listeParticipantGroupePrive)) {
            $this->ListeParticipantGroupePrive->add($listeParticipantGroupePrive);
            $listeParticipantGroupePrive->addParticipant($this);
        }

        return $this;
    }

    public function removeListeParticipantGroupePrive(GroupePrive $listeParticipantGroupePrive): static
    {
//
        if ($this->listeParticipantGroupePrive->removeElement($listeParticipantGroupePrive)) {
            $listeParticipantGroupePrive->removeParticipant($this);
        }
        return $this;
    }
}
