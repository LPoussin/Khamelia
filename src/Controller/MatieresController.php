<?php

namespace App\Controller;

use App\Entity\ConstanteSpecialite;
use App\Entity\Matieres;
use App\Form\MatieresType;
use App\Repository\ConstanteRepository;
use App\Repository\ConstanteSpecialiteRepository;
use App\Repository\MatieresRepository;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager as PersistenceObjectManager;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/specialite")
 */
class MatieresController extends AbstractController
{
    /**
     * @Route("/", name="matieres_index", methods={"GET"})
     */
    public function index(MatieresRepository $matieresRepository, FlashyNotifier $flashy, ConstanteSpecialiteRepository $constanteSpecialiteRepository): Response
    {
        #DATABASE_URL=mysql://khamelia3db:'i41Y!77wDlJHO!15E@D3q0XwAmObt66gsQz'@127.0.0.1:3306/khamelia3_db
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('SUPER_ADMIN', $user->getRoles())) {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_login');
        }
        $const_spec = $constanteSpecialiteRepository->findAll();
        $const_spec_id = [];
        if(count($const_spec) != 0){
            $const_spec_id = array_map(function($obj){return $obj->getIdSpecialite();},$const_spec);

        }

        return $this->render('matieres/index.html.twig', [
            'matieres' => $matieresRepository->findAll(),
            'position' => 'Spécialité',
            'constantes' => $const_spec_id,
            'chemin' => 'Spécialité',
            'constantess' => $constanteSpecialiteRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="matieres_new", methods={"GET","POST"})
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

        $matiere = new Matieres();
        $form = $this->createForm(MatieresType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$matiere->setConstante("1");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($matiere);
            $entityManager->flush();

            $flashy->success('Spécialité créée avec success');
            return $this->redirectToRoute('matieres_index');
        }

        return $this->render('matieres/new.html.twig', [
            'matiere' => $matiere,
            'form' => $form->createView(),
            'position' => 'Nouvelle Spécialité',
            'chemin' => 'Spécialité / Nouvelle Spécialité',
        ]);
    }

    /**
     * @Route("/{id}", name="matieres_show", methods={"GET"})
     */
    public function show(Matieres $matiere): Response
    {
        return $this->render('matieres/show.html.twig', [
            'matiere' => $matiere,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="matieres_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Matieres $matiere, FlashyNotifier $flashy): Response
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

        $form = $this->createForm(MatieresType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $flashy->success('Spécialité éditée avec success');
            return $this->redirectToRoute('matieres_index');
        }

        return $this->render('matieres/edit.html.twig', [
            'matiere' => $matiere,
            'form' => $form->createView(),
            'position' => 'Edition Spécialité',
            'chemin' => 'Spécialité / Edition Spécialité',
        ]);
    }


    /**
     * @Route("/{id}/constante", name="all_constantes", methods={"GET","POST"})
     */
    public function speConst($id, FlashyNotifier $flashy, HttpClientInterface $client, ConstanteRepository $constanteRepository): Response
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
        $liaisonCM =  $entityManager->getRepository(ConstanteSpecialite::class)->findBy(['id_specialite' => $id]);
        $const_spec_id = [];

        if (count($liaisonCM) !== 0) {
            //$entityManager->remove($liaisonCM);
            //$entityManager->persist($liaisonCM);
            //$entityManager->flush();
            $const_spec_id = array_map(function($obj){return $obj->getIdConstante();},$liaisonCM);

            //$flashy->success('Enseigne deliéé avec succès!');
        }else{
            $const_spec_id = [];
            
            //$flashy->error('Impossible de deliéé !');
        }
        //dd($const_spec_id);
        //$nom_enseigne = $request->request->get('nom_enseigne');
        //$all_client = $request->request->get('all_client');

        return $this->render('matieres/constante.html.twig', [
            'constantes' => $constanteRepository->findAll(),
            'constantesSpecId' => $const_spec_id,
            'id_matiere' => $id,
            'position' => 'Liste des constantes',
            'chemin' => 'Specialite / Liste des constantes',
        ]);

        //dd($content_array);

    }

    /**
     * @Route("/liaison_spec/{id}/constante/{id_matiere}/liaison", name="specialite_liaison", methods={"GET","POST"})
     */
    public function medLiaison($id, $id_matiere, FlashyNotifier $flashy): Response
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

        //$nom_enseigne = $request->request->get('nom_enseigne');
        //$all_client = $request->request->get('all_client');
        //$id = $request->request->get('id');
        //dd($id_particulier);
        $em = $this->getDoctrine()->getManager();

        $const_spec = new ConstanteSpecialite();

        $const_spec->setIdConstante($id)->setIdSpecialite($id_matiere)
            ->setCreatedAt(new DateTime())->setUpdatedAt(new DateTime());
        $em->persist($const_spec);
        $em->flush();
        $flashy->success("Constante liée avec réussie");
        //return $this->redirectToRoute('enseigne');
        return $this->redirectToRoute('all_constantes', ['id'=>$id_matiere]);

        /*if ($content_array['server_responses'][0]['founded'] == 1) {
            $flashy->success("Opération réussie");
            //return $this->redirectToRoute('enseigne');
            return $this->redirectToRoute('medecins_index');
        } else if ($content_array['server_responses'][0]['founded'] == 0) {
            $flashy->error("Opération échouée");
            return $this->redirectToRoute('medecins_index');
        } else {
            $flashy->error("Liaison deja etabli impossible de se lier!");
            return $this->redirectToRoute('medecins_index');
        }*/
    }

    /**
     * @Route("/{id}/deliaison/{id_matiere}", name="specialite_deliaison", methods={"GET"})
     */
    public function medDeliaison($id,$id_matiere, FlashyNotifier $flashy): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $liaisonCM =  $entityManager->getRepository(ConstanteSpecialite::class)->findBy(['id_specialite' => $id_matiere,'id_constante' => $id]);

        //dd($liaisonCM);
        if ($liaisonCM !== null) {
            $entityManager->remove($liaisonCM[0]);
            //$entityManager->persist($liaisonCM);
            $entityManager->flush();

            $flashy->success('Constante deliéé avec succès!');
        }else{
            $flashy->error('Impossible de deliéé !');
        }

        return $this->redirectToRoute('all_constantes',['id' => $id_matiere]);
    }
    /**
     * @Route("/{id}", name="matieres_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Matieres $matiere): Response
    {
        if ($this->isCsrfTokenValid('delete' . $matiere->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($matiere);
            $entityManager->flush();
        }

        return $this->redirectToRoute('matieres_index');
    }
}
