<?php

// src/Controller/SecurityController.php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Gère les erreurs de connexion
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier nom d'utilisateur (email) saisi par l'utilisateur
        $lastEmail = $authenticationUtils->getLastUsername();

        // Passe les informations nécessaires à la vue (template) de connexion
        return $this->render('security/login.html.twig', [
            'last_username' => $lastEmail,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // Cette méthode sera gérée par Symfony lui-même grâce à la configuration dans security.yaml
        throw new \Exception('This should never be reached!');
    }
}
