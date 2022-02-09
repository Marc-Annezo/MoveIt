<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\CreerSortieType;
use App\Form\FormLieuType;
use App\Repository\EtatRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'sortie')]
    public function creerSortie(
        Request $request,
        EntityManagerInterface $em,
        UtilisateurRepository $repoUser,
        EtatRepository $repoEtat,
    ): Response
    {

        // Récupération de l'id de l'organisateur :
        $utilisateur = $this->getUser()->getUserIdentifier();
        $user = $repoUser->findOneBy(["email"=>$utilisateur]);
        $participant = $user->getIdParticipant();

        // Création d'une nouvelle sortie
        $nouvelleSortie = new Sortie();

        // Définition de l'organisateur en récupérant le participant.id
        $nouvelleSortie->setOrganisateur($participant);

        // Définition du site en fonction du site de rattachement du participant
        $nouvelleSortie->setSite($participant->getSite());

        // Création du formulaire
        $creerSortie = $this->createForm(CreerSortieType::class, $nouvelleSortie);
        $creerSortie->handleRequest($request);

        // Récupération des données
        if($creerSortie->isSubmitted() && $creerSortie->isValid()){

            // Adaptation du comportement en fonction du bouton cliqué (creer ou publier)
            $boutonclique = $creerSortie->getClickedButton()->getName();
            if($boutonclique =="creer"){
                $etat = $repoEtat->findOneBy(['id'=>1]);
                $nouvelleSortie->setEtat($etat);
            } elseif($boutonclique == "publier") {
                $etat = $repoEtat->findOneBy(['id'=>2]);
                $nouvelleSortie->setEtat($etat);
            }

            // Insertion en BDD
            $em->persist($nouvelleSortie);
            $em->flush();
        }

        return $this->renderForm('sortie/index.html.twig',
            compact('creerSortie')
        );
    }
}
