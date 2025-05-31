<?php

namespace App\DTO;

use App\Entity\Campus;
use App\Entity\Etat;
use Doctrine\Common\Collections\ArrayCollection;

class RechercheSortiesDTO
{
    private ?string $nom = null;
    private ?\DateTimeImmutable $dateHeureDebutRecherche = null;
    private ?\DateTimeImmutable $dateHeureFinRecherche = null;
    private ?Campus $campus = null;
    private ?Etat $etat = null;
    private Bool $userOrganisateur = false;
    private Bool $userInscrit = false;
    private Bool $userPasInscrit = false;
    private Bool $sortiesTerminees = false;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getDateHeureDebutRecherche(): ?\DateTimeImmutable
    {
        return $this->dateHeureDebutRecherche;
    }

    public function setDateHeureDebutRecherche(?\DateTimeImmutable $dateHeureDebutRecherche): void
    {
        $this->dateHeureDebutRecherche = $dateHeureDebutRecherche;
    }

    public function getDateHeureFinRecherche(): ?\DateTimeImmutable
    {
        return $this->dateHeureFinRecherche;
    }

    public function setDateHeureFinRecherche(?\DateTimeImmutable $dateHeureFinRecherche): void
    {
        $this->dateHeureFinRecherche = $dateHeureFinRecherche;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): void
    {
        $this->etat = $etat;
    }

    public function isUserOrganisateur(): bool
    {
        return $this->userOrganisateur;
    }

    public function setUserOrganisateur(bool $userOrganisateur): void
    {
        $this->userOrganisateur = $userOrganisateur;
    }

    public function isUserInscrit(): bool
    {
        return $this->userInscrit;
    }

    public function setUserInscrit(bool $userInscrit): void
    {
        $this->userInscrit = $userInscrit;
    }

    public function isUserPasInscrit(): bool
    {
        return $this->userPasInscrit;
    }

    public function setUserPasInscrit(bool $userPasInscrit): void
    {
        $this->userPasInscrit = $userPasInscrit;
    }

    public function isSortiesTerminees(): bool
    {
        return $this->sortiesTerminees;
    }

    public function setSortiesTerminees(bool $sortiesTerminees): void
    {
        $this->sortiesTerminees = $sortiesTerminees;
    }

    public function getParticipants(): ArrayCollection
    {
        return $this->participants;
    }

    public function setParticipants(ArrayCollection $participants): void
    {
        $this->participants = $participants;
    }

}