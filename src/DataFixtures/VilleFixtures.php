<?php

namespace App\DataFixtures;

use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VilleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void {

        $sites = ['Nantes', 'Rennes', 'Quimper', 'Niort'];
        foreach ($sites as $site) {
            $campus = new Campus();
            $campus->setNom($site);
            $manager->persist($campus);
        }

        $manager->flush();
    }
}
