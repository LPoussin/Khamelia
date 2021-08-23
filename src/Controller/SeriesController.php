<?php

namespace App\Controller;

use App\Entity\Series;
use App\Form\SeriesType;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MercurySeries\FlashyBundle\FlashyNotifier;


/**
 * @Route("/series")
 */
class SeriesController extends AbstractController
{
    /**
     * @Route("/", name="series_index", methods={"GET"})
     */
    public function index(SeriesRepository $seriesRepository, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if(!in_array('SUPER_ADMIN', $user->getRoles()))
        {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_logout');
        }

        return $this->render('series/index.html.twig', [
            'series' => $seriesRepository->findAll(),
            'position' => 'Séries',
            'chemin' => 'Séries',
        ]);
    }

    /**
     * @Route("/new", name="series_new", methods={"GET","POST"})
     */
    public function new(Request $request, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if(!in_array('SUPER_ADMIN', $user->getRoles()))
        {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_logout');
        }
        $series = new Series();
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($series);
            $entityManager->flush();

            $flashy->success('Série bien créée');
            return $this->redirectToRoute('series_index');
        }
       

        return $this->render('series/new.html.twig', [
            'series' => $series,
            'form' => $form->createView(),
            'position' => 'Nouveau',
            'chemin' => 'Séries / Nouveau',
        ]);
    }

    /**
     * @Route("/{id}", name="series_show", methods={"GET"})
     */
    public function show(Series $series): Response
    {
        return $this->render('series/show.html.twig', [
            'series' => $series,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="series_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Series $series, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if(!in_array('SUPER_ADMIN', $user->getRoles()))
        {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_logout');
        }
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $flashy->success('Série bien mise à jour');
            return $this->redirectToRoute('series_index');
        }

        
        return $this->render('series/edit.html.twig', [
            'series' => $series,
            'form' => $form->createView(),
            'position' => 'Mise à jour',
            'chemin' => 'Séries / Mise à jour',
        ]);
    }

    /**
     * @Route("/{id}", name="series_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Series $series): Response
    {
        if ($this->isCsrfTokenValid('delete'.$series->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($series);
            $entityManager->flush();
        }

        return $this->redirectToRoute('series_index');
    }
}
