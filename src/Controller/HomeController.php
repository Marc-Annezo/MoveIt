<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{


    #[Route('/home', name: 'home')]
    public function accueil(): Response

    {
        return $this->render('home/index.html.twig',

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
        $utilisateur = $repoUser->findOneBy(["email"=>$userSession]);
        $participant = $utilisateur->getIdParticipant();

        //trouver la sortie dans la base de donnée
        $sortie = $sortieRepository->findOneBy(['id'=>$idsortie]);

        //ajouter l'utilisateur a la sortie
        //ou le supprimer



        $entityManager->persist($sortie->addInscrit($participant));
        $entityManager->flush();




        return $this->render('home/index.html.twig');
    }




}


