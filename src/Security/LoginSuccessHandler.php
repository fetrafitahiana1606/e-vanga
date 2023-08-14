<?php

namespace App\Security;

use App\Service\UserInfoService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    /**
     * @inheritDoc
     */
    use TargetPathTrait;

    private AuthorizationCheckerInterface $authorizationChecker;
    private RouterInterface $router;

    private UserInfoService $infoService;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, RouterInterface $router)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->router = $router;

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, ): Response
    {
        // Redirection vers app_dashboard si l'utilisateur a le rôle ROLE_ADMIN
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return new RedirectResponse($this->router->generate('app_dashboard'));
        }

        // Redirection vers app_home si l'utilisateur a le rôle ROLE_USER
        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->router->generate('app_home'));
        }

        // Si l'utilisateur n'a ni ROLE_ADMIN ni ROLE_USER, vous pouvez rediriger vers une page par défaut
        return new RedirectResponse($this->router->generate('app_login'));
    }
}