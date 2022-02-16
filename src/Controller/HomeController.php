<?php

namespace App\Controller;

use App\Form\CreerSortieType;
use App\Repository\EtatRepository;

use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VilleRepository;
use App\Services\AutoUpdateStatut;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{


    #[Route('/home', name: 'home')]
    public function accueil(
        SortieRepository $sortieRepository,
        UtilisateurRepository $repoUser,
        SiteRepository $siteRepository,
        EtatRepository $etatRepository,
        AutoUpdateStatut $autoUpdateStatut,

    ): Response

    {

        $sites= $siteRepository->findAll();

        $listeSorties = $sortieRepository->findAll();
        $userSession = $this->getUser()->getUserIdentifier();
        $utilisateur = $repoUser->findOneBy(["email" => $userSession]);
        $participant = $utilisateur->getIdParticipant();
        $listeSortiesParticipant = $participant ->getSortiesParticipant();
     //   $etat = $etatRepository->findAll();

        $autoUpdateStatut->miseAJour($listeSorties);


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


        // Autoriser l'inscription uniquement quand la sortie est état 2

        if ($sortie->getEtat()->getLibelle() != "Ouverte") {

            return $this->redirectToRoute('home');

        } else {


            //ajouter l'utilisateur a la sortie
            //ou le supprimer
            $nbinscrit = count($sortie->getInscrits());
            $date_du_jour = new DateTime('now');


            // verifier que le nombre max d'inscrit n'est pas dépassé
            // verifier que la date du jour est inferieur a la date de fin d'inscription

            if (($nbinscrit < $sortie->getNbInscriptionsMax() and $date_du_jour < $sortie->getDateLimiteInscription())
                or ($nbinscrit <= $sortie->getNbInscriptionsMax() and $date_du_jour < $sortie->getDateLimiteInscription() and $sortie->getInscrits()->contains($participant) )

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
    }

    #[Route('/home/filtres', name: 'filtres')]
    public function filtres(
        SiteRepository $siteRepository,
        UtilisateurRepository $repoUser,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,
        AutoUpdateStatut $autoUpdateStatut,


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

                $sortie_qui_sont_terminees= $etatRepository->findOneBy(['libelle'=>'passee']);
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
            $autoUpdateStatut->miseAJour($listeSorties);

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
        UtilisateurRepository $repoUser
    ): Response

    {
        $utilisateur = $this->getUser()->getUserIdentifier();
        $user = $repoUser->findOneBy(["email" => $utilisateur]);
        $participant = $user->getIdParticipant();
        $sortie = $sortieRepository->findOneBy(['id'=>$id]);
        if($participant==$sortie->getOrganisateur()) {


            $etat = $etatRepository->findOneBy(['libelle' => 'Annulee']);

            $motif = filter_input(INPUT_POST, 'motif', FILTER_DEFAULT);

            $sortie->setMotifAnnulation($motif);

            $sortie->setEtat($etat);
            $em->persist($sortie);
            $em->flush();
        }
        else{

            $this->addFlash('error', 'Vous ne pouvez pas faire cela');
        }

        return $this->redirectToRoute('home');
    }

    #[Route('/sortie/supprimer/{id}', name: 'sortieSuppr')]
    public function suppressionSortie(
        $id,
        SortieRepository $sortieRepository,
        EntityManagerInterface $em,
        UtilisateurRepository $repoUser
    ): Response

    {
        $utilisateur = $this->getUser()->getUserIdentifier();
        $user = $repoUser->findOneBy(["email" => $utilisateur]);
        $participant = $user->getIdParticipant();
        $sortie = $sortieRepository->findOneBy(['id'=>$id]);
            if ($participant == $sortie->getOrganisateur()) {
                if ($sortie->getEtat()->getLibelle() == 'creee') {
                    $em->remove($sortie);
                    $em->flush();
                }
            }
            else{
                $this->addFlash('error', 'Vous ne pouvez pas faire cela');
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


    #[Route('/sortie/modifier/{id}', name: 'sortieModif')]
    public function modifierSortie(
        SortieRepository $sortieRepository,
        $id,
        Request $request,
        VilleRepository $repoVille,
        EtatRepository $repoEtat,
        EntityManagerInterface $em,
        UtilisateurRepository $repoUser,
    ): Response

    {
       // $messageErreur = 'Vous ne pouvez modifier que les sorties dont vous êtes organisateur';

        // Récupération de l'ID de la personne qui veut modifier
        $Demandant = $repoUser->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $Demandant = $Demandant->getIdParticipant()->getId();

        // Récupération de l'id de l'organisateur de la sortie
        $sortie = $sortieRepository->findOneBy(['id' => $id]);
        $idOrganisateur = $sortie->getOrganisateur()->getId();

        // Création de la liste ville pour le formulaire
        $listeVille = $repoVille->findAll();

        // Création du formulaire
        $formModifSortie = $this->createForm(CreerSortieType::class, $sortie);
        $formModifSortie->handleRequest($request);


        if ($Demandant != $idOrganisateur) {
            return $this->redirectToRoute('home');
        } else {
            if ($formModifSortie->isSubmitted() && $formModifSortie->isValid())
            {

                $boutonclique = $formModifSortie->getClickedButton()->getName();

                if ($boutonclique == "creer") {
                    $etat = $repoEtat->findOneBy(['id' => 1]);
                    $sortie-> setEtat($etat);
                } elseif ($boutonclique == "publier") {
                    $etat = $repoEtat->findOneBy(['id' => 2]);
                    $sortie->setEtat($etat);
                }

                $em->persist($sortie);
                $em->flush();
            }


        }
        return $this->renderForm('sortie/modifSortie.html.twig',
            compact('sortie', 'formModifSortie', 'listeVille'));
    }

    #[Route('/aide', name: 'aide')]
    public function aide(): Response
    {
        return $this->render('home/aide.html.twig');
    }

}




