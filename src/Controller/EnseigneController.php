<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MercurySeries\FlashyBundle\FlashyNotifier;
use App\Repository\UserJoinedEnseigneRepository;
use App\Entity\UserJoinedEnseigne;
use App\Repository\UserRepository;
use App\Repository\ServicesRepository;
use App\Repository\TypeEnsRepository;
use App\Repository\EnseigneTypeEnsRepository;
use App\Entity\EnseigneTypeEns;
use App\Repository\NiveauxEtudesRepository;
use App\Repository\EnseigneNiveauEtudeRepository;
use App\Entity\EnseigneNiveauEtude;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Repository\SeriesRepository;
use App\Repository\MatieresRepository;
use App\Entity\AssocMatiereNiveau;
use App\Repository\AssocMatiereNiveauRepository;
use App\Entity\Series;
use App\Entity\Classes;
use App\Entity\DemandeConsultation;
use App\Entity\EnseigneAffiliee;
use App\Entity\EnseigneMatiere;
use App\Repository\ClassesRepository;
use App\Repository\InscriptionsRepository;
use App\Entity\Inscriptions;
use App\Entity\InvitationMedecin;
use App\Entity\Matieres;
use App\Entity\PatientApi;
use App\Repository\CoursRepository;
use App\Repository\EnseigneAffilieeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use App\Entity\User;
use App\Form\DemandeConsultationType;
use App\Form\PatientApiType;
use App\Repository\DemandeConsultationRepository;
use App\Repository\EnseigneMatiereRepository;
use App\Repository\InvitationMedecinRepository;
use App\Repository\MedecinRepository;
use App\Repository\PatientApiRepository;
use App\Service\UniqueId;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/dashboard/enseigne")
 */
class EnseigneController extends AbstractController
{
    private $apiMyEnseigneUrl = 'my_enseignes.php';
    private $apiAllEnseigneAffilierUrl = 'all_enseigne_affilier.php';
    private $apiAffilierEnseigneCommunaute = 'affilier_enseigne_communaute.php';
    private $apiUpdateEnseigne = 'update_enseigne.php';
    private $apiNewEnseigneUrl = 'add_enseigne.php';

    // Produits propriety
    private $api_key = "jC9Ekv86WHkW9B68sAM7gadHYfxZjB645X0el6aY7SqJherYS47M18zr5pGn2Q2G";
    private $apiListProduct = "products?sortfield=t.ref&sortorder=ASC&limit=100&mode=1";
    private $apiListService = "products?sortfield=t.ref&sortorder=ASC&limit=100&mode=2";
    private $apiCreateProduitService = "products";
    private $apiGetProduitService = "products/";
    private $apiUpdateProduitService = "products/";
    private $apiSignInUrl = 'sign_patient.php';
    private $apiJoinEnseigneUrl = 'join_enseigne_gt.php';
    private $api_url_new = 'http://net2all-business.online/Controlleur/';

    // Produits propriety



    //private $rooturl='https://centrale.magmaerp.online/public/';
    private $rooturl = 'https://127.0.0.1:8000/';

    /**
     * @Route("/newEnseigne", name="new_enseigne", methods={"GET", "POST"})
     */
    public function newEnseigne(Request $request, FlashyNotifier $flashy, HttpClientInterface $client)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connectez.');
            return $this->redirectToRoute('app_login');
        }

        if (!in_array("ENTREPRISE", $user->getRoles())) {
            $flashy->error('Vous n\'êtes pas autorisé a effectué cette action');
            return $this->redirectToRoute('app_logout');
        }
        $submittedToken = $request->request->get('_token');

        if ($submittedToken != null) {
            if ($this->isCsrfTokenValid("new-enseigne", $submittedToken)) {
                $nom_enseigne = $request->request->get('nom_enseigne');
                $code_enseigne = $request->request->get('code_enseigne');
                $id_entreprise = $user->getId();

                $responseMyEnseigne = $client->request(
                    'POST',
                    $this->getParameter('API_URL') . $this->apiNewEnseigneUrl,
                    [
                        'query' => [
                            'id_entreprise' => $id_entreprise,
                            'nom_enseigne' => $nom_enseigne,
                            'code_enseigne' => $code_enseigne,
                        ]
                    ]
                );

                $content = $responseMyEnseigne->getContent();
                $content_array = json_decode($content, true);
                if ($content_array['server_response'][0]['status'] == 1) {
                    $flashy->success("Opération réussie");
                    return $this->redirectToRoute('enseigne');
                } else {
                    $flashy->error("Opération échouée");
                    return $this->redirectToRoute('enseigne');
                }
            } else {
                $flashy->error("Formulaire invalide");
                return $this->redirectToRoute('enseigne');
            }
        } else {
            return $this->render("enseigne/new.html.twig", [
                'position' => 'Nouveau',
                'chemin' => 'Enseigne  /  Nouveau',
            ]);
        }
    }


    /**
     * @Route("/", name="enseigne", methods={"GET"})
     */
    public function index(
        Request $request,
        HttpClientInterface $client,
        UserJoinedEnseigneRepository $userJoinedEnseigneRepository,
        ServicesRepository $servicesRepo,
        TypeEnsRepository $TypeEnsRepo,
        EnseigneTypeEnsRepository $EnseigneTypeEnsRepository,
        NiveauxEtudesRepository $niveauEtudesRepo,
        EnseigneNiveauEtudeRepository $enseigneNiveauEtudeRepo,
        ClassesRepository $classesRepository,
        EnseigneAffilieeRepository $enseigneAffilieeRepository
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $TypeEns = $TypeEnsRepo->findBy(['active' => true]);
        $typeEnsArray = [];

        //Liste des enseignes affilié à InetSCHOOLS
        /*$responseEnseigneAffilier = $client->request('POST', $this->getParameter('API_URL').$this->apiAllEnseigneAffilierUrl, [
    			'body' => [
    				'id_communaute' => $this->getParameter('ID_COMMUNAUTE')
    			]
    		]);

    		$content = $responseEnseigneAffilier->getContent();
    		$content_array = json_decode($content, true);

    		$AllEnseigneAffiliees= array_key_exists("0", $content_array['server_responses'])  && $content_array['server_responses'][0]['founded'] === 0 ? [] : $content_array['server_responses'];*/

        $AllEnseigneAffiliees = $enseigneAffilieeRepository->findAll();

        //Obtenir la liste des enseignes rejoints par l'utilisateur connecté
        $enseignesJoined = $userJoinedEnseigneRepository->findBy(['id_user' => $user->getId()]);
        $joined = [];

        //recuperation des classes par enseigne
        $allClasses = $classesRepository->findAll();
        $allClassesByEnseigne = [];

        foreach ($enseignesJoined as $enseigneJoined) {
            $temp = [];
            foreach ($AllEnseigneAffiliees as $enseigneAffiliee) {
                if ($enseigneJoined->getIdEnseigne() == $enseigneAffiliee->getId())
                    array_push($joined, $enseigneJoined->getIdEnseigne());
            }


            foreach ($allClasses as $value) {

                if ($value->getIdEnseigne() == $enseigneJoined->getIdEnseigne()) {
                    $temp[] = $value;
                }
            }
            $allClassesByEnseigne[$enseigneJoined->getIdEnseigne()] = $temp;
        }

        //dd($AllEnseigneAffiliees);
        //enseigne joined by user with profile details
        // $onlineUserProfiles = $userJoinedEnseigneRepository->findBy(['id_user' => $user->getId()]);


        //dd($enseignesJoined);


        $services = $servicesRepo->findBy(['etat' => true]);
        if ($user->getType() === $this->getParameter('TYPE_PARTICULIER')) {

            return $this->render('enseigne/index.html.twig', [
                'affiliées' => $AllEnseigneAffiliees,
                'joined' => $joined,
                'enseignesJoined' => $enseignesJoined,
                'position' => 'Enseigne',
                'chemin' => 'Enseigne',
                'services' => !$services ? [] : $services,
                'TypeEns' => $TypeEns,
                'onlineUserProfiles' => $enseignesJoined,
                'allClassesByEnseigne' => $allClassesByEnseigne
            ]);
        } elseif ($user->getType() === $this->getParameter('TYPE_ENTREPRISE')) {
            //Liste des enseignes créer par l'entreprise connectée
            $responseMyEnseigne = $client->request('POST', $this->getParameter('API_URL') . $this->apiMyEnseigneUrl, [
                'query' => [
                    'id_entreprise' => $user->getId()
                ]
            ]);

            $content = $responseMyEnseigne->getContent();
            $content_array = json_decode($content, true);
            //$MyEnseignes = $content_array['server_responses'][0]['founded'] === 0 ? [] : $content_array['server_responses'];
            $MyEnseignes = [];
            if (key_exists(0, $content_array['server_responses'])) {
                if ($content_array['server_responses'][0]['founded'] == 1) {
                    $MyEnseignes = $content_array['server_responses'];
                }
            }

            $affiliées = [];
            $myEnseignesId = [];
            $niveauxEtudes = [];
            if (!empty($MyEnseignes) and !empty($AllEnseigneAffiliees)) {
                foreach ($MyEnseignes as $enseigne) {
                    array_push($myEnseignesId, $enseigne['id_enseigne']);
                    foreach ($AllEnseigneAffiliees as $enseigneAffilie) {
                        if ($enseigne['id_enseigne'] == $enseigneAffilie->getId()) {
                            array_push($affiliées, $enseigne['id_enseigne']);
                            $niveauxEtudes[$enseigne['id_enseigne']] =
                                $enseigneNiveauEtudeRepo->findOneBy(['id_enseigne' => $enseigne['id_enseigne']]) == null ? [] : $enseigneNiveauEtudeRepo->findOneBy(['id_enseigne' => $enseigne['id_enseigne']]);
                        }
                    }
                    $typeEnsArray[$enseigne['id_enseigne']] = $EnseigneTypeEnsRepository->findBy(['id_enseigne' => $enseigne['id_enseigne']]);
                }
            }

            $allNiveauxEtudes = $niveauEtudesRepo->findBy(['etat' => 1]);



            return $this->render('enseigne/index.html.twig', [
                'enseignes' => $MyEnseignes,
                'affiliées' => $affiliées,
                'enseignesAffiliees' => $AllEnseigneAffiliees,
                'myEnseignesId' => $myEnseignesId,
                'enseignesJoined' => $enseignesJoined,
                'joined' => $joined,
                'position' => 'Enseigne',
                'chemin' => 'Enseigne',
                'services' => !$services ? [] : $services,
                'TypeEns' => $TypeEns,
                'typeEnsArray' => $typeEnsArray,
                'niveauxEtudes' => $niveauxEtudes,
                'allNiveauxEtudes' => $allNiveauxEtudes,
                'onlineUserProfiles' => $enseignesJoined,
                'allClassesByEnseigne' => $allClassesByEnseigne,
                'userid' => sha1($user->getId())
            ]);
        }
    }

    /**
     * @Route("/affilier/{id_enseigne}", name="affilier", methods={"GET"})
     */
    public function affilier(Request $request, $id_enseigne, HttpClientInterface $client, FlashyNotifier $flashy)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $response = $client->request('POST', $this->getParameter('API_URL') . $this->apiAffilierEnseigneCommunaute, [
            'query' => [
                'id_communaute' => $this->getParameter('ID_COMMUNAUTE'),
                'id_enseigne' => $id_enseigne
            ]
        ]);

        $content = $response->getContent();
        $content_array = json_decode($content, true);
        $isSuccess = $content_array['server_responses'];
        if ($isSuccess == 1) {
            $message = "Affiliation réussie";
            $enseigne = new EnseigneAffiliee();
            //Liste des enseignes de l'user connecté
            $myEnseignes = $client->request('POST', $this->getParameter('API_URL') . $this->apiMyEnseigneUrl, [
                'query' => [
                    'id_entreprise' => $user->getId()
                ]
            ]);

            $content = $myEnseignes->getContent();
            $content_array = json_decode($content, true);
            //throw new \Exception(var_dump($content_array), 1);

            $allMyEnseignes = [];
            if (key_exists(0, $content_array['server_responses'])) {
                if ($content_array['server_responses'][0]['founded'] == 1) {
                    $allMyEnseignes = $content_array['server_responses'];
                    //

                }
            }

            foreach ($allMyEnseignes as $value) {
                if ($value['id_enseigne'] == $id_enseigne) {
                    $enseigne->setIdEntreprise($user)
                        ->setNomEnseigne($value['nom_enseigne'])
                        ->setCodeEnseigne($value['code_enseigne'])
                        ->setDateAffiliation(new \DateTime('now'));
                    $enseigne->setId($value['id_enseigne']);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($enseigne);
                    $entityManager->flush();
                }
            }
        } elseif ($isSuccess == 0) {
            $message = "Echec de l'Affiliation";
        } elseif ($isSuccess == -1) {
            $message = "Affiliation déjà existante!";
        }

        $flashy->success($message);
        return $this->redirectToRoute('enseigne');
    }

    /**
     * @Route("/rejoindre/{id_enseigne}", name="rejoindre", methods={"GET"})
     */
    public function rejoindre(Request $request, HttpClientInterface $client, FlashyNotifier $flashy, UserJoinedEnseigneRepository $userJoinedEnseigneRepository, $id_enseigne, UniqueId $unique)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez-vous connecter');
            return $this->redirectToRoute('app_login');
        }

        //--ril--
        $ens = $this->getDoctrine()->getManager()->getRepository(EnseigneAffiliee::class)->findOneById($id_enseigne);
        if (is_null($ens)) {
            $flashy->error('Enseigne introuvable');
            return $this->redirectToRoute('enseigne');
        }
        //vérifier s'il avait rejoint au moins une enseigne
        $oneJoin = $this->getDoctrine()->getManager()->getRepository(UserJoinedEnseigne::class)->findByUser($user);
        $num = "1000_AA-00";
        if (!empty($oneJoin)) {
            //rejoint au  moins une fois
            $num = $oneJoin[0]->getIdentificationNumber();
        } else {
            //on prend le dernier ayant rejoint
            $all = $this->getDoctrine()->getManager()->getRepository(UserJoinedEnseigne::class)->findAll();
            if (!empty($all)) {
                $lastJoin = $all[count($all) - 1];
                $num = $unique->AssignUniqueId($lastJoin->getIdentificationNumber());
            }
        }
        //**
        $userJoinedEnseigne = new UserJoinedEnseigne();
        $userJoinedEnseigne->setIdEnseigne($id_enseigne)->setEnseigne($ens)
            ->setIdUser($user->getId())->setUser($user)->setIdentificationNumber($num);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($userJoinedEnseigne);
        $entityManager->flush();

        $message = "Succès de l'intégration";
        $flashy->success($message);
        return $this->redirectToRoute('enseigne');
    }
    /**
     * @Route("/manager/api", name="enseigne_manager", methods={"GET"})
     */
    public function manager(FlashyNotifier $flashy)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez-vous connecter');
            return $this->redirectToRoute('app_login');
        }


        return $this->render('enseigne/manager.html.twig', [
            'position' => 'Gestion enseigne',
            'chemin' => ' Enseigne  /  Gestion',
            'methode' => 'Gestion'
        ]);
    }

    /**
     * @Route("/{id_enseigne}/new/patient", name="patient_new", methods={"GET","POST"})
     */
    public function newPatient(HttpClientInterface $client, $id_enseigne, Request $request, FlashyNotifier $flashy, Swift_Mailer $mailer, UserPasswordEncoderInterface $encoder, UniqueId $unique): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }

        $patientApi = new PatientApi();
        $form = $this->createForm(PatientApiType::class, $patientApi);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            $hash = $encoder->encodePassword($patientApi, $patientApi->getMdp());
            $entityManager = $this->getDoctrine()->getManager();
            $patientApi->setCompte(1);
            $patient = $patientApi;

            $patientApi->setMdp($hash);
            $response = $client->request('POST', $this->api_url_new . $this->apiSignInUrl, [
                'query' => [
                    'isPatient' => true,
                    'nom' => $patient->getNom(),
                    'prenoms' => $patient->getPrenoms(),
                    'email' => $patient->getEmail(),
                    'type' => $patient->getCompte(),
                    'genre' => $patient->getGenre(),
                    'phoneNumber' => $patient->getTelephone(),
                    'passe' => $patient->getMdp(),
                    'conf_passe' => $patient->getMdp(),
                ]
            ]);

            

            //dd($patient->getCompte());

            $content = $response->getContent();
            $content_array = json_decode($content, true);
            $isSuccess = $content_array['server_responses'];
            if (count($isSuccess[0]) !== 0) {
                $userr = $isSuccess[0];
                $entityManager->persist($patientApi->setIdParticulier($userr['id_client'])->setIsPatient(0));
            
                $entityManager->flush();

                $responses = $client->request('POST', $this->getParameter('API_URL') . $this->apiJoinEnseigneUrl, [
                    'query' => [
                        'id_client' => $userr['id_client'],
                        'id_enseigne' => $id_enseigne
                    ]
                ]);

                $contentt = $responses->getContent();
                $contentt_array = json_decode($contentt, true);
                $isSuccesss = $contentt_array['server_response'];

                ///$message = "Succès de l'intégration";
                //$flashy->success($message);

                //dd($contentt_array);
                if (count($isSuccesss[0]) !== 0) {
                    $message = (new Swift_Message('Nouveau compte'))
                        ->setFrom('test.inetschools@gmail.com')
                        ->setTo($patient->getEmail())
                        ->setBody(
                            $this->renderView(
                                'email/patient.html.twig',
                                compact('patient')
                            ),
                            'text/html'
                        );

                    $mailer->send($message);
                    //dd($re);
                    $flashy->success('Patient créé avec success');
                    return $this->redirectToRoute('membre_inscription', ['id_enseigne' => $id_enseigne]);
                }
            }
        }

        return $this->render('patient_api/index.html.twig', [
            'medecin' => $patientApi,
            'form' => $form->createView(),
            'position' => 'Nouveau Patient',
            'chemin' => 'Patient / Nouveau Patient',
        ]);
    }

    /**
     * @Route("/membres/{id_enseigne}", name="membre", methods={"GET", "POST"})
     */
    public function membres($id_enseigne, Request $request, FlashyNotifier $flashy, UserJoinedEnseigneRepository $userJoinedEnseigneRepository, UserRepository $userRepository)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $session = new Session();
        //--ril--
        $ens = $this->getDoctrine()->getManager()->getRepository(EnseigneAffiliee::class)->findOneById($id_enseigne);
        if (is_null($ens)) {
            $flashy->error('Enseigne introuvable');
            return $this->redirectToRoute('enseigne');
        }
        //**
        $session->Set('id_enseigne', $id_enseigne);

        //utilisateur ayant rejoint l'enseigne
        $usersJoined = $userJoinedEnseigneRepository->findBy(['id_enseigne' => $id_enseigne]);
        $users = $userRepository->findAll();
        $usersArray = [];

        foreach ($usersJoined as $userJoined) {
            foreach ($users as $user) {
                if ($user->getId() == $userJoined->getIdUser()) {
                    $usersArray[$user->getId()] = $user;
                }
            }
        }

        return $this->render('enseigne/membres.html.twig', [
            'usersArray' => $usersArray,
            'usersJoined' => $usersJoined,
            'position' => 'Liste des membres',
            'chemin' => 'Enseigne  /  Liste des membres',
            'methode' => 'membre',
            'id_enseigne' => $id_enseigne
        ]);
    }

    /**
     * @Route("/membres_insciption/{id_enseigne}", name="membre_inscription", methods={"GET", "POST"})
     */
    public function membres_inscription(PatientApiRepository $patientApiRepository,Request $request, FlashyNotifier $flashy, $id_enseigne, UserJoinedEnseigneRepository $userJoinedEnseigneRepository, UserRepository $userRepository, ClassesRepository $classeRepo, InscriptionsRepository $inscriptionRepo)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $session = new Session();
        //--ril--
        $ens = $this->getDoctrine()->getManager()->getRepository(EnseigneAffiliee::class)->findOneById($id_enseigne);
        if (is_null($ens)) {
            $flashy->error('Enseigne introuvable');
            return $this->redirectToRoute('enseigne');
        }
        //**
        $session->set('id_enseigne', $id_enseigne);
        //tous les autres utilisateurs
        $parentsPossibles = $userJoinedEnseigneRepository->findBy(['id_enseigne' => $id_enseigne]);
        $parentsPossiblesArray = [];

        $classes = $classeRepo->findAll();

        
        //utilisateur ayant rejoint l'enseigne
        $usersJoined = $userJoinedEnseigneRepository->findByProfilesAndEnseigneField(['id_enseigne' => $id_enseigne, 'profile' => 'patient']);
        $users = $userRepository->findAll();
        $usersArray = [];

        $inscrits = $inscriptionRepo->findAll();
        $inscritsArray = [];
        $inscritsDetailsArray = [];
        foreach ($inscrits as $value) {
            foreach ($users as $userInsc) {
                if ($userInsc->getId() == $value->getIdEleve()->getId()) {
                    $inscritsArray[$userInsc->getId()] = true;
                    $pere = $userRepository->findOneBy(['id' => $value->getIdPere()]);
                    $mere = $userRepository->findOneBy(['id' => $value->getIdMere()]);
                    $tuteur = $userRepository->findOneBy(['id' => $value->getIdTuteur()]);
                    $classeEleve = $classeRepo->findOneBy(['id' => $value->getClasse()]);
                    $inscritsDetailsArray[$userInsc->getId()] = [
                        'pere' => $pere,
                        'mere' => $mere,
                        'tuteur' => $tuteur != null ? $tuteur : 'néan',
                        'classe' => $classeEleve
                    ];
                }
            }
        }

        foreach ($usersJoined as $userJoined) {
            foreach ($users as $userJ) {
                if ($userJ->getId() == $userJoined->getIdUser()) {
                    $usersArray[$userJ->getId()] = $userJ;
                }
            }
        }

        foreach ($parentsPossibles as $parent) {
            foreach ($users as $userP) {
                if ($userP->getId() == $parent->getIdUser()) {
                    $parentsPossiblesArray[$userP->getId()] = $userP;
                }
            }
        }
        $submittedToken = $request->request->get('_token');

        if ($submittedToken != null) {

            if ($this->isCsrfTokenValid('association-parent', $submittedToken)) {
                $pere = $request->request->get('pere');
                $mere = $request->request->get('mere');
                $tuteur = $request->request->get('tuteur') == '' ? 0 : $request->request->get('tuteur');
                $classe = $request->request->get('classe');
                //--ril
                $pereo = $userRepository->findOneBy(['id' => $pere]);
                $mereo = $userRepository->findOneBy(['id' => $pere]);
                $tuteuro = $userRepository->findOneBy(['id' => $tuteur]);
                //
                $exist = $inscriptionRepo->findOneBy([
                    'id_eleve' => $request->request->get('eleve'),
                    'id_pere' => $pere,
                    'id_mere' => $mere,
                    'id_tuteur' => $tuteur,
                    'classe' => $classe
                ]);
                $entityManager = $this->getDoctrine()->getManager();

                if ($exist) {
                    $exist->setIdEleve($userRepository->findOneBy(['id' => $request->request->get('eleve')]));
                    $exist->setIdPere($pere);
                    $exist->setIdMere($mere);
                    $exist->setIdTuteur($tuteur);
                    //*--ril--
                    $exist->setPere($pereo);
                    $exist->setPere($mereo);
                    $exist->setTuteur($tuteuro);
                    //
                    $exist->setClasse($classeRepo->findOneBy(['id' => $classe]));
                    $entityManager->flush();
                    $flashy->error("Inscription mis à jour");
                    return $this->redirectToRoute('membre_inscription', ['id_enseigne' => $session->get('id_enseigne')]);
                }

                $Inscription = new Inscriptions();
                $Inscription->setIdEleve($userRepository->findOneBy(['id' => $request->request->get('eleve')]));
                $Inscription->setIdPere($pere);
                $Inscription->setIdMere($mere);
                $Inscription->setIdTuteur($tuteur);
                //*--ril--
                $Inscription->setPere($pereo);
                $Inscription->setPere($mereo);
                $Inscription->setTuteur($tuteuro);
                //
                $Inscription->setClasse($classeRepo->findOneBy(['id' => $classe]));


                $entityManager->persist($Inscription);
                $entityManager->flush();

                $flashy->success("Inscription réussie");
                return $this->redirectToRoute('membre_inscription', ['id_enseigne' => $session->get('id_enseigne')]);
            } else {
                $flashy->success("Formulaire invalide");
                return $this->redirectToRoute('enseigne');
            }
        }



        return $this->render('enseigne/membres.html.twig', [
            'usersArray' => $usersArray,
            'usersJoined' => $usersJoined,
            'position' => 'Liste des patients',
            'chemin' => 'Enseigne  /  Liste des patients',
            'methode' => 'membre_inscription',
            'parentsPossiblesArray' => $parentsPossiblesArray,
            'classes' => $classes,
            'inscritsArray' => $inscritsArray,
            'inscritsDetailsArray' => $inscritsDetailsArray,
            'id_enseigne' => $id_enseigne,
            'patient_inviteds' => $patientApiRepository->findAll()
        ]);
    }

    /**
     * @Route("/membre_consultation_specialite/{id_enseigne}/{id_patient}", name="membre_consultation_specialite", methods={"GET"})
     */
    public function membreConsultationSpecialite(
        Request $request,
        FlashyNotifier $flashy,
        UserJoinedEnseigneRepository $userJoinedEnseigneRepository,
        UserRepository $userRepository,
        MatieresRepository $matieresRepository,
        $id_enseigne,
        $id_patient
    ) {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $typeEnsArrayy = $matieresRepository->findSpecialite($id_enseigne);
        // dd($typeEnsArrayy);

        return $this->render('enseigne/membre_specialite.html.twig', [
            'matieres' => $typeEnsArrayy,
            'position' => 'Liste des specialite de demande d\'orientation',
            'chemin' => 'Enseigne  /  Liste des specialite de demande d\'orientation',
            'id_enseigne' => $id_enseigne,
            'id_patient' => $id_patient
        ]);
    }
    /**
     * @Route("/membre_consultation_specialite/secretaire/{id_secretaire}/{id_enseigne}/{id_patient}/demande/{id_specialite}", name="membre_consultation_specialite_demande", methods={"GET"})
     */
    public function membreConsultationSpecialiteDemande(
        Request $request,
        FlashyNotifier $flashy,
        UserJoinedEnseigneRepository $userJoinedEnseigneRepository,
        UserRepository $userRepository,
        DemandeConsultationRepository $demandeConsultationRepository,
        $id_enseigne,
        $id_specialite,
        $id_patient,
        $id_secretaire
    ) {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // dd($typeEnsArrayy);
        $typeEnsArrayy = $demandeConsultationRepository->findBy(["id_enseigne" => $id_enseigne, "id_secretaire" => $id_secretaire, "id_patient" => $id_patient, "id_specialite" => $id_specialite]);

        //dd($typeEnsArrayy);
        return $this->render('enseigne/membre_specialite_demande.html.twig', [
            'demandes' => $typeEnsArrayy,
            'position' => 'Liste des specialite de demande d\'orientation',
            'chemin' => 'Enseigne  /  Liste des specialite de demande d\'orientation',
            'id_enseigne' => $id_enseigne,
            'id_specialite' => $id_specialite,
            'id_secretaire' => $id_secretaire,
            'id_patient' => $id_patient
        ]);
    }

    /**
     * @Route("/membre_consultation_specialite/secretaire/{id_secretaire}/{id_enseigne}/{id_patient}/demande/{id_specialite}/new", name="demande_new", methods={"GET","POST"})
     */
    public function new($id_secretaire, $id_enseigne, $id_patient, $id_specialite, Request $request, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }


        $demande = new DemandeConsultation();
        $form = $this->createForm(DemandeConsultationType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demande->setIdSecretaire($id_secretaire)
                ->setIdEnseigne($id_enseigne)
                ->setIdPatient($id_patient)
                ->setIdSpecialite($id_specialite)
                ->setIsValided(0);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($demande);
            $entityManager->flush();

            $flashy->success('Demande créé avec success');
            return $this->redirectToRoute('membre_consultation_specialite_demande', [
                'id_enseigne' => $id_enseigne,
                'id_specialite' => $id_specialite,
                'id_secretaire' => $id_secretaire,
                'id_patient' => $id_patient
            ]);
        }

        return $this->render('enseigne/new_demande.html.twig', [
            'demande' => $demande,
            'form' => $form->createView(),
            'position' => 'Nouveau demande de consultation',
            'chemin' => 'Demande de consultation / Nouveau demande de consultation',
        ]);
    }

    /**
     * @Route("/membre_consultation_specialite/secretaire/{id_secretaire}/{id_enseigne}/{id_patient}/demande/{id_specialite}/edit", name="demande_edit", methods={"GET","POST"})
     */
    public function editDemande($id_secretaire, $id_enseigne, $id_patient, $id_specialite, Request $request, DemandeConsultation $demandeConsultation, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(DemandeConsultationType::class, $demandeConsultation);
        $form->handleRequest($request);

        // dd($demandeConsultation);
        if ($form->isSubmitted() && $form->isValid()) {


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($demandeConsultation);
            $entityManager->flush();
            $flashy->success('Demande de consultaion modifié avec success');
            return $this->redirectToRoute('membre_consultation_specialite_demande', [
                'id_enseigne' => $id_enseigne,
                'id_specialite' => $id_specialite,
                'id_secretaire' => $id_secretaire,
                'id_patient' => $id_patient
            ]);
        }

        return $this->render('enseigne/edit_demande.html.twig', [
            'demandeConsultation' => $demandeConsultation,
            'form' => $form->createView(),
            'position' => 'Edition demande de consultation',
            'chemin' => 'Demande de consultation / Edition demande de consultation',
        ]);
    }

    /**
     * @Route("/profile", name="profile", methods={"POST"})
     */
    public function profiles(
        Request $request,
        FlashyNotifier $flashy,
        UserJoinedEnseigneRepository $userJoinedEnseigneRepository,
        UserRepository $userRepository
    ) {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $submittedToken = $request->request->get('_token');
        if ($submittedToken) {
            if ($this->isCsrfTokenValid('profiles-item' . $user->getId(), $submittedToken)) {
                $id_enseigne = $request->request->get('id_enseigne');
                $id_user = $request->request->get('id_user');
                $profiles = $request->request->get('profiles');

                $userJoined = $userJoinedEnseigneRepository->findOneBy(['id_enseigne' => $id_enseigne, 'id_user' => $id_user]);
                $userJoined->setProfiles($profiles);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $flashy->success("Profiles assigné(s) avec succès");
                return $this->redirectToRoute('membre', ['id_enseigne' => $id_enseigne]);
            } else {
                $session = new Session();
                $flashy->error("Formulaire invalide");
                return $this->redirectToRoute('membre', ['id_enseigne' => $session->get('id_enseigne')]);
            }
        }
    }

    /**
     * @Route("/droits", name="droits", methods={"POST"})
     */
    public function roles(Request $request, FlashyNotifier $flashy, UserJoinedEnseigneRepository $userJoinedEnseigneRepository, UserRepository $userRepository)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('droits-item' . $user->getId(), $submittedToken)) {
            $id_enseigne = $request->request->get('id_enseigne');
            $id_user = $request->request->get('id_user');
            $droits = $request->request->get('droits');


            $userJoined = $userJoinedEnseigneRepository->findOneBy(['id_enseigne' => $id_enseigne, 'id_user' => $id_user]);
            $userJoined->setDroits($droits);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $flashy->success("Droits assignés avec succès");
            return $this->redirectToRoute('membre', ['id_enseigne' => $id_enseigne]);
        } else {
            $session = new Session();

            $flashy->error("Formulaire invalide");
            return $this->redirectToRoute('membre', ['id_enseigne' => $session->get('id_enseigne')]);
        }
    }

    /**
     * @Route("/edit/", name="edit_enseigne", methods={"POST"})
     */

    public function edit(Request $request, FlashyNotifier $flashy, HttpClientInterface $client)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $submittedToken = $request->request->get('_token');


        $id_enseigne = $request->request->get('id_enseigne');
        if ($this->isCsrfTokenValid('edit-item' . $user->getId(), $submittedToken)) {
            $response = $client->request('POST', $this->getParameter('API_URL') . $this->apiUpdateEnseigne, [
                'query' => [
                    'nom_enseigne' => $request->request->get('nom_enseigne'),
                    'code_enseigne' => $request->request->get('code_enseigne'),
                    'id_enseigne' => $id_enseigne
                ]
            ]);

            $content = $response->getContent();
            $content_array = json_decode($content, true);

            $isSuccess = key_exists("server_response", $content_array) ? $content_array['server_response'][0]['status'] : 0;

            if ($isSuccess == 1) {
                $flashy->success("Mise à jour réussie");
                return $this->redirectToRoute('enseigne');
            } else {
                $flashy->error("Echec de la mise à jour");
                return $this->redirectToRoute('enseigne');
            }
        } else {
            $flashy->error("Formulaire invalide");
            return $this->redirectToRoute('membre', ['id_enseigne' => $id_enseigne]);
        }
    }

    /**
     * @Route("/typeEnseigne/", name="add_type", methods={"POST"})
     */
    public function addType(Request $request, FlashyNotifier $flashy, EnseigneTypeEnsRepository $enseigneTypeEnsRepository)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        $submittedToken = $request->request->get('_token');

        $id_enseigne = $request->request->get('id_enseigne');
        $entityManager = $this->getDoctrine()->getManager();

        if ($this->isCsrfTokenValid('type-item' . $user->getId(), $submittedToken)) {
            $types = $request->request->get('types');
            $enseigneId = $request->request->get('id_enseigne');

            $exist = $enseigneTypeEnsRepository->findBy(['id_enseigne' => $enseigneId]);
            if ($exist) {
                foreach ($exist as $value) {
                    $entityManager->remove($value);
                    $entityManager->flush();
                }
                /*$flashy->success("Type enseigne existant");
                    return $this->redirectToRoute('enseigne');*/
            }
            for ($i = 0; $i < sizeof($types); $i++) {

                $EnseigneTypeEns = new EnseigneTypeEns();
                $EnseigneTypeEns->setIdEnseigne($enseigneId);
                $EnseigneTypeEns->setIdTypeEns($types[$i]);
                $entityManager->persist($EnseigneTypeEns);
                $entityManager->flush();
            }





            $flashy->success("Type enseigne ajouté / mise à jour");
            return $this->redirectToRoute('enseigne');
        } else {
            $flashy->error("Formulaire invalide");
            return $this->redirectToRoute('membre', ['id_enseigne' => $id_enseigne]);
        }
    }

    /**
     * @Route("/niveauxEtudes", name="ajoute_niveau", methods={"POST"})
     */
    public function ajouterNiveau(Request $request, FlashyNotifier $flashy, EnseigneNiveauEtudeRepository $enseigneNiveauEtudeRepo)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }
        $submittedToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('niveau-item' . $user->getId(), $submittedToken)) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($enseigneNiveauEtde = $enseigneNiveauEtudeRepo->findBy(['id_enseigne' => $request->request->get('id_enseigne')])) {
                foreach ($enseigneNiveauEtde as $value) {
                    $entityManager->remove($value);
                    $entityManager->flush();
                }
            }
            $enseigneNiveauEtude = new EnseigneNiveauEtude();
            $enseigneNiveauEtude->setIdEnseigne($request->request->get('id_enseigne'))
                ->setNiveaux($request->request->get('niveaux'));

            $entityManager->persist($enseigneNiveauEtude);
            $entityManager->flush();

            $flashy->success("Niveaux d'étude bien ajouté / mise à jour");
            return $this->redirectToRoute('enseigne');
        } else {
            $session = new Session();
            $flashy->error("Formulaire invalide");
            return $this->redirectToRoute('membre', ['id_enseigne' => $session->get('id_enseigne')]);
        }
    }

    /**
     * @Route("/matieresEnseigne/{id_enseigne}", name="matieresEnseigne", methods={"GET"})
     */
    public function matiereEnseigneList(Request $request, FlashyNotifier $flashy, $id_enseigne, EnseigneMatiereRepository $enseigneMatiereRepository, MatieresRepository $matieresRepository, AssocMatiereNiveauRepository $assocMatiereNiveauRepo, EnseigneTypeEnsRepository $enseigneTypeEnsRepository, TypeEnsRepository $typeEnsRepo)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        $session  = new Session();
        $session->set('id_enseigne', $id_enseigne);


        //$TypeEns = $typeEnsRepo->findBy(['active' => true]);
        $matieres = $matieresRepository->findBy(['etat' => true]);
        //dd($matieres);
        $enseigneTypeEns = $enseigneMatiereRepository->findBy(['id_enseigne' => $id_enseigne]);
        $typeEnsArray =  array();
        $typeEnsArrayy = $matieresRepository->findSpecialite($id_enseigne);
        if (count($enseigneTypeEns) != 0) {
            //dd($typeEnsArrayy);
            foreach ($enseigneTypeEns as $value) {

                array_push($typeEnsArray, $matieresRepository->findOneBy(['id' => $value->getIdMatiere()]));
                /*foreach ($matieres as $value2) {
                    if ($value->getIdMatiere() != $value2->getId()) {
                        $typeEnsArrayy[] = $value2;
                    }
                }*/
            }
            //dd($typeEnsArray);
        } else {
            $typeEnsArray = $matieres;
        }

        return $this->render('enseigne/matieres_enseigne.html.twig', [
            'position' => 'Spécialité',
            'chemin' => 'Enseigne / Spécialité',
            'assocMatiereNiveauAll' => count($enseigneTypeEns) != 0 ? $typeEnsArray : [],
            'id_enseigne' => $id_enseigne,
            'TypeEns' => $typeEnsArrayy,
            'typeEnsArray' => $typeEnsArrayy
        ]);
    }

    /**
     * @Route("/matieresTypeEns", name="matiere_type", methods={"POST"})
     */
    public function matiereType(Request $request, FlashyNotifier $flashy, EnseigneMatiereRepository $enseigneMatiereRepository)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        if ($request->request->get('_token') !== null) {
            $submittedToken = $request->request->get('_token');
            if ($this->isCsrfTokenValid('new-item', $submittedToken)) {
                $session = new Session();
                $session->set('id_typeEns', $request->request->get('types'));
                $entityManager = $this->getDoctrine()->getManager();

                $ensMat = new EnseigneMatiere();
                $ensMat->setIdEnseigne($request->request->get('id_enseigne'))->setIdMatiere($request->request->get('types'))->setCoeff(0)->setHoraire(0);
                $entityManager->persist($ensMat);
                $entityManager->flush();
                //dd($ensMat);

                $flashy->success("Specialité associé avec succès");
                return $this->redirectToRoute('matieresEnseigne', ['id_enseigne' => $request->request->get('id_enseigne')]);
            } else {
                $flashy->error("Formulaire invalide");
                return $this->redirectToRoute('enseigne');
            }
        } else {
            $flashy->error("Erreur");
            return $this->redirectToRoute('enseigne');
        }
    }


    /**
     * @Route("/matieresEnseigne_new", name="matieresEnseigne_new", methods={"POST", "GET"})
     */
    public function matiereEnseigneNew(Request $request, FlashyNotifier $flashy, EnseigneTypeEnsRepository $enseigneTypeEnsRepository, TypeEnsRepository $typeEnsRepo, SeriesRepository $seriesRepo, MatieresRepository $matieresRepo, AssocMatiereNiveauRepository $assocMatiereNiveauRepo, NiveauxEtudesRepository $niveauEtudesRepo)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        $session = new Session();

        $id_enseigne = $session->get('id_enseigne');
        $id_typeEns = $session->get('id_typeEns');

        //$typeEnsOfEnseigne = $enseigneTypeEnsRepository->findOneBy(['id_enseigne' => $id_enseigne]);
        $typeEnseigneConcerned  = $typeEnsRepo->findOneBy(['id' => $id_typeEns]);

        $series = $seriesRepo->findAll();
        $matieres = $matieresRepo->findBy(['etat' => true]);

        if ($request->request->get('_token') !== null) {
            $submittedToken = $request->request->get('_token');
            if ($this->isCsrfTokenValid('new-association-matiere', $submittedToken)) {
                $niveau = $request->request->get('niveau');
                $serie = null;
                if ($request->request->get('serie') !== null) {
                    $serie = $seriesRepo->findOneBy(['id' => $request->request->get('serie')]);
                }
                $matiere = $request->request->get('matiere');
                $assocMatiereNiveau = new AssocMatiereNiveau();
                $assocMatiereNiveau->setNiveauEtude($niveauEtudesRepo->findOneBy(['id' => $niveau]));
                $assocMatiereNiveau->setSerie($serie);
                $assocMatiereNiveau->setMatiere($matieresRepo->findOneBy(['id' => $matiere]));
                $assocMatiereNiveau->setIdEnseigne($id_enseigne);
                $assocMatiereNiveau->setCoef($request->request->get('coef'));
                $assocMatiereNiveau->setHoraire($request->request->get('horaire'));

                if ($exist = $assocMatiereNiveauRepo->findBy([
                    'id_enseigne' => $id_enseigne,
                    'matiere' => $assocMatiereNiveau->getMatiere(),
                    'niveauEtude' => $assocMatiereNiveau->getNiveauEtude(),
                ])) {
                    $flashy->warning("Association existe déjà");
                    return $this->redirectToRoute('matieresEnseigne', ['id_enseigne' => $id_enseigne]);
                } else {
                    $entityManager = $this->getDoctrine()->getManager();

                    $entityManager->persist($assocMatiereNiveau);
                    $entityManager->flush();
                    $flashy->success("Association réussie");
                    return $this->redirectToRoute('matieresEnseigne', ['id_enseigne' => $id_enseigne]);
                }
            } else {
                $flashy->error("Formulaire invalide");
                return $this->redirectToRoute('matieresEnseigne', ['id_enseigne' => $id_enseigne]);
            }
        }

        return $this->render('enseigne/matieres_enseigne_new.html.twig', [
            'position' => 'Matières',
            'chemin' => 'Enseigne / Matières',
            'typeEnseigneConcerned' => $typeEnseigneConcerned,
            'series' => $series,
            'matieres' => $matieres,
            'id_enseigne' => $id_enseigne
        ]);
    }



    /**
     * @Route("/classe_new", name="classe_new", methods={"GET", "POST"})
     */
    public function classeNew(Request $request, FlashyNotifier $flashy, EnseigneTypeEnsRepository $enseigneTypeEnsRepository, TypeEnsRepository $typeEnsRepo, SeriesRepository $seriesRepo, UserRepository $userRepo, UserJoinedEnseigneRepository $userJoinedEnseigneRepository, NiveauxEtudesRepository $niveauEtudesRepo, ClassesRepository $classeRepo)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        $session = new Session();
        $id_enseigne = $session->get('id_enseigne');
        //--ril--
        $ens = $this->getDoctrine()->getManager()->getRepository(EnseigneAffiliee::class)->findOneById($id_enseigne);
        if (is_null($ens)) {
            $flashy->error('Enseigne introuvable');
            return $this->redirectToRoute('enseigne');
        }
        $id_typeEns = $session->get('id_typeEns');

        //$typeEnsOfEnseigne = $enseigneTypeEnsRepository->findOneBy(['id_enseigne' => $id_enseigne]);
        $typeEnseigneConcerned  = $typeEnsRepo->findOneBy(['id' => $id_typeEns]);

        $series = $seriesRepo->findAll();
        $users = $userRepo->findAll();

        $resultatUser = [];
        $usersJoinedEnseigne = $userJoinedEnseigneRepository->findByProfilesField('enseignant');
        foreach ($users as $value) {
            foreach ($usersJoinedEnseigne as $value2) {
                if ($value2->getIdUser() == $value->getId()) {
                    $resultatUser[] = $value;
                }
            }
        }


        if ($request->request->get('_token') !== null) {
            $submittedToken = $request->request->get('_token');
            if ($this->isCsrfTokenValid('new-classe', $submittedToken)) {
                $niveau = $request->request->get('niveau');
                $serie = null;
                if ($request->request->get('serie') !== null) {
                    $serie = $seriesRepo->findOneBy(['id' => $request->request->get('serie')]);
                }
                $prof_princ = $request->request->get('prof_princ');
                $classe = new Classes();
                $classe->setNiveau($niveauEtudesRepo->findOneBy(['id' => $niveau]));
                $classe->setSerie($serie);
                $classe->setProfesseurPrincipale($userRepo->findOneBy(['id' => $prof_princ]));
                $classe->setIdEnseigne($id_enseigne);
                $classe->setEnseigne($ens);
                $classe->setLibelle($request->request->get('libelle'));

                $exist = $classeRepo->findOneBy([
                    'niveau' => $classe->getNiveau(),
                    'serie' => $serie,
                    'professeur_principale' => $classe->getProfesseurPrincipale(),
                    'id_enseigne' => $id_enseigne,
                    'libelle' => $request->request->get('libelle')
                ]);

                if ($exist) {
                    $flashy->warning("Classe existe déjà");
                    return $this->redirectToRoute('enseigne');
                }

                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($classe);
                $entityManager->flush();
                $flashy->success("Création de classe réussie");
                return $this->redirectToRoute('classe_index', ['id_enseigne' => $id_enseigne]);
            } else {
                $flashy->error("Formulaire invalide");
                return $this->redirectToRoute('classe_index', ['id_enseigne' => $id_enseigne]);
            }
        }

        return $this->render('enseigne/classes_enseigne_new.html.twig', [
            'position' => 'Nouveau',
            'chemin' => 'Classe / Nouveau',
            'id_enseigne' => $id_enseigne,
            'typeEnseigneConcerned' => $typeEnseigneConcerned,
            'series' => $series,
            'resultatUser' => $resultatUser
        ]);
    }
    /**
     * @Route("/matieresEnseigne_edit/{id_matiere}", name="matieresEnseigne_edit", methods={"POST", "GET"})
     */
    public function matiereEnseigneEdit($id_matiere, Request $request, EnseigneMatiere $enseigneMatiere, MatieresRepository $matieresRepo, FlashyNotifier $flashy)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        $session = new Session();

        $id_enseigne = $session->get('id_enseigne');
        //$typeEnsOfEnseigne = $enseigneTypeEnsRepository->findOneBy(['id_enseigne' => $id_enseigne]);
        //$typeEnseigneConcerned  = $typeEnsRepo->findOneBy(['id' => $typeEnsOfEnseigne->getIdTypeEns()]);

        $matieres = $matieresRepo->findBy(['etat' => true]);

        if ($request->request->get('_token') !== null) {
            $submittedToken = $request->request->get('_token');
            if ($this->isCsrfTokenValid('edit-association-matiere', $submittedToken)) {
                //$niveau = $request->request->get('niveau');
                //$serie = null;
                /*if ($request->request->get('serie') !== null) {
                    $serie = $seriesRepo->findOneBy(['id' => $request->request->get('serie')]);
                }*/
                $matiere = $request->request->get('matiere');
                $enseigneMatiere->setIdMatiere($matieresRepo->findOneBy(['id' => $matiere])->getId());
                /*$assocMatiereNiveau->setNiveauEtude($niveauEtudesRepo->findOneBy(['id' => $niveau]));
                $assocMatiereNiveau->setSerie($serie);
                $assocMatiereNiveau->setIdEnseigne($id_enseigne);
                $assocMatiereNiveau->setCoef($request->request->get('coef'));
                $assocMatiereNiveau->setHoraire($request->request->get('horaire'));*/

                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->flush();
                $flashy->success("Edition Association réussie");
                return $this->redirectToRoute('matieresEnseigne', ['id_enseigne' => $id_enseigne]);
            } else {
                $flashy->error("Formulaire invalide");
                return $this->redirectToRoute('matieresEnseigne', ['id_enseigne' => $id_enseigne]);
            }
        }

        return $this->render('enseigne/matieres_enseigne_edit.html.twig', [
            'position' => 'Edition',
            'chemin' => 'Spécialité / Edition',
            'matieres' => $matieres,
            'id_enseigne' => $id_enseigne,
            'id_matiere' => $id_matiere,
            'enseigneMatiere' => $enseigneMatiere
        ]);
    }

    /**
     * @Route("/classe_index/{id_enseigne}", name="classe_index", methods={"GET", "POST"})
     */
    public function classeIndex(Request $request, FlashyNotifier $flashy, $id_enseigne, ClassesRepository $classeRepo, TypeEnsRepository $typeEnsRepo, EnseigneTypeEnsRepository $enseigneTypeEnsRepository)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        $session  = new Session();
        $session->set('id_enseigne', $id_enseigne);

        $classes = $classeRepo->findBy(['id_enseigne' => $id_enseigne]);
        $TypeEns = $typeEnsRepo->findBy(['active' => true]);
        $enseigneTypeEns = $enseigneTypeEnsRepository->findBy(['id_enseigne' => $id_enseigne]);
        $typeEnsArray = [];
        foreach ($enseigneTypeEns as $value) {
            foreach ($TypeEns as $value2) {
                if ($value->getIdTypeEns() == $value2->getId())
                    $typeEnsArray[] = $value2;
            }
        }


        return $this->render('enseigne/classes_enseigne_index.html.twig', [
            'position' => 'Classe',
            'chemin' => 'Classe',
            'classes' => $classes,
            'id_enseigne' => $id_enseigne,
            'TypeEns' => $TypeEns,
            'typeEnsArray' => $typeEnsArray
        ]);
    }

    /**
     * @Route("/classe_edit/{id}", name="classe_edit", methods={"GET", "POST"})
     */
    public function classeEdit(Request $request, FlashyNotifier $flashy, Classes $classe, EnseigneTypeEnsRepository $enseigneTypeEnsRepository, TypeEnsRepository $typeEnsRepo, SeriesRepository $seriesRepo, UserRepository $userRepo, UserJoinedEnseigneRepository $userJoinedEnseigneRepository, NiveauxEtudesRepository $niveauEtudesRepo, ClassesRepository $classeRepo)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        $session = new Session();

        $id_enseigne = $session->get('id_enseigne');
        //--ril--
        $ens = $this->getDoctrine()->getManager()->getRepository(EnseigneAffiliee::class)->findOneById($id_enseigne);
        if (is_null($ens)) {
            $flashy->error('Enseigne introuvable');
            return $this->redirectToRoute('enseigne');
        } //********
        $id_typeEns = $session->get('id_typeEns');



        $typeEnseigneConcerned = $typeEnsRepo->findOneBy(['id' => $id_typeEns]);



        $series = $seriesRepo->findAll();
        $users = $userRepo->findAll();

        $resultatUser = [];
        $usersJoinedEnseigne = $userJoinedEnseigneRepository->findByProfilesField('enseignant');
        foreach ($users as $value) {
            foreach ($usersJoinedEnseigne as $value2) {
                if ($value2->getIdUser() == $value->getId()) {
                    $resultatUser[] = $value;
                }
            }
        }

        if ($request->request->get('_token') !== null) {

            $submittedToken = $request->request->get('_token');

            if ($this->isCsrfTokenValid('edit-classe', $submittedToken)) {

                $niveau = $request->request->get('niveau');
                $serie = null;
                if ($request->request->get('serie') !== null) {
                    $serie = $seriesRepo->findOneBy(['id' => $request->request->get('serie')]);
                }
                $prof_princ = $request->request->get('prof_princ');

                $classe->setNiveau($niveauEtudesRepo->findOneBy(['id' => $niveau]));
                $classe->setSerie($serie);
                $classe->setProfesseurPrincipale($userRepo->findOneBy(['id' => $prof_princ]));
                $classe->setIdEnseigne($id_enseigne);
                //--ril
                $classe->setEnseigne($ens);
                $classe->setLibelle($request->request->get('libelle'));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                $flashy->success("Edition de la classe réussie");
                return $this->redirectToRoute('classe_index', ['id_enseigne' => $id_enseigne]);
            } else {
                $flashy->error("Formulaire invalide");
                return $this->redirectToRoute('classe_index', ['id_enseigne' => $id_enseigne]);
            }
        }

        return $this->render('enseigne/classes_enseigne_edit.html.twig', [
            'position' => 'Edition',
            'chemin' => 'Classe / Edition',
            'id_enseigne' => $id_enseigne,
            'typeEnseigneConcerned' => $typeEnseigneConcerned,
            'series' => $series,
            'resultatUser' => $resultatUser,
            'classe' => $classe
        ]);
    }

    /**
     * @Route("/classeTypeEns", name="classe_type", methods={"POST"})
     */
    public function classeType(Request $request, FlashyNotifier $flashy)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        if ($request->request->get('_token') !== null) {
            $submittedToken = $request->request->get('_token');
            if ($this->isCsrfTokenValid('new-item', $submittedToken)) {
                $session = new Session();
                $session->set('id_typeEns', $request->request->get('types'));
                return $this->redirectToRoute('classe_new');
            } else {
                $flashy->error("Formulaire invalide");
                return $this->redirectToRoute('enseigne');
            }
        } else {
            $flashy->error("Erreur");
            return $this->redirectToRoute('enseigne');
        }
    }

    /**
     * @Route("/medecin/{id_enseigne}/invitation", name="invitaion_medecin", methods={"GET"})
     */
    public function medecinInvitation($id_enseigne, Request $request, FlashyNotifier $flashy, MedecinRepository $medecinRepository, InvitationMedecinRepository $invitationMedecinRepository)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $liaisonCM =  $entityManager->getRepository(InvitationMedecin::class)->findOneBy(['id_enseigne' => $id_enseigne]);
        $medecins_new = array();
        if ($liaisonCM !== null) {
            //$entityManager->persist($liaisonCM);
            $medecins = $medecinRepository->findAll();

            foreach ($medecins as $medecin) {
                //$liaisonCM =  $entityManager->getRepository(LiaisonCentreSante::class)->findOneBy(['id_medecin' => $medecin->getId()]);
                $results = $invitationMedecinRepository->findInvitaion($id_enseigne, $medecin->getId());

                if (count($results) !== 0) {
                    foreach ($results as $result) {
                        $medecin->setIsInvited($result->getIsChecked());
                        array_push($medecins_new, $medecin);
                    }
                } else {
                    array_push($medecins_new, $medecin);
                }
            }
            //dd($medecins_new);  
            //$flashy->success('Enseigne deliéé avec succès!');
        } else {
            $medecins_new  = $medecinRepository->findAll();

            //$flashy->error('Impossible de deliéé !');
        }
        //dd($medecins_new);


        return $this->render('enseigne/invitationListe.html.twig', [
            'position' => 'Invitation',
            'chemin' => 'Liste / Invitation',
            'medecins' => $medecins_new,
            'id_enseigne' => $id_enseigne
        ]);
    }
    /**
     * @Route("/medecin/{id_enseigne}/colaborateur", name="colaborateur", methods={"GET"})
     */
    public function collaborateur($id_enseigne, Request $request, FlashyNotifier $flashy, MedecinRepository $medecinRepository, InvitationMedecinRepository $invitationMedecinRepository)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $liaisonCM =  $entityManager->getRepository(InvitationMedecin::class)->findOneBy(['id_enseigne' => $id_enseigne]);
        $medecins_new = array();
        if ($liaisonCM !== null) {
            //$entityManager->persist($liaisonCM);
            $medecins = $medecinRepository->findAll();

            foreach ($medecins as $medecin) {
                //$liaisonCM =  $entityManager->getRepository(LiaisonCentreSante::class)->findOneBy(['id_medecin' => $medecin->getId()]);
                $results = $invitationMedecinRepository->findInvitaion($id_enseigne, $medecin->getId());

                if (count($results) !== 0) {
                    foreach ($results as $result) {
                        $medecin->setIsInvited($result->getIsChecked());
                        array_push($medecins_new, $medecin);
                    }
                } else {
                    array_push($medecins_new, $medecin);
                }
            }
            //dd($medecins_new);  
            //$flashy->success('Enseigne deliéé avec succès!');
        } else {
            $medecins_new  = $medecinRepository->findAll();

            //$flashy->error('Impossible de deliéé !');
        }
        //dd($medecins_new);


        return $this->render('enseigne/collaborateur.html.twig', [
            'position' => 'Colaborateur',
            'chemin' => 'Liste / Colaborateur',
            'medecins' => $medecins_new,
            'id_enseigne' => $id_enseigne
        ]);
    }
    /**
     * @Route("/medecin/{id_enseigne}/invitation/{id_medecin}", name="add_invitaion_medecin", methods={"GET"})
     */
    public function addMedecinInvitation($id_enseigne, $id_medecin, Request $request, FlashyNotifier $flashy, MedecinRepository $medecinRepository, InvitationMedecinRepository $invitationMedecinRepository)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        $invitationMedecin = new InvitationMedecin();

        $invitationMedecin->setIdEnseigne($id_enseigne);
        $invitationMedecin->setIdMedecin($id_medecin);
        $invitationMedecin->setIsChecked(false);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($invitationMedecin);

        $entityManager->flush();
        $flashy->success('Médécin inviter avec succès!');


        return $this->redirectToRoute('invitaion_medecin', ['id_enseigne' => $id_enseigne]);
    }

    /**
     * @Route("/medecin/{id_enseigne}/invitation/{id_medecin}/deliaison", name="ensseigne_invitaion_deliaison", methods={"GET","POST"})
     */
    public function deliaisonEntreprise($id_enseigne, $id_medecin, FlashyNotifier $flashy, InvitationMedecinRepository $invitationMedecinRepository): Response
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
        $results = $invitationMedecinRepository->findInvitaion($id_enseigne, $id_medecin);

        if ($results !== null) {
            $entityManager->remove($results[0]);
            //$entityManager->persist($liaisonCM);
            $entityManager->flush();

            $flashy->success('Liaison rompue avec succès!');
        } else {
            $flashy->error('Impossible de desinviter !');
        }

        return $this->redirectToRoute('invitaion_medecin', ['id_enseigne' => $id_enseigne]);
    }
    /**
     * @Route("/medecin/{id_enseigne}/colaborateur/{id_medecin}/deliaison", name="ensseigne_colaborateur_deliaison", methods={"GET","POST"})
     */
    public function deliaisonColaborateur($id_enseigne, $id_medecin, FlashyNotifier $flashy, InvitationMedecinRepository $invitationMedecinRepository): Response
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
        $results = $invitationMedecinRepository->findInvitaion($id_enseigne, $id_medecin);

        if ($results !== null) {
            $entityManager->remove($results[0]);
            //$entityManager->persist($liaisonCM);
            $entityManager->flush();

            $flashy->success('Liaison rompue avec succès!');
        } else {
            $flashy->error('Impossible de desinviter !');
        }

        return $this->redirectToRoute('colaborateur', ['id_enseigne' => $id_enseigne]);
    }

    /**
     * @Route("/classe/addProfesseur/{id_user}/{id_classe}/{id_matiere}", name="classe_add_enseignant", methods={"GET"})
     * @Entity("classe", expr="repository.find(id_classe)")
     * @Entity("professeur", expr="repository.find(id_user)")
     * @Entity("matiere", expr="repository.find(id_matiere)")
     */
    public function classeAddProfesseur(Request $request, FlashyNotifier $flashy, User $professeur, Classes $classe, Matieres $matiere)
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        $classe->addProfesseur($professeur);
        $classe->addMatiere($matiere);
        //$user->addProfesseurOfClass($classe);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        $session = new Session();
        $id_enseigne = $session->get('id_enseigne');
        $flashy->success('Professeur bien lié à la classe');
        return $this->redirectToRoute('classe_index', ['id_enseigne' => $id_enseigne]);
    }

    /**
     * @Route("/dashboard/details/{id_enseigne}", name="statistique")
     */
    public function statistique(
        $id_enseigne,
        FlashyNotifier $flashy,
        UserJoinedEnseigneRepository $userJoinedEnseigneRepository,
        CoursRepository $coursRepository,
        EnseigneAffilieeRepository $enseigneAffilieeRepository
    ) {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }


        //Obtention du profile de l'utilisateur connecté
        $joinedDetails = $userJoinedEnseigneRepository->findOneBy(['id_user' => $user->getId(), 'id_enseigne' => $id_enseigne]);


        $nbr_ecole = 0;
        $nbr_cours = 0;
        $nbr_classes = [];
        $nbr_evaluation = 0;
        //Les points de l'éléve dans l'enseigne
        $mes_points = 0;

        //Les abscences de l'élève dans l'enseigne
        $mes_abscences = 0;


        //Les retards
        $mes_retards = 0;

        //Les avis
        $mes_avis = count($this->getUser()->getAvis());

        //Les points du parent dans l'enseigne
        $mes_points_parent = 0;

        //Les abscences du parent dans l'enseigne
        $mes_abscences_parent = 0;


        //Les retards
        $mes_retards_parent  = 0;

        //Les validations
        $mes_validations = 0;

        if (in_array("enseignant", $joinedDetails->getProfiles())) {
            $nbr_ecole++;

            //Nombre de cours
            $cours = $coursRepository->findBy(['id_prof' => $user->getId(), 'id_enseigne' => $id_enseigne]);
            $nbr_cours = count($cours);

            // Nombre d'évaluation
            $nbr_evaluation = count($this->getUser()->getEvaluations());

            //Nombre de classe

            foreach ($cours as $value) {
                if (in_array($value->getIdClasse()->getId(), $nbr_classes)) {
                    continue;
                } else
                    $nbr_classes[] = $value->getIdClasse()->getId();
            }
        }
        if (in_array("eleve", $joinedDetails->getProfiles())) {
        }
        if (in_array("parent", $joinedDetails->getProfiles())) {
        }




        return $this->render("dashboard/stats.html.twig", [
            "nbr_cours" => $nbr_cours,
            "nbr_ecole" => $nbr_ecole,
            "nbr_classes" => count($nbr_classes),
            "nbr_evaluation" => $nbr_evaluation,
            "mes_points" => $mes_points,
            "mes_retards" => $mes_retards,
            "mes_abscences" => $mes_abscences,
            "mes_avis" => $mes_avis,
            "mes_points_parent" => $mes_points_parent,
            "mes_abscences_parent" => $mes_abscences_parent,
            "mes_retards_parent" => $mes_retards_parent,
            "mes_validations" => $mes_validations,
            "detailsUser" => $joinedDetails
        ]);
    }
}
