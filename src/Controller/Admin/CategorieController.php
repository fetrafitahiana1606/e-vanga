<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Service\UserInfoService;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/categorie')]
class CategorieController extends AbstractController
{
    #[Route('/', name: 'app_categorie_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')] // Restriction d'accès admin seulement
    public function index(CategorieRepository $categorieRepository, UserInfoService $userInfoService): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'user' => $userInfoService->getUserInfo(),
        ]);
    }

    #[Route('/new', name: 'app_categorie_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Restriction d'accès admin seulement
    public function new(Request $request, CategorieRepository $categorieRepository, UserInfoService $userInfoService): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieRepository->add($categorie, true);

            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
            'user' => $userInfoService->getUserInfo(),
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')] // Restriction d'accès admin seulement
    public function show(Categorie $categorie, UserInfoService $userInfoService): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
            'user' => $userInfoService->getUserInfo(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorie_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Restriction d'accès admin seulement
    public function edit(Request $request, Categorie $categorie, CategorieRepository $categorieRepository, UserInfoService $userInfoService): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieRepository->add($categorie, true);

            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
            'user' => $userInfoService->getUserInfo(),
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')] // Restriction d'accès admin seulement
    public function delete(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $categorieRepository->remove($categorie, true);
        }

        return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
