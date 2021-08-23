<?php

namespace App\Controller;

use App\Entity\NiveauxEtudes;
use App\Form\NiveauxEtudesType;
use App\Repository\NiveauxEtudesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MercurySeries\FlashyBundle\FlashyNotifier;


/**
 * @Route("/niveaux/etudes")
 */
class NiveauxEtudesController extends AbstractController
{
    /**
     * @Route("/", name="niveaux_etudes_index", methods={"GET"})
     */
    public function index(NiveauxEtudesRepository $niveauxEtudesRepository, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('niveaux_etudes/index.html.twig', [
            'niveaux_etudes' => $niveauxEtudesRepository->findAll(),
            'position' => 'Niveau d\'étude',
            'chemin' => 'Niveau d\'étude',
        ]);
    }

    /**
     * @Route("/new", name="niveaux_etudes_new", methods={"GET","POST"})
     */
    public function new(Request $request, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        $niveauxEtude = new NiveauxEtudes();
        $form = $this->createForm(NiveauxEtudesType::class, $niveauxEtude);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($niveauxEtude);
            $entityManager->flush();

            $flashy->success('Niveau d\'étude créé avec success');
            return $this->redirectToRoute('niveaux_etudes_index');
        }

        return $this->render('niveaux_etudes/new.html.twig', [
            'niveaux_etude' => $niveauxEtude,
            'form' => $form->createView(),
            'position' => 'Nouveau Niveau d\'étude',
            'chemin' => 'Niveau d\'étude / Nouveau',
        ]);
    }

    /**
     * Route("/{id}", name="niveaux_etudes_show", methods={"GET"})
     */
    public function show(NiveauxEtudes $niveauxEtude): Response
    {
        return $this->render('niveaux_etudes/show.html.twig', [
            'niveaux_etude' => $niveauxEtude,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="niveaux_etudes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, NiveauxEtudes $niveauxEtude, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(NiveauxEtudesType::class, $niveauxEtude);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $flashy->success('Niveau d\'étude mise à jour avec success');
            return $this->redirectToRoute('niveaux_etudes_index');
        }

        return $this->render('niveaux_etudes/edit.html.twig', [
            'niveaux_etude' => $niveauxEtude,
            'form' => $form->createView(),
            'position' => 'Edition Niveau d\'étude',
            'chemin' => 'Niveau d\'étude / Edition',
        ]);
    }

    /**
     * Route("/{id}", name="niveaux_etudes_delete", methods={"DELETE"})
     */
    public function delete(Request $request, NiveauxEtudes $niveauxEtude): Response
    {
        if ($this->isCsrfTokenValid('delete'.$niveauxEtude->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($niveauxEtude);
            $entityManager->flush();
        }

        return $this->redirectToRoute('niveaux_etudes_index');
    }
}
