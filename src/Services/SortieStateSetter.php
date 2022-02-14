<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;

class SortieStateSetter
{
    public function updateState(
        Sortie $sortie,
        EtatRepository $etatRepository
    ) {
        $now = new \DateTime('now');
        $dateLimite = $sortie->getDateLimiteInscription();

        if($now > $dateLimite) {
            $etatCloture = $etatRepository->find(3);
            $sortie->setIdEtat($etatCloture);
        }

        $dateHeureDebut = $sortie->getDateHeureDebut();

        if($now >= $dateHeureDebut) {
            $etatEnCours = $etatRepository->find(4);
            $sortie->setIdEtat($etatEnCours);
        }

        $dureeSortie = $sortie->getDuree();
        $finSortie = $dateHeureDebut->add(new \DateInterval('PT' . $dureeSortie . 'M'));

        if($now > $finSortie) {
            $etatPassee = $etatRepository->find(5);
            $sortie->setIdEtat($etatPassee);
        }
    }
}