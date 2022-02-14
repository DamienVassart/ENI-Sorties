<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;

class SortieStateSetter
{
    public function updateState(Sortie $sortie) {
        $etatRepository = EtatRepository::class;
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
    }
}