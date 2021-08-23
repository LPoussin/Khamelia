<?php

namespace App\Controller;

use App\Entity\Quartier;
use App\Form\QuartierType;
use App\Repository\QuartierRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/quartier")
 */
class QuartierController extends AbstractController
{
    /**
     * @Route("/", name="quartiers_index", methods={"GET"})
     */
    public function index(QuartierRepository $quartiersRepository, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('SUPER_ADMIN', $user->getRoles())) {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('quartier/index.html.twig', [
            'quartiers' => $quartiersRepository->findAll(),
            'position' => 'Quartiers',
            'chemin' => 'Quartiers',
        ]);
    }


    
    /**
     * @Route("/new", name="quartier_new", methods={"GET","POST"})
     */
    public function new(Request $request, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('SUPER_ADMIN', $user->getRoles())) {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_login');
        }

        $quartier = new Quartier();
        $form = $this->createForm(QuartierType::class, $quartier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quartier);
            $entityManager->flush();

            $flashy->success('Quartier créé avec success');
            return $this->redirectToRoute('quartiers_index');
        }

        return $this->render('quartier/new.html.twig', [
            'quartier' => $quartier,
            'form' => $form->createView(),
            'position' => 'Nouveau quartier',
            'chemin' => 'Quartier / Nouveau Quartier',
        ]);
    }

    /**
     * @Route("/{id}", name="quartier_vil", methods={"GET"})
     */
    public function quartierVil($id, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('SUPER_ADMIN', $user->getRoles())) {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_login');
        }
        
        $entityManager = $this->getDoctrine()->getManager();


        return $this->render('quartier/index.html.twig', [
            'quartiers' => $entityManager->getRepository(quartier::class)->findBy(['ville' => $id]),
            'position' => 'Quartiers',
            'chemin' => 'Quartiers',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="quartier_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Quartier $quartier, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('SUPER_ADMIN', $user->getRoles())) {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(QuartierType::class, $quartier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quartier);
            $entityManager->flush();

            $flashy->success('Quartier édité avec success');
            return $this->redirectToRoute('quartiers_index');
        }

        return $this->render('quartier/edit.html.twig', [
            'quartier' => $quartier,
            'form' => $form->createView(),
            'position' => 'Edition quartier',
            'chemin' => 'Quartier / Edition Quartier',
        ]);
    }
}
