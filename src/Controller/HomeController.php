<?php

namespace App\Controller;

use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{


    #[Route('/home', name: 'home')]
    public function accueil(
        SortieRepository $sortieRepository,
        UtilisateurRepository $repoUser,
        SiteRepository $siteRepository,
        EtatRepository $etatRepository,

    ): Response

    {

        $sites= $siteRepository->findAll();



        $listeSorties = $sortieRepository->findAll();
        $userSession = $this->getUser()->getUserIdentifier();
        $utilisateur = $repoUser->findOneBy(["email" => $userSession]);
        $participant = $utilisateur->getIdParticipant();
        $listeSortiesParticipant = $participant ->getSortiesParticipant();
     //   $etat = $etatRepository->findAll();


        return $this->render('home/index.html.twig',
        ['listeSorties'=>$listeSorties,
         'listeSortiesParticipant'=>$listeSortiesParticipant,
         'sites'=>$sites,
          'participant' =>$participant

        ]
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
            or ($nbinscrit <=$sortie->getNbInscriptionsMax() and $date_du_jour < $sortie->getDateLimiteInscription() and in_array($participant, $sortie->getInscrits(), TRUE))

        ) {

            try {


                $sortie->addInscritOuSuppression($participant);
                $entityManager->persist($participant);
                $entityManager->flush();
                $this->addFlash('success', 'félicitation vous êtes inscrit');

            } catch (Exception $e) {

            }

        } else {

            $this->addFlash('error', 'Vous ne pouvez pas vous inscrire');
        }

        return $this->redirectToRoute('home');
    }

    #[Route('/home/filtres', name: 'filtres')]
    public function filtres(
        SiteRepository $siteRepository,
        UtilisateurRepository $repoUser,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,


    ): Response

    {
        if (isset($_POST['rechercher'])) {

            $utilisateur = $this->getUser()->getUserIdentifier();
            $user = $repoUser->findOneBy(["email" => $utilisateur]);
            $participant = $user->getIdParticipant();


            //site universitaire
            $nomdusite = filter_input(INPUT_POST, 'selection_du_site', FILTER_SANITIZE_STRING);

            $site=null;
            if ($nomdusite!= "ok") {
            $site = $siteRepository->findOneBy(['nom' => $nomdusite]);

        }

            //input search
            $textsearch = filter_input(INPUT_POST, 'barre_recherche', FILTER_SANITIZE_STRING);


            // input date début inférieur
            $date_entree_datetime=null;
            $date_entree = filter_input(INPUT_POST, 'dateEntree', FILTER_SANITIZE_STRING);

            if ($date_entree != "") {
                $date_entree_datetime = $date_entree;
            }
            //input date début supérieur
            $date_sortie_datetime=null;
            $sortie_terminee = filter_input(INPUT_POST, 'dateEt', FILTER_SANITIZE_STRING);

            if($sortie_terminee !="") {
                $date_sortie_datetime = $sortie_terminee;
            }

            //checkbox sortie dont je suis l'organisateur
            $organisateur = filter_input(INPUT_POST, 'organisateur', FILTER_SANITIZE_STRING);

            if($organisateur) {

               $organisateur=$participant;
            }

            //input sortie dont je suis le participant
            $sortie_inscrit = filter_input(INPUT_POST, 'inscrit', FILTER_SANITIZE_STRING);

            if ($sortie_inscrit){

                $sortie_inscrit = $participant;

            }

            //sortie ou je ne suis pas inscrit
            $sortie_non_inscrit = filter_input(INPUT_POST, 'nonInscrit', FILTER_SANITIZE_STRING);
            if ($sortie_non_inscrit){

                $sortie_non_inscrit=$participant;
            }

            //sortie déja terminée
            $sortie_qui_sont_terminees = filter_input(INPUT_POST, 'termine', FILTER_SANITIZE_STRING);
            if($sortie_qui_sont_terminees){
                $sortie_qui_sont_terminees= $etatRepository->findOneBy(['libelle'=>'Annulee']);
            }

            $listeSorties = $sortieRepository->filtres(
                                    $date_entree_datetime,
                                    $date_sortie_datetime,
                                    $textsearch,
                                    $organisateur,
                                    $sortie_inscrit,
                                    $site,
                                    $sortie_qui_sont_terminees,
                                    $sortie_non_inscrit,



            );
            $sites= $siteRepository->findAll();
            $listeSortiesParticipant = $participant ->getSortiesParticipant();

        }

        return $this->render('home/index.html.twig',
            ['listeSorties'=>$listeSorties,
                'listeSortiesParticipant'=>$listeSortiesParticipant,
                'sites'=>$sites,
                'participant' =>$participant

            ]
        );
    }

    #[Route('/sortie/annule/{id}', name: 'sortieannulee')]
    public function annulationSortie(
                                    $id,
                                    SortieRepository $sortieRepository,
                                    EtatRepository $etatRepository,
                                    EntityManagerInterface $em,
    ): Response

    {

        $sortie = $sortieRepository->findOneBy(['id'=>$id]);
        $etat = $etatRepository->findOneBy(['libelle'=>'Annulee']);
        $sortie->setEtat($etat);
        $em->persist($sortie);
        $em->flush();


        return $this->redirectToRoute('home');
    }

    #[Route('/sortie/supprimer/{id}', name: 'sortieSuppr')]
    public function suppressionSortie(
        $id,
        SortieRepository $sortieRepository,
        EntityManagerInterface $em,
    ): Response

    {

        $sortie = $sortieRepository->findOneBy(['id'=>$id]);

        if($sortie->getEtat()->getLibelle() == 'creee') {
                $em->remove($sortie);
                $em->flush();
        }
        return $this->redirectToRoute('home');
    }
    #[Route('/sortie/publier/{id}', name: 'publiersortie')]
    public function publierSortie(
        $id,
        SortieRepository $sortieRepository,
        EntityManagerInterface $em,
        EtatRepository $etatRepository,
    ): Response

    {

        $sortie = $sortieRepository->findOneBy(['id'=>$id]);
        $etat = $etatRepository->findOneBy(['libelle'=>'Ouverte']);
        $sortie->setEtat($etat);
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    #[Route('/sortie/details/{id}', name: 'details')]
    public function details(
        SiteRepository $siteRepository,
        UtilisateurRepository $repoUser,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,
        $id,
    ): Response

    {

        $detailssortie = $sortieRepository->findById($id);
        return $this->render('sortie/details.html.twig',
            compact('detailssortie'));
    }

}


