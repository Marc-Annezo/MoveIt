<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Session $session, Request $request): Response
    {
         if ($this->getUser()) {

             return $this->redirectToRoute('home');
         }

        // Récupérer l'userIdentifier de session
        $valeurChamp = $request->request->get('email');
        $checkbox = $request->request->get('_remember_me');

        if($checkbox!=null) {

            setcookie('authMail', $valeurChamp, time() + 604800);
        }

         // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {


        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
