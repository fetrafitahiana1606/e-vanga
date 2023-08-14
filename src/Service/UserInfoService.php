<?php
// src/Service/UserInfoService.php

namespace App\Service;

use Symfony\Component\Security\Core\Security;

class UserInfoService
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getUserInfo()
    {
        // Récupère l'utilisateur connecté
        $user = $this->security->getUser();;

        // Vérifie que l'utilisateur est bien connecté
        if (!$user) {
            return null;
        }

        // Renvoie un tableau contenant les informations de l'utilisateur
        return $user;
    }
}
