<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= 10; $i++) {
            $lieu = new Lieu();
            $lieu->setNom($faker->word());
            $lieu->setRue($faker->streetName());
            $lieu->setLatitude($faker->optional(60)->latitude());

            if($lieu->getLatitude()){
                $lieu->setLongitude($faker->longitude());
            }

            $lieu->setVille($this->getReference('ville'.$faker->numberBetween(1, 10), Ville::class));
            $manager->persist($lieu);

            if(!$this->hasReference('lieu'.$i, Lieu::class)){
                $this->addReference('lieu'.$i, $lieu);
            }


        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [VilleFixtures::class];
    }
}
