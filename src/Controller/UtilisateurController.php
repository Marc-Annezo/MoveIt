<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Utilisateur;
use App\Form\FormParticipantType;
use App\Repository\ParticipantRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UtilisateurController extends AbstractController
{
    #[Route('utilisateur/profil', name: 'MonProfil')]
    public function utilisateur(Request $request,
                                EntityManagerInterface $entityManager,
                                ParticipantRepository $participantRepository,
    UtilisateurRepository $utilisateurRepository
    ): Response
    {
        $userSession = $this->getUser()->getUserIdentifier();
        $utilisateur = $utilisateurRepository->findOneBy(['email' => $userSession]);
        $participant = $utilisateur->getIdParticipant();


        $form = $this->createForm(FormParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $participant = new Participant();
            $entityManager->persist($participant);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('MonProfil');
        }







        return $this->render('utilisateur/profil.html.twig', [
            'monProfilForm' => $form -> createView()
        ]);
    }
}
