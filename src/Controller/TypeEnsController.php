<?php

namespace App\Controller;

use App\Entity\TypeEns;
use App\Form\TypeEnsType;
use App\Repository\TypeEnsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MercurySeries\FlashyBundle\FlashyNotifier;

/**
 * @Route("/type/ens")
 */
class TypeEnsController extends AbstractController
{
    /**
     * @Route("/", name="type_ens_index", methods={"GET"})
     */
    public function index(TypeEnsRepository $typeEnsRepository): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('type_ens/index.html.twig', [
            'type_ens' => $typeEnsRepository->findAll(),
            'position' => 'Type enseigne',
            'chemin' => 'Type enseigne',
        ]);
    }

    /**
     * @Route("/new", name="type_ens_new", methods={"GET","POST"})
     */
    public function new(Request $request, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            return $this->redirectToRoute('app_login');
        }

        $typeEn = new TypeEns();
        $form = $this->createForm(TypeEnsType::class, $typeEn);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            
            if(strlen($typeEn->getSlug()) >= 7)
            {
                $flashy->error('Nombre de caractère trop grand pour slug!');
                return $this->redirectToRoute('type_ens_index');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($typeEn);
            $entityManager->flush();


            $flashy->success('Type d\'enseigne créé avec success');
            return $this->redirectToRoute('type_ens_index');
        }

        return $this->render('type_ens/new.html.twig', [
            'type_en' => $typeEn,
            'form' => $form->createView(),
            'position' => 'Nouveau type d\'enseinge',
            'chemin' => 'Type enseigne / Nouveau',
        ]);
    }

    /**
     * Route("/{id}", name="type_ens_show", methods={"GET"})
     */
    public function show(TypeEns $typeEn): Response
    {
        return $this->render('type_ens/show.html.twig', [
            'type_en' => $typeEn,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="type_ens_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TypeEns $typeEn, FlashyNotifier $flashy): Response
    {
        $form = $this->createForm(TypeEnsType::class, $typeEn);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(strlen($typeEn->getSlug()) >= 7)
            {
                $flashy->error('Nombre de caractère trop grand pour slug!');
                return $this->redirectToRoute('type_ens_index');
            }
            $this->getDoctrine()->getManager()->flush();

            $flashy->success('Type d\'enseigne créé mise à jour avec success');
            return $this->redirectToRoute('type_ens_index');
        }

        return $this->render('type_ens/edit.html.twig', [
            'type_en' => $typeEn,
            'form' => $form->createView(),
            'position' => 'Edition d\'un type d\'enseinge',
            'chemin' => 'Type enseigne / Edition d\'un type d\'enseinge',
        ]);
    }

    /**
     * Route("/{id}", name="type_ens_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TypeEns $typeEn): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeEn->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($typeEn);
            $entityManager->flush();
        }

        return $this->redirectToRoute('type_ens_index');
    }
}
