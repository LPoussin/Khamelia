<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ville")
 */
class VilleController extends AbstractController
{
    /**
     * @Route("/", name="villes_index", methods={"GET"})
     */
    public function index(VilleRepository $villesRepository, FlashyNotifier $flashy): Response
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

        return $this->render('ville/index.html.twig', [
            'villes' => $villesRepository->findAll(),
            'position' => 'Villes',
            'chemin' => 'Villes',
        ]);
    }

    /**
     * @Route("/new", name="ville_new", methods={"GET","POST"})
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

        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ville);
            $entityManager->flush();

            $flashy->success('Ville créé avec success');
            return $this->redirectToRoute('villes_index');
        }

        return $this->render('ville/new.html.twig', [
            'ville' => $ville,
            'form' => $form->createView(),
            'position' => 'Nouveau ville',
            'chemin' => 'Ville / Nouveau Ville',
        ]);
    }

    /**
     * @Route("/{id}", name="ville_depat", methods={"GET"})
     */
    public function villeDepat(int $id, FlashyNotifier $flashy): Response
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


        return $this->render('ville/index.html.twig', [
            'villes' => $entityManager->getRepository(Ville::class)->findBy(['departement' => $id]),
            'position' => 'Villes',
            'chemin' => 'Villes',
        ]);
    }
    
    

    /**
     * @Route("/{id}/edit", name="ville_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ville $ville, FlashyNotifier $flashy): Response
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
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ville);
            $entityManager->flush();

            $flashy->success('ville édité avec success');
            return $this->redirectToRoute('villes_index');
        }

        return $this->render('ville/edit.html.twig', [
            'ville' => $ville,
            'form' => $form->createView(),
            'position' => 'Edition ville',
            'chemin' => 'ville / Edition ville',
        ]);
    }
}
