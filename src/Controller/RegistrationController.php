<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;

use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RegistrationController extends AbstractController
{
    #[Route('admin/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,UtilisateurRepository $utilisateurRepository): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $participant = new Participant();
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

            if($form->get('admin')->getData()===true){

                $user->setRoles(["ROLE_ADMIN"]);
            }else{
                $user->setRoles(["ROLE_VISITOR"]);
            }
            $entityManager->persist($user);
            $entityManager->flush();



            if(file_exists($request->files->get('registration_form')['my_csv'])) {
                $file = $request->files->get('registration_form')['my_csv'];
                $uploads_directory = $this->getParameter('uploads_directory');
                $filename = md5(uniqid()) . '.' . $file->guessExtension();

                $file->move(
                    $uploads_directory,
                    $filename
                );



                $file = 'uploads/'.$filename;
                $csvfilesextension = pathinfo($file, PATHINFO_EXTENSION);
                $normalizer = [new ObjectNormalizer()];

                $encoders = [
                    new CsvEncoder(),
                ];

                $serializer = new Serializer($normalizer,$encoders);
                $fileString = file_get_contents($file);

                $data = $serializer->decode($fileString, $csvfilesextension);

                foreach ($data as $row){
                    if (array_key_exists('email',$row) && !empty($row['email'])){

                        $user = $utilisateurRepository->findOneBy(['email'=>$row['email']]);


                     if(!$user){

                         $utilisateur = new Utilisateur();
                         $participant = new Participant();
                         $entityManager->persist($participant);
                         $entityManager->flush();

                         $utilisateur->setEmail($row['email'])
                             ->setPassword(
                                 $userPasswordHasher->hashPassword(
                                     $utilisateur,
                                     $row['password'])
                             )
                         ->setRoles(["ROLE_VISITOR"]);

                         $utilisateur->setIdParticipant($participant);
                         $entityManager->persist($utilisateur);
                         $entityManager->flush();





                     }



                    }






                }









            }



            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_register');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
