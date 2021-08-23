<?php

namespace App\Controller;

use App\Entity\Constante;
use App\Entity\ConstanteJour;
use App\Form\ConstanteType;
use App\Repository\ConstanteJourRepository;
use App\Repository\ConstanteRepository;
use App\Repository\ConstanteSpecialiteRepository;
use App\Repository\DemandeConsultationRepository;
use App\Repository\MatieresRepository;
use DateTime;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/constante")
 */
class ConstanteController extends AbstractController
{

    private $apiUserInfoByIdUrl = 'user_find_by_id.php';

    /**
     * @Route("/", name="constantes_index", methods={"GET"})
     */
    public function index(ConstanteRepository $constanteRepository, FlashyNotifier $flashy): Response
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

        return $this->render('constante/index.html.twig', [
            'constantes' => $constanteRepository->findAll(),
            'position' => 'Constante',
            'chemin' => 'Constante',
        ]);
    }

    /**
     * @Route("/new", name="constantes_new", methods={"GET","POST"})
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

        $constante = new Constante();
        $form = $this->createForm(ConstanteType::class, $constante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $constante->setCreatedAt(new DateTime());
            $constante->setUpdatedAt(new DateTime());
            $entityManager->persist($constante);
            $entityManager->flush();

            $flashy->success('Spécialité créée avec success');
            return $this->redirectToRoute('constantes_index');
        }

        return $this->render('constante/new.html.twig', [
            'constante' => $constante,
            'form' => $form->createView(),
            'position' => 'Nouvelle Spécialité',
            'chemin' => 'Spécialité / Nouvelle Spécialité',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="constantes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Constante $constante, FlashyNotifier $flashy): Response
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

        $form = $this->createForm(ConstanteType::class, $constante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $flashy->success('Constante éditée avec success');
            return $this->redirectToRoute('constantes_index');
        }

        return $this->render('constante/edit.html.twig', [
            'constante' => $constante,
            'form' => $form->createView(),
            'position' => 'Edition Constante',
            'chemin' => 'Constante / Edition Constante'
        ]);
    }
    /**
     * @Route("/attente/enseigne/{id_enseigne}/{id_specialite}/{id_patient}/addConstanteJour", name="constante_entente_add_jour", methods={"GET","POST"})
     */
    public function addContstanteJour(Request $request, $id_specialite, $id_patient, $id_enseigne, ConstanteJourRepository $constanteJourRepository, ConstanteSpecialiteRepository $constanteSpecialiteRepository, ConstanteRepository $constanteRepository, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }

        $submittedToken = $request->request->get('_token');

        $const_spec = $constanteSpecialiteRepository->findBy(["id_specialite" => $id_specialite]);
        $consttantes = [];
        if (count($const_spec) != 0) {
            foreach ($const_spec as $cs) {
                # code...
                $consttante = $constanteRepository->findOneBy(['id' => $cs->getIdConstante()]);
                array_push($consttantes, $consttante);
            }
            //dd($consttantes);
        }
        if ($submittedToken != null) {
            if ($this->isCsrfTokenValid("addConstanteJour", $submittedToken)) {
                $constante = $request->request->get('constante');
                $lib_cst = $request->request->get('libelle_jour');
                $id_client = $user->getId();

                $constanteJour = new ConstanteJour();

                $constanteJour->setIdPatient($id_patient)
                    ->setIdInfirmier($id_client)
                    ->setIdSpecialite($id_specialite)
                    ->setIdEnseigne($id_enseigne)
                    ->setLibelleCst($lib_cst)
                    ->setIdConstante($constante)
                    ->setCreatedAt(new DateTime());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($constanteJour);
                $entityManager->flush();
                $flashy->success("Constante du jour défini avec succès");

                return $this->redirectToRoute('constante_patient_jour', [
                    'id_enseigne' => $id_enseigne,
                    'id_specialite' => $id_specialite,
                    'id_patient' => $id_patient,
                ]);

                //dd($constanteJour);
            } else {
                $flashy->error("Formulaire invalide");
                //return $this->redirectToRoute('enseigne');
            }
        } else {
            /*return $this->render("enseigne/new.html.twig", [
                'position' => 'Nouveau',
                'chemin' => 'Enseigne  /  Nouveau',
            ]);*/
        }

        return $this->render('constante/add_constante_jour.html.twig', [
            'allconstantes' => $consttantes,
            'position' => 'Spécialité attente',
            'chemin' => 'Spécialité attente',
            'id_enseigne' => $id_enseigne
        ]);
    }

    /**
     * @Route("/attente/enseigne/patient/{id_enseigne}/{id_specialite}/{id_patient}", name="constante_patient_jour", methods={"GET","POST"})
     */
    public function constanteJour(ConstanteSpecialiteRepository $constanteSpecialiteRepository, ConstanteRepository $constanteRepository, $id_enseigne, $id_specialite, $id_patient, FlashyNotifier $flashy, ConstanteJourRepository $constanteJourRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }

        $const_spec = $constanteSpecialiteRepository->findBy(["id_specialite" => $id_specialite]);
        $consttantes = [];
        if (count($const_spec) != 0) {
            foreach ($const_spec as $cs) {
                # code...
                $consttante = $constanteRepository->findOneBy(['id' => $cs->getIdConstante()]);
                array_push($consttantes, $consttante);
            }
            //dd($consttantes);
        }

        //$demandeConsultation = $demandeConsultationRepository->findBy(['id_enseigne' => $id_enseigne, 'id_specialite' => $id_specialite, 'is_valided' => 0],[['distinct'=>true]]);
        $constanteJours = $constanteJourRepository->findBy(['id_enseigne' => $id_enseigne, 'id_specialite' => $id_specialite, 'id_patient' => $id_patient, 'id_infirmier' => $user->getId()]);

        //dd($constanteJours);

        return $this->render('constante/constante_jour.html.twig', [
            'constantesJours' => $constanteJours,
            'position' => 'Constante du jour',
            'chemin' => 'Constante du jour',
            'id_enseigne' => $id_enseigne,
            'id_specialite' => $id_specialite,
            'id_patient' => $id_patient,
            'constantes_spec' => $consttantes
        ]);
    }


    /**
     * @Route("/attente/enseigne/patient/{id_enseigne}/{id_specialite}", name="constante_patient_attente", methods={"GET","POST"})
     */
    public function patientAtente(HttpClientInterface $client, $id_enseigne, $id_specialite, FlashyNotifier $flashy, DemandeConsultationRepository $demandeConsultationRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }

        //$demandeConsultation = $demandeConsultationRepository->findBy(['id_enseigne' => $id_enseigne, 'id_specialite' => $id_specialite, 'is_valided' => 0],[['distinct'=>true]]);
        $demandeConsultation = $demandeConsultationRepository->findByPatientAttenteField($id_enseigne, $id_specialite, $is_valided = 0);

        $patients = array();
        //dd($demandeConsultation);
        if (count($demandeConsultation) != 0) {
            foreach ($demandeConsultation as $dc) {
                //commandes
                $responseMyClient = $client->request(
                    'POST',
                    $this->getParameter('API_URL') . $this->apiUserInfoByIdUrl,
                    [
                        'query' => [
                            'id_client' => $dc->getIdPatient()
                        ]
                    ]
                );

                $content = $responseMyClient->getContent();
                $content_array = json_decode($content, true);

                if ($content_array['server_response'][0]['status'] == 1) {
                    $flashy->success("Liste des patients en attente");
                    $arr_pat = $content_array['server_response'][0]["client"];
                    $arr_pat["date"] =  $dc->getDateCreation();
                    //array_push($arr_pat, ["date" => $dc->getDateCreation()]);
                    array_push($patients, $arr_pat);
                    //return $this->redirectToRoute('constante_entente', ['id_enseigne' => $id_enseigne]);
                } else {
                    $flashy->error("Opération échouée");
                    return $this->redirectToRoute('constante_entente', ['id_enseigne' => $id_enseigne]);
                }
            }
            //dd($patients);

        }

        return $this->render('constante/patient_attente.html.twig', [
            'patients' => $patients,
            'position' => 'Patients attente',
            'chemin' => 'Patients attente',
            'id_enseigne' => $id_enseigne,
            'id_specialite' => $id_specialite,
        ]);
    }



    /**
     * @Route("/attente/enseigne/{id_enseigne}", name="constante_entente", methods={"GET","POST"})
     */
    public function infirmier($id_enseigne, MatieresRepository $matieresRepository, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }


        return $this->render('constante/constante_specialite.html.twig', [
            'specialites' => $matieresRepository->findAll(),
            'position' => 'Spécialité attente',
            'chemin' => 'Spécialité attente',
            'id_enseigne' => $id_enseigne
        ]);
    }
}
