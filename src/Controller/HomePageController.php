<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Form\SearchType;
use App\Repository\RecetteRepository;
use App\Repository\RecetteRepositoryInterface;
use App\Repository\RepositoryInterface;
use App\Service\ImageInterface;
use App\Service\RecetteInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class HomePageController extends BaseController
{
    private RecetteRepository $recetteRepository;
    private RepositoryInterface $repositoryInterface;
    private ImageInterface $imageInterface;
    private string $image_directory;

    public function __construct(
        RecetteRepository $recetteRepository,
        ImageInterface $imageInterface,
        RepositoryInterface $repositoryInterface,
        string $image_directory
    )
    {
        parent::__construct($recetteRepository, $imageInterface, $image_directory);
        $this->recetteRepository = $recetteRepository;
        $this->imageInterface = $imageInterface;
        $this->image_directory = $image_directory;
        $this->repositoryInterface = $repositoryInterface;
    }

    #[Route('/home', name: 'app_home_page_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        return $this->render('home_page/index.html.twig', [
            'bestThreeLikePublication' => $this->recetteRepository->findTopThreeBestLikedRecipe(),
            'recettes' => $this->recetteRepository->findThreeLastRecette(),
            'form' => $this->createForm(SearchType::class, null, ['action' => $this->generateUrl('app_homepage_search')])->createView()
        ]);
    }

    #[Route('/search', name: 'app_homepage_search', methods: ['GET', 'POST'])]
    public function search(Request $request): Response
    {
        $recette = new Recette();
        $form = $this->createForm(SearchType::class, $recette)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Recette $recette */
            $recette = $form->getData();
        }

        return $this->render('Search/index.html.twig', [
            'recettes' => null === $recette->getName() ? [] : $this->recetteRepository->filterByRecette($recette->getName()),
            'name' => $recette->getName(),
            'form' => $form->createView()
        ]);
    }


    #[Route('/recette/new', name: 'app_home_page_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recetteRepository->save($recette, true);

            // Move image
            $this->imageInterface->downloadImage($form, $recette,$this->recetteRepository,$this->image_directory);
            return $this->redirectToRoute('app_home_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recette/new.html.twig', [
            'recette' => $recette,
            'form' => $form
        ]);
    }

    #[Route('/recette/{id}', name: 'app_home_page_show', methods: ['GET'])]
    public function show(Recette $recette): Response
    {
        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
        ]);
    }

    #[Route('/recette/{id}/edit', name: 'app_home_page_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recette $recette): Response
    {
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->recetteRepository->save($recette, true);
            //move the image
            $this->imageInterface->downloadImage($form, $recette,$this->recetteRepository,$this->image_directory);
            return $this->redirectToRoute('app_home_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }

    #[Route('/recette/{id}', name: 'app_home_page_delete', methods: ['POST'])]
    public function delete(Request $request, Recette $recette): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recette->getId(), $request->request->get('_token'))) {
            $this->recetteRepository->remove($recette, true);
        }

        return $this->redirectToRoute('app_home_page_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/recette', name: 'app_homepage_recette', methods: ['GET'])]
    public function findAllRecette(): Response
    {
        return $this->renderForm('recette/index.html.twig', [
            'recettes' => $this->repositoryInterface->findTenLastObject()
        ]);
    }
}
