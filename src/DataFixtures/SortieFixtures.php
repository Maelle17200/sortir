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

        for ($i = 1; $i <= 40; $i++) {
            $sortie = new Sortie();

            $sortie->setNom($faker->sentence());
            
            $dateDebut = $faker->dateTimeBetween('now', '+1 month');
            $sortie->setDateHeureDebut(\DateTimeImmutable::createFromMutable($dateDebut));

            $sortie->setDuree($faker->numberBetween(30,300));

            $dateRegister = $faker->dateTimeBetween('now' ,\DateTime::createFromImmutable($sortie->getDateHeureDebut()));
            $sortie->setDateLimiteInscription(\DateTimeImmutable::createFromMutable($dateRegister));
            $sortie->setNbInscriptionMax($faker->numberBetween(1,50));
            $sortie->setInfosSortie($faker->paragraph(5));
            $organisateurId = $faker->numberBetween(1,20);
            $sortie->setOrganisateur($this->getReference('participant'.$organisateurId, Participant::class));
            //4 campus
            $sortie->setCampus($sortie->getOrganisateur()->getCampus());
            //7 états
            $sortie->setEtat($this->getReference('etat'.$faker->numberBetween(1,7), Etat::class));
            //10 lieux différents (cf LieuFixtures)
            $sortie->setLieu($this->getReference('lieu'.$faker->numberBetween(1,10), Lieu::class));

            //Tire au sort le nombre de participants à la sortie, puis crée le tableau des participants
            $nbParticipants = $faker->numberBetween(1, $sortie->getNbInscriptionMax());
            for ($j = 0; $j < $nbParticipants; $j++) {

                $participant = $this->getReference('participant'.$faker->numberBetween(1,20), Participant::class);

                // vérifie que le participant tiré au sort fait bien parie du même campus que la sortie
                if($participant->getCampus() == $sortie->getCampus()){
                    $sortie->addParticipant($participant);
                }

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
