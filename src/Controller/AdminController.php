<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Ville;
use App\Form\FormVilleType;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/gestionville', name: 'gestionville')]
    public function gestionville(Request $request,
                                 EntityManagerInterface $em,
                                 VilleRepository $villeRepo,

    ): Response

    {
        $listeVille = $villeRepo->findAll();

        $nvVille = new Ville();
        $ajoutVille = $this->createForm(FormVilleType ::class, $nvVille);
        $ajoutVille->handleRequest($request);

        if ($ajoutVille->isSubmitted() && $ajoutVille->isValid()){
            $em->persist($nvVille);
            $em->flush();

            return $this->redirectToRoute('admin_gestionville');
        }

        return $this->renderForm('admin/gestionville.html.twig',
            compact('listeVille', 'ajoutVille')
        );
    }

//   #[Route('/ajouterville', name: 'ajouterville')]
//    public function ajouterville(Request $request,
//                                 EntityManagerInterface $em,
//                                 VilleRepository $villeRepo,
//
//    ): Response
//
//    {
//
//        return $this->render('admin/gestionville.html.twig',
//
//        );
//
//    }

 /*   #[Route('/modifierville', name: 'modifierville')]
    public function modifierville(Request $request,
                                  EntityManagerInterface $em,
                                  VilleRepository $villeRepo,
    ) : Response

    {
        // récupération de la ville


        // création du formulaire de modification de la ville
        $form = $this->createForm(FormVilleType ::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())

        $modifVille = $form -> getData();
        $modifVille -> setNom('nom');
        $modifVille -> setCodePostal('CodePostal');
        $em -> persist ($modifVille);
        $em -> flush();

        $listeVille = $villeRepo->findAll();
        return $this->render('admin/gestionville.html.twig',
            compact('listeVille')
        );
    }

    #[Route('/supprimerville', name: 'supprimerville')]
    public function supprimerville(Request $request,
                                   VilleRepository $villeRepo,
                                   EntityManagerInterface $em,
    ) : Response

    {
        $supprimerville = $this.
        $em -> remove();

        $listeVille = $villeRepo->findAll();
        return $this->render('admin/gestionville.html.twig',
            compact('listeVille')
        );
    }

    #[Route('/gestionsite', name: 'gestionsite')]
    public function gestionsite(
        SiteRepository $siteRepo,
    ): Response

    {
        $listeSite = $siteRepo->findAll();

        return $this->render('admin/gestionsite.html.twig',
            compact('listeSite')
        );
    }

    #[Route('/ajoutersite', name: 'ajoutersite')]
    public function ajoutersite(
        SiteRepository $siteRepo,
        EntityManagerInterface $em,
        Request $request,
    ): Response

    {
        $nvSite = new Site();
        dd($request);

        $listeSite = $siteRepo->findAll();

        return $this->render('admin/gestionsite.html.twig',
            compact('listeSite')
        );
    } */
}
