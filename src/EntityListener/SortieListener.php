<?php

namespace App\EntityListener;

use App\Entity\Sortie;
use App\Repository\EtatRepository;

class SortieListener
{

    public function __construct(private readonly EtatRepository $er)
    {

    }

    public function cloture(Sortie $sortie) : void {
        $etat = $this->er->findOneBy(['libelle' => "Clôturée"]);

        if ($sortie->getDateLimiteInscription() >= new \DateTime('now') || $sortie->getParticipants()->count() >= $sortie->getNbInscriptionMax()) {
            $sortie->setEtat($etat);
        }
    }

}