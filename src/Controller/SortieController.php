<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\AjoutLieuType;
use App\Form\CreerSortieType;
use App\Form\FormLieuType;
use App\Repository\EtatRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie/', name: 'sortie')]
    public function creerSortie(
        Request                $request,
        EntityManagerInterface $em,
        UtilisateurRepository  $repoUser,
        EtatRepository         $repoEtat,
        VilleRepository        $repoVille,
    ): Response
    {

        //$test = $request->request->get('selectVille');

        // On récupère l'idVille


        // Récupération de la liste des villes :
        $listeVille = $repoVille->findAll();

        // Récupération de l'id de l'organisateur :
        $utilisateur = $this->getUser()->getUserIdentifier();
        $user = $repoUser->findOneBy(["email" => $utilisateur]);
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

        // On vérifie si on a une requête AJAX

        // Récupération des données
        if ($creerSortie->isSubmitted() && $creerSortie->isValid()) {

            // Adaptation du comportement en fonction du bouton cliqué (creer ou publier)
            $boutonclique = $creerSortie->getClickedButton()->getName();
            if ($boutonclique == "creer") {
                $etat = $repoEtat->findOneBy(['id' => 1]);
                $nouvelleSortie->setEtat($etat);
            } elseif ($boutonclique == "publier") {
                $etat = $repoEtat->findOneBy(['id' => 2]);
                $nouvelleSortie->setEtat($etat);
            }

            // Insertion en BDD
            $em->persist($nouvelleSortie);
            $em->flush();
        }

        return $this->renderForm('sortie/index.html.twig',
            compact('creerSortie', 'listeVille')
        );
    }

    #[Route('/ajouterLieu', name: 'ajouterlieu')]
    public function ajouterlieu(
Request $request,
        EntityManagerInterface $em,
    ): Response

    {
        $nvLieu = new Lieu();

        $formAjoutLieu = $this->createForm(AjoutLieuType::class, $nvLieu);
        $formAjoutLieu->handleRequest($request);

        if($formAjoutLieu->isSubmitted() && $formAjoutLieu->isValid()){

            $em->persist($nvLieu);
            $em->flush();

            return $this->redirectToRoute('sortie');
        }

        return $this->renderForm('sortie/ajoutlieu.html.twig',
            compact('formAjoutLieu')
        );
    }

    #[Route('/listeLieuVille/{id}', name: 'listeLieuVille')]
    public function listeLieuVille(
        Request $request,
        EntityManagerInterface $em,
        $id
    ): Response
    {
        $test = $request->request->get('_route_params');
        dd($test);

        return $this->render('home/index.html.twig');
    }}
