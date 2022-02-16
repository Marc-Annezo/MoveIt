<?php

namespace App\Controller;


use App\Entity\Site;
use App\Entity\Ville;
use App\Form\FormSiteType;
use App\Form\FormVilleType;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
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
        LieuRepository $lieuRepository,
        SortieRepository $sortieRepo,
        ParticipantRepository $participantRepository,
        SiteRepository $siteRepository,
        $id

    ) : Response

    {
        $listeVille = $villeRepository->findAll();
        // Récupération de la ville à supprimer
        $villeASuppr = $villeRepository->findOneBy(['id' => $id]);

        // Récuperation des lieux relatifs à cette sortie (FK)
        $lieuxASuppri = $lieuRepository->lieuParIdVille($villeASuppr);
        $objetLieux = $lieuRepository->findOneBy(['idVille' => $villeASuppr]);

        // Récupération des sorties liées aux lieux (FK)
        $sortiesASupprm = $sortieRepo->findByLieu($lieuxASuppri);
        $objetsortie = $sortieRepo->findOneBy(['id' => $sortiesASupprm]);




        // Récupération de la liste de participant inscrits et
        if($objetsortie != null) {
            $listeParticipant = $objetsortie->getInscrits();
            if (!empty($listeParticipant)) {
                foreach ($listeParticipant as $participant) {
                    $objetsortie->removeInscrit($participant);
                }
            }

            $em->persist($objetsortie);
            $em->remove($objetsortie);
            $em->flush();
        }


        if($objetLieux != null) {
            $em->remove($objetLieux);
            $em->flush();
        }

        $em->remove($villeASuppr);
        $em->flush();


        return $this->redirectToRoute('admin_ajouterville');
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/ajoutersite', name: 'ajoutersite')]
    public function gestionsite(Request $request,
                                SiteRepository $siteRepo,
                                EntityManagerInterface $em
    ): Response

    {
        // récupère la liste des sites
        $listeSite = $siteRepo->findAll();

        // Ajout d'une nouvelle ville
        // instancie une nouvelle ville
        $nvSite = new Site();

        // créer le formulaire d'ajout de site
        $ajoutSite = $this->createForm(FormSiteType::class, $nvSite);
        $ajoutSite->handleRequest($request);

        // validation du formulaire et envoi à la DB
        if($ajoutSite->isSubmitted() && $ajoutSite->isValid()){
            $em->persist($nvSite);
            $em->flush();

        // redirection vers la page de gestionnaire de sites
            return $this->redirectToRoute('admin_ajoutersite');
        }

        return $this->render('admin/gestionsite.html.twig',
            compact('listeSite', 'ajoutSite')
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/modifiersite/{id}', name: 'modifiersite')]
    public function modifierSite(   Request $request,
                                    SiteRepository $siteRepo,
                                    EntityManagerInterface $em,
                                    $id
    ): Response

    {
        if (isset($_POST['modifiertype'])) {

            $siteAModifier = $siteRepo->findBy(['id' => $id]);

            $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);

        $siteAModifier->setNom($nom);
        $em->persist((object)$siteAModifier);
        $em->flush();
        }

        return $this->redirectToRoute('admin_ajoutersite');

    }








}
