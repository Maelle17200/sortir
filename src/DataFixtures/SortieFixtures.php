<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= 20; $i++) {
            $sortie = new Sortie();

            $sortie->setNom($faker->sentence());
            
            $dateDebut = $faker->dateTimeBetween('now', '+1 month');
            $sortie->setDateHeureDebut(\DateTimeImmutable::createFromMutable($dateDebut));

            $duree = $faker->dateTimeBetween('+1 hour', '+8 hour');
            $sortie->setDuree(\DateTimeImmutable::createFromMutable($duree));

            $dateRegister = $faker->dateTimeBetween('now' ,\DateTime::createFromImmutable($sortie->getDateHeureDebut()));
            $sortie->setDateLimiteInscription(\DateTimeImmutable::createFromMutable($dateRegister));
            $sortie->setNbInscriptionMax($faker->optional(20)->numberBetween(1,50));
            $sortie->setInfosSortie($faker->paragraph(5));
            //4 campus
            $sortie->setCampus($this->getReference('campus'.$faker->numberBetween(1,4), Campus::class));
            //7 états
            $sortie->setEtat($this->getReference('etat'.$faker->numberBetween(1,7), Etat::class));
            //10 lieux différents (cf LieuFixtures)
            $sortie->setLieu($this->getReference('lieu'.$faker->numberBetween(1,10), Lieu::class));
            //20 participants actifs différents (cf ParticipantFixtures)
            $sortie->setOrganisateur($this->getReference('participant'.$faker->numberBetween(1,20), Participant::class));
            //Tire au sort le nombre de participants à la sortie, puis cré le tableau des participants
            $nbParticipants = $faker->numberBetween(1, $sortie->getNbInscriptionMax());
            for ($j = 0; $j < $nbParticipants; $j++) {
                $sortie->addParticipant($this->getReference('participant'.$faker->numberBetween(1,20), Participant::class));
            }

            $manager->persist($sortie);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [EtatFixtures::class, LieuFixtures::class, CampusFixtures::class, ParticipantFixtures::class];
    }
}
