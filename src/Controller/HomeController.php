<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{


    #[Route('/home', name: 'home')]
    public function accueil(
        SortieRepository $sortieRepository,

    ): Response

    {
    $listeSorties = $sortieRepository->findAll();

        return $this->render('home/index.html.twig',
        compact('listeSorties')

        );
    }

    #[Route('/check', name: 'check')]
    public function check(
        UtilisateurRepository $repoUser
    ): Response

    {
        $utilisateur = $this->getUser()->getUserIdentifier();
        $user = $repoUser->findOneBy(["email"=>$utilisateur]);
        $participant = $user->getIdParticipant();

        if($participant->getNom() == null or $participant->getPrenom() == null or $participant->getTelephone() == null or $participant->getSite() == null){
            return $this->redirectToRoute('MonProfil');
        } else {
            return $this->redirectToRoute('home');
        }

        return $this->render('home/index.html.twig');
    }

    #[Route('/inscrire/{idsortie}', name: 'inscriptionsortie')]
    public function inscriptionSortie(
        $idsortie,
        UtilisateurRepository $repoUser,
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager

    ): Response

    {

        // Obtenir le participant
        $userSession = $this->getUser()->getUserIdentifier();
        $utilisateur = $repoUser->findOneBy(["email" => $userSession]);
        $participant = $utilisateur->getIdParticipant();

        //trouver la sortie dans la base de donnée
        $sortie = $sortieRepository->findOneBy(['id' => $idsortie]);

        //ajouter l'utilisateur a la sortie
        //ou le supprimer
        $nbinscrit = count($sortie->getInscrits());
        $date_du_jour = new DateTime('now');


        // verifier que le nombre max d'inscrit n'est pas dépassé
        // verifier que la date du jour est inferieur a la date de fin d'inscription

        if (($nbinscrit < $sortie->getNbInscriptionsMax() and $date_du_jour < $sortie->getDateLimiteInscription())
            or ($date_du_jour < $sortie->getDateLimiteInscription() and in_array($participant, $sortie->getInscrits(), TRUE))

        ) {

            try {
                $entityManager->persist($sortie->addInscritOuSuppression($participant));

                $entityManager->flush();
                $this->addFlash('success', 'félicitation vous êtes inscrit');

            } catch (Exception $e) {


            }

        } else {

            $this->addFlash('error', 'Vous ne pouvez pas vous inscrire');
        }

        return $this->render('home/index.html.twig');
    }

}


