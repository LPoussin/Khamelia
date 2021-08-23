<?php

namespace App\Controller;

use App\Entity\EnseigneAffiliee;
use App\Entity\Ensseigne;
use App\Entity\LiaisonCentreSante;
use App\Form\EnsseigneType;
use App\Repository\EnseigneAffilieeRepository;
use App\Repository\EnsseigneRepository;
use App\Repository\LiaisonCentreSanteRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use _;

/**
 * @Route("/ensseigne")
 */
class EnsseigneController extends AbstractController
{
    /**
     * @Route("/", name="ensseignes_index", methods={"GET"})
     */
    public function index(EnsseigneRepository $enseigneRepository, FlashyNotifier $flashy): Response
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

        $all_enseigne = $enseigneRepository->findAll();
        foreach ($all_enseigne as $enseigne) {
            $liaisonCM =  $entityManager->getRepository(LiaisonCentreSante::class)->findOneBy(['id_centre_de_sante' => $enseigne->getId()]);
            if ($liaisonCM !== null) {
                $enseigne->setLiaison(1);
            } else {
                //$enseigneAffilie =  $entityManager->getRepository(EnseigneAffiliee::class)->findBy(['g  ' => $id]);
                //each($id);
                //dd(_::is_numeric($id));
                $enseigne->setLiaison(0);

                //dd($allEnseigneLiaison);
            }


            $entityManager->persist($enseigne);
            $entityManager->flush();
        }
        $all_enseigne = $enseigneRepository->findAll();

        return $this->render('ensseigne/index.html.twig', [
            'enseignes' => $all_enseigne,
            'position' => 'Centre médicale',
            'chemin' => 'Centre médicale',
        ]);
    }

    /**
     * @Route("/new", name="ensseigne_new", methods={"GET","POST"})
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

        $ensseigne = new Ensseigne();
        $form = $this->createForm(EnsseigneType::class, $ensseigne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ensseigne);
            $entityManager->flush();

            $flashy->success('Enseigne créé avec success');
            return $this->redirectToRoute('ensseignes_index');
        }

        return $this->render('ensseigne/new.html.twig', [
            'ensseigne' => $ensseigne,
            'form' => $form->createView(),
            'position' => 'Nouvelle Centre médical',
            'chemin' => 'Centre médical / Nouvelle Centre médical',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ensseigne_edit", methods={"GET","POST"})
     */
    public function edit($id, Request $request, FlashyNotifier $flashy, Ensseigne $ensseigne): Response
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
        $form = $this->createForm(ensseigneType::class, $ensseigne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ensseigne);
            $entityManager->flush();

            $flashy->success('Centre médical édité avec success');
            return $this->redirectToRoute('ensseignes_index');
        }

        return $this->render('ensseigne/edit.html.twig', [
            'ensseigne' => $ensseigne,
            'form' => $form->createView(),
            'position' => 'Edition Centre médical',
            'chemin' => 'Centre médical / Edition Centre médical',
        ]);
    }



    /**
     * @Route("/centrem/{id}/listeenseigne/{id_enseigne}", name="ensseigne_medicales_liaison", methods={"GET","POST"})
     */
    public function liaisonEntreprise($id, $id_enseigne, FlashyNotifier $flashy): Response
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
        $lcs = new LiaisonCentreSante();

        $lcs->setIdEnseigneAffilie($id_enseigne);
        $lcs->setIdCentreDeSante($id);
        $entityManager->persist($lcs);
        $entityManager->flush();
        $flashy->success('Enseigne liéé avec succès!');
        return $this->redirectToRoute('ensseignes_index');
    }
    /**
     * @Route("/centrem/delier/{id}", name="ensseigne_medicales_deliaison", methods={"GET","POST"})
     */
    public function deliaisonEntreprise($id, FlashyNotifier $flashy): Response
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
        $liaisonCM =  $entityManager->getRepository(LiaisonCentreSante::class)->findOneBy(['id_centre_de_sante' => $id]);

        if ($liaisonCM !== null) {
            $entityManager->remove($liaisonCM);
            //$entityManager->persist($liaisonCM);
            $entityManager->flush();

            $flashy->success('Enseigne deliéé avec succès!');
        }else{
            $flashy->error('Impossible de deliéé !');
        }

        return $this->redirectToRoute('ensseignes_index');
    }

    /**
     * @Route("/centrem/{id}/listeenseigne", name="ensseigne_medicales", methods={"GET","POST"})
     */
    public function listeEnseigneEntreprise($id, Request $request, FlashyNotifier $flashy, EnseigneAffilieeRepository $enseigneAffilieeRepository): Response
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
        $liaisonCM =  $entityManager->getRepository(LiaisonCentreSante::class)->findOneBy(['id_centre_de_sante' => $id]);
        $allEnseigneLiaison = array();
        if ($liaisonCM !== null) {
            return $this->redirectToRoute('ensseignes_index');
        } else {
            //$enseigneAffilie =  $entityManager->getRepository(EnseigneAffiliee::class)->findBy(['g  ' => $id]);
            //each($id);
            //dd(_::is_numeric($id));
            $allenseigneAffilie = $enseigneAffilieeRepository->findAll();
            if (count($allenseigneAffilie) !== 0) {
                foreach ($allenseigneAffilie as $enseigeAffile) {
                    $liaisonCMEnseigne =  $entityManager->getRepository(LiaisonCentreSante::class)->findOneBy(['id_enseigne_affilie' => $enseigeAffile->getId()]);

                    if ($liaisonCMEnseigne == null) {
                        array_push($allEnseigneLiaison, $enseigeAffile);
                    }
                }
            }
            //dd($allEnseigneLiaison);
        }
        return $this->render('ensseigne/medicales.html.twig', [
            'enseignes' => $allEnseigneLiaison,
            'id_centre' => $id,
            'position' => 'Liste Enseigne',
            'chemin' => 'Enseigne / Liste Enseigne',
        ]);
    }
}
