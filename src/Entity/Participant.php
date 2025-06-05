<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

//quand contraintes sur 2 attribut => contrainte sur l'attribut directement
#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email existe déjà.')]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo existe déjà.')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Email(message: "Veuillez renseigner un email valide.")]
    #[Assert\NotBlank(message: "Veuillez renseigner un email.")]
    #[Assert\Length(max: 180, maxMessage: "Cet email est trop long, 180 caractères max.")]
    #[ORM\Column(length: 180, unique: true)]
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
    private ?string $motPasse = null;

    #[Assert\NotBlank(message: "Veuillez renseigner un pseudo.")]
    #[Assert\Length(
        min: 2,
        max: 180,
        minMessage: "Ce pseudo est trop court, 2 caractères min.",
        maxMessage: "Ce pseudo est trop long, 180 caractères max.",
    )]
    #[ORM\Column (length: 180, unique: true)]
    private ?string $pseudo = null;

    #[Assert\NotBlank(message: "Veuillez renseigner un nom.")]
    #[Assert\Length(
        min: 2,
        max: 180,
        minMessage: "Ce nom est trop court, 2 caractères min.",
        maxMessage: "Ce nom est trop long, 180 caractères max.",
    )]
    #[ORM\Column()]
    private ?string $nom = null;

    #[Assert\NotBlank(message: "Veuillez renseigner un prénom.")]
    #[Assert\Length(
        min: 2,
        max: 180,
        minMessage: "Ce prénom est trop court, 2 caractères min.",
        maxMessage: "Ce prénom est trop long, 180 caractères max.",
    )]
    #[ORM\Column]
    private ?string $prenom = null;

    #[Assert\NotBlank(message: "Veuillez renseigner un numéro de téléphone.")]
    #[Assert\Regex(
        pattern: '/^\d{10}$/',
        message: "Le numéro doit contenir 10 chiffres, sans tiret ni caractère spécial.",
    )]
    #[ORM\Column]
    private ?string $telephone = null;

    #[ORM\Column (nullable: true)]
    private ?string $imageURL = null;

    #[ORM\ManyToOne(targetEntity: Campus::class, inversedBy: "participants")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'participants')]
    private Collection $sorties;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'organisateur', orphanRemoval: true)]
    private Collection $sortiesOrganisees;



    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->sortiesOrganisees = new ArrayCollection();
    }

    public function getImageURL(): ?string
    {
        return $this->imageURL;
    }

    public function setImageURL(?string $imageURL): void
    {
        $this->imageURL = $imageURL;
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

    public function setTelephone(string $tel): static
    {
        $this->telephone = $tel;

        return $this;
    }

    
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email . ' - ' . $this->pseudo;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array    {

        return array_unique($this->roles);
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
        return $this->motPasse;
    }

    public function setPassword(string $password): static
    {
        $this->motPasse = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setPseudo(string $string): static
    {
        $this->pseudo = $string;
        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): static
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties->add($sorty);
            $sorty->addParticipant($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): static
    {
        if ($this->sorties->removeElement($sorty)) {
            $sorty->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisee(Sortie $sortiesOrganisee): static
    {
        if (!$this->sortiesOrganisees->contains($sortiesOrganisee)) {
            $this->sortiesOrganisees->add($sortiesOrganisee);
            $sortiesOrganisee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(Sortie $sortiesOrganisee): static
    {
        if ($this->sortiesOrganisees->removeElement($sortiesOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getOrganisateur() === $this) {
                $sortiesOrganisee->setOrganisateur(null);
            }
        }

        return $this;
    }

}
