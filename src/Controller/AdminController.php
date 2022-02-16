<?php

namespace App\Controller;


use App\Entity\Ville;
use App\Form\FormVilleType;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/ajouterville', name: 'ajouterville')]
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
            return $this->redirectToRoute('admin_ajouterville');
        }

        return $this->renderForm('admin/gestionville.html.twig',
            compact('listeVille', 'ajoutVille')
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/modifierville/{id}', name: 'modifierville')]
    public function modifierville(
        Request $request,
        EntityManagerInterface $em,
        VilleRepository $villeRepository,
        $id

    ) : Response

    {

        if (isset($_POST['modifiertype'])) {

            $villeAModif = $villeRepository->findOneBy(['id' => $id]);


        $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
        $codePostal = filter_input(INPUT_POST, 'codePostal', FILTER_SANITIZE_STRING);


        $villeAModif->setNom($nom);
        $villeAModif->setCodePostal($codePostal);

        $em->persist($villeAModif);
        $em->flush();

    }
        return $this->redirectToRoute('admin_ajouterville');
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/supprimerville/{id}', name: 'supprimerville')]
    public function supprimerville(
        Request $request,
        EntityManagerInterface $em,
        VilleRepository $villeRepository,
        $id

    ) : Response

    {
       $villeASuppr = $villeRepository->findOneBy(['id' => $id]);

       $em->remove($villeASuppr);
       $em->flush();

        return $this->redirectToRoute('home'
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
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
