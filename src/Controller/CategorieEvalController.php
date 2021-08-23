<?php

namespace App\Controller;

use App\Entity\CategorieEval;
use App\Form\CategorieEvalType;
use App\Repository\CategorieEvalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MercurySeries\FlashyBundle\FlashyNotifier;


/**
 * @Route("/categorie/eval")
 */
class CategorieEvalController extends AbstractController
{
    /**
     * @Route("/", name="categorie_eval_index", methods={"GET"})
     */
    public function index(CategorieEvalRepository $categorieEvalRepository, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        } 
        return $this->render('categorie_eval/index.html.twig', [
            'categorie_evals' => $categorieEvalRepository->findAll(),
            'position' => 'Catégorie Évaluation',
            'chemin' => 'Catégorie Évaluation'
        ]);
    }

    /**
     * @Route("/new", name="categorie_eval_new", methods={"GET","POST"})
     */
    public function new(Request $request, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }  
        $categorieEval = new CategorieEval();
        $form = $this->createForm(CategorieEvalType::class, $categorieEval);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorieEval);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_eval_index');
        }

        return $this->render('categorie_eval/new.html.twig', [
            'categorie_eval' => $categorieEval,
            'form' => $form->createView(),
            'position' => 'Nouveau',
            'chemin' => 'Catégorie Évaluation / Nouveau'
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_eval_show", methods={"GET"})
     */
    public function show(CategorieEval $categorieEval): Response
    {
        return $this->render('categorie_eval/show.html.twig', [
            'categorie_eval' => $categorieEval,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categorie_eval_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CategorieEval $categorieEval, FlashyNotifier $flashy): Response
    {
        $form = $this->createForm(CategorieEvalType::class, $categorieEval);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categorie_eval_index');
        }

        return $this->render('categorie_eval/edit.html.twig', [
            'categorie_eval' => $categorieEval,
            'form' => $form->createView(),
            'position' => 'Edition',
            'chemin' => 'Catégorie Évaluation / Edition'
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_eval_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CategorieEval $categorieEval): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieEval->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorieEval);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_eval_index');
    }
}
