<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

//UniqueEntity et UniqueConstraint ensemble obligatoirement
//Pour le formulaire
#[UniqueEntity(fields: ['nom', 'campus', 'dateHeureDebut'], message: "La sortie existe déjà")]
//Pour la base
#[ORM\UniqueConstraint(columns: ['nom', 'campus_id', 'date_heure_debut'])]
#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Veuillez renseigner le nom de la sortie")]
    #[Assert\Length(
        min: 5,
        max: 180,
        minMessage: "Le nom est trop court. 5 caractères minimum.",
        maxMessage: "Le nom est trop long. 180 caractères maximum."
    )]
    #[ORM\Column(length: 180)]
    private ?string $nom = null;

    #[Assert\NotBlank(message: "Veuillez renseigner la date du début de la sortie")]
    #[ORM\Column]
    private ?\DateTimeImmutable $dateHeureDebut = null;

    #[Assert\NotBlank(message: "Veuillez renseigner la durée de la sortie")]
    #[Assert\Positive(message: "La durée doit être positive")]
    #[ORM\Column]
    private ?int $duree = null;

    #[Assert\NotBlank(message: "Veuillez renseigner la date limite pour s'inscrire")]
    #[ORM\Column]
    private ?\DateTimeImmutable $dateLimiteInscription = null;

    #[Assert\Positive(message: "Le nombre de participant doit être positif")]
    #[ORM\Column]
    private ?int $nbInscriptionMax = null;

    #[Assert\NotBlank(message: "Veuillez donner quelques informations sur la sortie")]
    #[Assert\Length(
        min: 20,
        max: 500,
        minMessage: "Le texte est trop court. 20 caractères minimum.",
        maxMessage: "Le text est trop long. 500 caractères maximum."
    )]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $infosSortie = null;

    #[ORM\ManyToOne(targetEntity: Etat::class, inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etat $etat = null;

    #[Assert\NotNull(message: "Veuillez sélectionner un lieu")]
    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lieu $lieu = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\ManyToMany(targetEntity: Participant::class, inversedBy: 'sorties')]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'sortiesOrganisees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $organisateur = null;

    #[Assert\Length(
        min: 10,
        max: 500,
        minMessage: "Le texte est trop court. 10 caractères minimum.",
        maxMessage: "Le text est trop long. 500 caractères maximum."
    )]
    #[ORM\Column]
    private ?String $motifAnnulation = null;

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): void
    {
        $this->motifAnnulation = $motifAnnulation;
    }

    public function __construct()
    {
        $this->participants = new ArrayCollection();
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

    public function getDateHeureDebut(): ?\DateTimeImmutable
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeImmutable $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeImmutable
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeImmutable $dateLimiteInscription): static
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionMax(): ?int
    {
        return $this->nbInscriptionMax;
    }

    public function setNbInscriptionMax(?int $nbInscriptionMax): static
    {
        $this->nbInscriptionMax = $nbInscriptionMax;

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

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

}
