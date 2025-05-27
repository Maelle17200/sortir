<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {

    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        //Crée un admin
        $admin = new Participant();
        $admin->setNom('Admin');
        $admin->setPrenom('Admin');
        $admin->setPseudo('Admin');
        $admin->setEmail('admin@sortir.com');
        $admin->setTelephone('0607080910');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, '123456'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setCampus($this->getReference('campus'.$faker->numberBetween(1,4), Campus::class));
        $manager->persist($admin);

        //Crée 20 users actifs
        for($i = 1; $i <= 20; $i++){
            $user = new Participant();
            $user->setNom($faker->lastName());
            $user->setPrenom($faker->firstName());
            $user->setPseudo('pseudo'.$i);
            $user->setEmail('user'.$i.'@sortir.com');
            $user->setTelephone('0607080910');
            $user->setPassword($this->passwordHasher->hashPassword($admin, '123456'));
            $user->setRoles(['ROLE_USER']);
            $user->setCampus($this->getReference('campus'.$faker->numberBetween(1,4), Campus::class));
            $manager->persist($user);
            //vérifie que la référence n'existe pas avant de l'ajouter, utilse si la boucle tourne plusieurs fois
            if(!$this->hasReference('participant'.$i, Participant::class)){
                $this->addReference('participant'.$i, $user);
            }


        }

        //Crée 2 users inactifs (sans rôle)
        for($i = 1; $i <= 2; $i++){
            $user = new Participant();
            $user->setNom($faker->lastName());
            $user->setPrenom($faker->firstName());
            $user->setPseudo('pseudoInactif'.$i);
            $user->setEmail('userInactif'.$i.'@sortir.com');
            $user->setTelephone('0607080910');
            $user->setPassword($this->passwordHasher->hashPassword($admin, '123456'));
            $user->setRoles(['']);
            $user->setCampus($this->getReference('campus'.$faker->numberBetween(1,4), Campus::class));
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CampusFixtures::class];
    }
}
