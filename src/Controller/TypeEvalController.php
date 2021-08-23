<?php

namespace App\Controller;

use App\Entity\TypeEval;
use App\Form\TypeEvalType;
use App\Repository\TypeEvalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MercurySeries\FlashyBundle\FlashyNotifier;

/**
 * @Route("/type/eval")
 */
class TypeEvalController extends AbstractController
{
    /**
     * @Route("/", name="type_eval_index", methods={"GET"})
     */
    public function index(TypeEvalRepository $typeEvalRepository, FlashyNotifier $flashy): Response
    {
        return $this->render('type_eval/index.html.twig', [
            'type_evals' => $typeEvalRepository->findAll(),
            'position' => 'Type d\'évaluation',
            'chemin' => 'Type d\'évaluation'
        ]);
    }

    /**
     * @Route("/new", name="type_eval_new", methods={"GET","POST"})
     */
    public function new(Request $request, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }  

        $typeEval = new TypeEval();
        $form = $this->createForm(TypeEvalType::class, $typeEval);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($typeEval);
            $entityManager->flush();

            return $this->redirectToRoute('type_eval_index');
        }

        return $this->render('type_eval/new.html.twig', [
            'type_eval' => $typeEval,
            'form' => $form->createView(),
            'position' => 'Nouveau',
            'chemin' => 'Type d\'évaluation / Nouveau'
        ]);
    }

    /**
     * @Route("/{id}", name="type_eval_show", methods={"GET"})
     */
    public function show(TypeEval $typeEval): Response
    {
        return $this->render('type_eval/show.html.twig', [
            'type_eval' => $typeEval,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="type_eval_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TypeEval $typeEval, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }   
        
        $form = $this->createForm(TypeEvalType::class, $typeEval);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('type_eval_index');
        }

        return $this->render('type_eval/edit.html.twig', [
            'type_eval' => $typeEval,
            'form' => $form->createView(),
            'position' => 'Mise à jour',
            'chemin' => 'Type d\'évaluation / Mise à jour'
        ]);
    }

    /**
     * @Route("/{id}", name="type_eval_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TypeEval $typeEval): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeEval->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($typeEval);
            $entityManager->flush();
        }

        return $this->redirectToRoute('type_eval_index');
    }
}
