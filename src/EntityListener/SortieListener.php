<?php

namespace App\EntityListener;

use App\Entity\Sortie;
use App\Repository\EtatRepository;

class SortieListener
{

    public function __construct(private readonly EtatRepository $er)
    {

    }

    public function postLoad(Sortie $sortie) : void {
        $etatClo = $this->er->findOneBy(['libelle' => "Clôturée"]);
        $etatOuv = $this->er->findOneBy(['libelle' => "Ouverte"]);
        $etatTerm = $this->er->findOneBy(['libelle' => "Terminée"]);

        if ($sortie->getDateLimiteInscription() <= new \DateTime('now') || $sortie->getParticipants()->count() >= $sortie->getNbInscriptionMax()) {
            $sortie->setEtat($etatClo);
        }

        if ($sortie->getEtat() == $etatClo && $sortie->getParticipants()->count() < $sortie->getNbInscriptionMax() && $sortie->getDateLimiteInscription() >= new \DateTime('now')) {
            $sortie->setEtat($etatOuv);
        }

        if($sortie->getDateHeureDebut() <= new \DateTime('now')){
            $sortie->setEtat($etatTerm);
        }
    }

}