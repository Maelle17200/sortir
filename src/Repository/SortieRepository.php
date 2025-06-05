<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function getByRecherche
    (
        ?String $nom,
        ?\DateTimeImmutable $dateDebutRecherche,
        ?\DateTimeImmutable $dateFinRecherche,
        ?campus $campus,
        ?UserInterface $user,
        bool $userOrganisateur,
        bool $userInscrit,
        bool $userPasInscrit,
        bool $sortiesTerminees,
    )
    {
        $queryBuilder = $this->createQueryBuilder('sortie')
            ->innerJoin('sortie.etat', 'etat')
            ->andWhere('etat.libelle != :historise')
            ->setParameter('historise', 'Historisée');

        if($nom){
            $queryBuilder
                ->andWhere('sortie.nom LIKE :nom')
                ->setParameter(':nom','%'.$nom.'%');
        }

        if($campus){
            $queryBuilder
                ->andWhere('sortie.campus = :campus')
                ->setParameter('campus',$campus);
        }

        if($dateDebutRecherche<$dateFinRecherche && $dateDebutRecherche && $dateFinRecherche){
            $queryBuilder
                ->andWhere('sortie.dateHeureDebut >= :dateDebutRecherche')
                ->andWhere('sortie.dateHeureDebut <= :dateFinRecherche')
                ->setParameter('dateDebutRecherche', $dateDebutRecherche)
                ->setParameter('dateFinRecherche', $dateFinRecherche);
        }

        if($userOrganisateur){
            $queryBuilder
                ->andWhere('sortie.organisateur = :user')
                ->setParameter('user', $user->getId());
        }

        if($userInscrit && !$userPasInscrit){
            $queryBuilder
                ->andWhere(':user MEMBER OF sortie.participants')
                ->setParameter('user', $user->getId());
        }

        if($userPasInscrit && !$userInscrit){
            $queryBuilder
                ->andWhere(':user NOT MEMBER OF sortie.participants')
                ->setParameter('user', $user->getId());
        }

        if($sortiesTerminees){
            $queryBuilder
                //->innerJoin('sortie.etat', 'etat')  // Jointure entre Sortie et Etat
                ->andWhere('etat.libelle = :libelle')  // Filtrer par le libelle de l'état
                ->setParameter('libelle', "Terminée");
        }

        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

}
