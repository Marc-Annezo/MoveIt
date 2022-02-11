<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Utilisateur;
use App\Form\FormParticipantType;
use App\Form\ModifierMdpType;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class UtilisateurController extends AbstractController
{
    #[Route('utilisateur/profil', name: 'MonProfil')]
    public function utilisateur(Request                $request,
                                EntityManagerInterface $entityManager,
                                ParticipantRepository  $participantRepository,
                                UtilisateurRepository  $utilisateurRepository
    ): Response
    {
        // Récupération de l'utilisateur
        $userSession = $this->getUser()->getUserIdentifier();
        $utilisateur = $utilisateurRepository->findOneBy(['email' => $userSession]);

        // Création du formulaire 'première connexion' du participant
        $participant = $utilisateur->getIdParticipant();


        $formParticipant = $this->createForm(FormParticipantType::class, $participant);
        $formParticipant->handleRequest($request);


        if ($formParticipant->isSubmitted() && $formParticipant->isValid()) {

            if(file_exists($request->files->get('form_participant')['my_file'])) {
                $file = $request->files->get('form_participant')['my_file'];
                $uploads_directory = $this->getParameter('uploads_directory');
                $filename = md5(uniqid()) . '.' . $file->guessExtension();

                $file->move(
                    $uploads_directory,
                    $filename
                );

                $participant->setImage($filename);
            }
            $participant->setIdUtilisateur($utilisateur);
            $entityManager->persist($participant);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('MonProfil');
        }


        return $this->render('utilisateur/profil.html.twig', [
            'monProfilFormParticipant' => $formParticipant->createView(),
                'participant'=>$participant
        ]);
    }

    #[Route('/modifiermdp', name: 'modifiermdp')]
    public function modifiermdp(
        EntityManagerInterface      $entityManager,
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UtilisateurRepository       $userRepo,
    ): Response
    {
        // Récupération de l'utilisateur
        $user = $this->getUser()->getUserIdentifier();
        $user = $userRepo->findOneBy(['email' => $user]);

        // Création du formulaire de modification
        $form = $this->createForm(ModifierMdpType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupération du participant correspond à l'id utilisateur
            $participant = $user->getIdParticipant();
            dd($participant->getSite());
            $entityManager->persist($participant);
            $entityManager->flush();


            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setIdParticipant($participant);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('home');
        }

        return $this->render('utilisateur/resetmdp.html.twig', [
            'form' => $form->createView(),
        ]);

        return $this->render('utilisateur/resetmdp.html.twig',

        );
    }


    #[Route('utilisateur/profil/{id}', name: 'modifierProfil')]
    public function modifierProfil(Request $request,
                                EntityManagerInterface $entityManager,
                                ParticipantRepository $participantRepository,
                                $id,
    ): Response
    {
        // Récupération de l'utilisateur
        $participantModifie = $participantRepository->findOneBy($id);

        // Création du formulaire 'modification profil' du participant
        $formParticipant = $this->createForm(FormParticipantType::class, $participantModifie);
        $formParticipant->handleRequest($request);


        if ($formParticipant->isSubmitted() && $formParticipant->isValid()) {
            $entityManager->persist($participantModifie);
            $entityManager->flush();

            // do anything else you need here, like send an email
            return $this->redirectToRoute('MonProfil');
        }


        return $this->render('utilisateur/profil.html.twig', [
            'monProfilFormParticipant' => $formParticipant -> createView(),
        ]);
    }

}
