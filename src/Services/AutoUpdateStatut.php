<?php

namespace App\Services;

use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SortieRepository;
use \Datetime;

class AutoUpdateStatut
{

    protected $entityManager;
    protected $sortieRepo;
    protected $etatRepo;
    protected $now;


    public function __construct
    (
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepo,
        EtatRepository $etatRepo,

    )
    {
            $this->entityManager = $entityManager;
            $this->sortieRepo = $sortieRepo;
            $this->etatRepo = $etatRepo;
    }

    function miseAJour($listeSortie){

        // Récupération de liste des sorties pour l'affichage

        $now = new Datetime();

        foreach ($listeSortie as $sortie){

            $etatId = $this->etatRepo->findOneBy(['id' => $sortie->getEtat()->getId()]);
            $etatId = $etatId->getId();


            // Protection des statuts creee et Annulee

                if($etatId >= 2 and $etatId <= 5){

                // Passage en statut 3 si dépassement du nombre d'inscrit max ou dépassement de la date limite d'inscription
                if((count($sortie->getInscrits()) >= $sortie->getnbInscriptionsMax()) or ($now >= $sortie->getdateLimiteInscription())){

                    $nvEtat = $this->etatRepo->findOneBy(['id' => 3]);
                    $sortie->setEtat($nvEtat);
                }

                if((count($sortie->getInscrits()) < $sortie->getnbInscriptionsMax()) and ($now < $sortie->getdateLimiteInscription())){
                    $nvEtat = $this->etatRepo->findOneBy(['id' => 2]);
                    $sortie->setEtat($nvEtat);
                }

                if($now >= $sortie->getdateHeureDebut() and $now <= $sortie->getdateHeureFin()){
                    $nvEtat = $this->etatRepo->findOneBy(['id' => 4]);
                   $sortie->setEtat($nvEtat);
               }

                if($now >= $sortie->getdateHeureFin()){
                    $nvEtat = $this->etatRepo->findOneBy(['id' => 5]);
                    $sortie->setEtat($nvEtat);
                }
            }


            }
        }


    // Il faut exclure les statuts id 1 et 6

    // Il faut update du statut 2 vers le statut 3


    // Il faut update vers le statut 4


    // Il faut update vers le statut 5
}