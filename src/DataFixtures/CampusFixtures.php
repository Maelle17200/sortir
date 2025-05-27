<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sites = ['Niort', 'Quimper', 'Nantes', 'Rennes'];
        $compteur = 0;
        foreach ($sites as $site) {
            $compteur++;
            $campus = new Campus();
            $campus->setNom($site);
            $manager->persist($campus);

            if(!$this->hasReference('campus'.$compteur, Campus::class))
            $this->addReference('campus'.$compteur, $campus);
        }

        $manager->flush();
    }

}
