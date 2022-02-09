<?php

namespace App\Controller;

use App\Entity\Site;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/gestionville', name: 'gestionville')]
    public function gestionville(): Response
    {
        return $this->render('admin/gestionville.html.twig', [
            'controller_name' => 'AdminController',
        ]);
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
    }
}
