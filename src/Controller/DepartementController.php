<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Form\DepartementType;
use App\Repository\DepartementRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/departement")
 */
class DepartementController extends AbstractController
{
    /**
     * @Route("/", name="departements_index", methods={"GET"})
     */
    public function index(DepartementRepository $departementsRepository, FlashyNotifier $flashy): Response
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

        return $this->render('departement/index.html.twig', [
            'departements' => $departementsRepository->findAll(),
            'position' => 'Departements',
            'chemin' => 'Departements',
        ]);
    }
    


    /**
     * @Route("/new", name="departement_new", methods={"GET","POST"})
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

        $departement = new Departement();
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($departement);
            $entityManager->flush();

            $flashy->success('Departement créé avec success');
            return $this->redirectToRoute('departements_index');
        }

        return $this->render('departement/new.html.twig', [
            'departement' => $departement,
            'form' => $form->createView(),
            'position' => 'Nouveau Departement',
            'chemin' => 'Departement / Nouveau Departement',
        ]);
    }

    /**
     * @Route("/{id}", name="departement_pays", methods={"GET"})
     */
    public function depPays($id, DepartementRepository $departementsRepository, FlashyNotifier $flashy): Response
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


        return $this->render('departement/index.html.twig', [
            'departements' => $entityManager->getRepository(Departement::class)->findBy(['country' => $id]),
            'position' => 'Departements',
            'chemin' => 'Departements',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="departement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Departement $departement, FlashyNotifier $flashy): Response
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
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($departement);
            $entityManager->flush();

            $flashy->success('Departement édité avec success');
            return $this->redirectToRoute('departements_index');
        }

        return $this->render('departement/edit.html.twig', [
            'departement' => $departement,
            'form' => $form->createView(),
            'position' => 'Edition departement',
            'chemin' => 'Departement / Edition Departement',
        ]);
    }
}
