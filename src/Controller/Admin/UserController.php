<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserFormType;
use App\Service\Cart\CartService;
use App\Service\UserInfoService;
use App\Repository\UserRepository;
use App\Form\UserRegistrationFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/info', name: 'app_user_info')]
    public function userInfo(UserInfoService $userInfoService, CartService $cartService): Response
    {
        // Récupère l'utilisateur connecté
        $user = $userInfoService->getUserInfo();

        // Vérifie que l'utilisateur est bien connecté
        if (!$user) {
            // Gère le cas où l'utilisateur n'est pas connecté (redirection, affichage d'un message, etc.)
            return $this->redirectToRoute('app_login');
        }


        // Puis, passe les informations à la vue (template) pour les afficher
        return $this->render('user/user_info.html.twig', [
            'user' => $user,
            'items' => $cartService->getCart(),
        ]);
    }

    #[Route('/list', name: 'app_user_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository, UserInfoService $userInfoService): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'user' => $userInfoService->getUserInfo(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
{
    // Récupérer le mot de passe actuel
    $currentPassword = $user->getPassword();

    $form = $this->createForm(UserFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer les données du formulaire
        $data = $form->getData();

        // Vérifier si le champ du mot de passe est rempli
        if (!empty($data->getPassword())) {
            // Encoder le nouveau mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $data->getPassword());
            // Mettre à jour le mot de passe avec le nouveau mot de passe encodé
            $user->setPassword($hashedPassword);
        } else {
            // Si le champ du mot de passe est vide, rétablir le mot de passe actuel
            $user->setPassword($currentPassword);
        }

        // Enregistrer les modifications dans la base de données
        $userRepository->add($user, true);

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    // dd($user);

    return $this->render('user/edit.html.twig', [
        'user' => $user,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}