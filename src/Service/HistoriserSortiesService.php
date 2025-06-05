<?php

namespace App\Service;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class HistoriserSortiesService
{
    private SortieRepository $sortieRepository;
    private EntityManagerInterface $em;

    public function __construct(SortieRepository $sortieRepository, EntityManagerInterface $em)
    {
        $this->sortieRepository = $sortieRepository;
        $this->em = $em;
    }

    public function historiserSorties(): void
    {
        $unMoisAvant = new \DateTime('-1 month');

        $sorties = $this->sortieRepository->createQueryBuilder('s')
            ->innerJoin('s.etat', 'etat')
            ->where('s.dateHeureDebut < :unMoisAvant')
            ->andWhere('etat.libelle != :historise')
            ->setParameter('unMoisAvant', $unMoisAvant)
            ->setParameter('historise', 'Historisée')
            ->getQuery()
            ->getResult();

        if (count($sorties) === 0) {
            return;
        }


        $etatHistorise = $this->em->getRepository(Etat::class)->findOneBy(['libelle' => 'Historisée']);
        if (!$etatHistorise) {
            throw new \Exception('État "Historisée" introuvable.');
        }


        foreach ($sorties as $sortie) {
            $sortie->setEtat($etatHistorise);
        }

        $this->em->flush();
    }
}