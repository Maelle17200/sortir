<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $etats = ['En création', 'Ouverte', 'Clôturée', 'En cours', 'Terminée', 'Annulée', 'Historisée'];

        $compteur = 0;
        foreach ($etats as $e) {
            $compteur++;
            $etat = new Etat();
            $etat->setLibelle($e);
            $manager->persist($etat);

            if(!$this->hasReference('etat'.$compteur, Etat::class)){
                $this->addReference('etat'.$compteur, $etat);
            }
        }
        $manager->flush();
    }
}
