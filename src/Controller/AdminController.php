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
        // récupère la liste des villes
        $listeVille = $villeRepo->findAll();

        // Ajout d'une nouvelle ville
        // instancie une nouvelle ville
        $nvVille = new Ville();

        // créer le formulaire d'ajout ville
        $ajoutVille = $this->createForm(FormVilleType ::class, $nvVille);
        $ajoutVille->handleRequest($request);

        // validation du formulaire et envoi à la DB
        if ($ajoutVille->isSubmitted() && $ajoutVille->isValid()){
            $em->persist($nvVille);
            $em->flush();

        // redirection vers la page de gestionnaire des villes
            return $this->redirectToRoute('admin_gestionville');
        }

        return $this->renderForm('admin/gestionville.html.twig',
            compact('listeVille', 'ajoutVille')
        );
    }

    #[Route('/modifierville', name: 'modifierville')]
    public function modifierville(Request $request,
                                  EntityManagerInterface $em

    ) : Response

    {
        $id = $request->request->get('id');
        $ville = $em->getRepository(Ville::class)->find($id);

        // création du formulaire de modification de la ville
        $modifVille = $this->createForm(FormVilleType ::class, $ville);
        $modifVille->handleRequest($request);

        // validation du formulaire et envoi à la DB
        if ($modifVille->isSubmitted() && $modifVille->isValid()
        ) {
            return $this->redirectToRoute('admin_gestionville');
        }

        $ville -> setNom('nom');
        $ville -> setCodePostal('CodePostal');

        $em -> persist ($ville);
        $em -> flush();

        return $this->render('admin/modifierville.html.twig',
            ['listeVille'=>$modifVille]
        );
    }

    #[Route('/supprimerville', name: 'supprimerville')]
    public function supprimerville(Request                $request,
                                   EntityManagerInterface $em

    ) : Response

    {
        $id = $request->request->get('id');
        $ville = $em -> getRepository(Ville::class)->find($id);

        $em -> remove($ville);
        $em ->flush();

        return $this->redirectToRoute('admin/gestionville.html.twig'
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

//    #[Route('/ajoutersite', name: 'ajoutersite')]
//    public function ajoutersite(
//        SiteRepository $siteRepo,
//        EntityManagerInterface $em,
//        Request $request,
//    ): Response
//
//    {
//        $nvSite = new Site();
//        dd($request);
//
//        $listeSite = $siteRepo->findAll();
//
//        return $this->render('admin/gestionsite.html.twig',
//            compact('listeSite')
//        );
//    }
}
