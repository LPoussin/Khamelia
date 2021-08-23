<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Cours;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ClassesRepository;
use App\Repository\AssocMatiereNiveauRepository;
use App\Repository\AvisRepository;
use App\Repository\CoursRepository;
use App\Repository\InscriptionsRepository;
use App\Repository\MatieresRepository;
use App\Repository\ProfesseurMatiereRepository;
use App\Repository\UserJoinedEnseigneRepository;
use App\Repository\UserRepository;
use \Swift_Mailer;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CoursController extends AbstractController
{
    private $apiAllEnseigneAffilierUrl = 'all_enseigne_affilier.php';


    /**
     * @Route("/cours/{id_enseigne}", name="cours")
     */
    public function index(Request $request, FlashyNotifier $flashy, 
        ClassesRepository $classesRepository, $id_enseigne, AssocMatiereNiveauRepository $assocMatiereNiveauRepository, 
        MatieresRepository $matieresRepository, \Swift_Mailer $mailer, UserRepository $userRepository, 
        HttpClientInterface $client, ProfesseurMatiereRepository $professeurMatiereRepository): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }

        $allEnseigneClasses = $classesRepository->findBy(['id_enseigne' => $id_enseigne]);
        $profMatiere = $professeurMatiereRepository->findOneBy(['id_prof' => $user->getId(), 'id_enseigne' => $id_enseigne]);
        $allEnseigneMatiere = $profMatiere ? $profMatiere->getMatieres() : [];

        $submittedToken = $request->request->get('_token');
        if ($submittedToken != null) {
            if ($this->isCsrfTokenValid('nouveau-cours', $submittedToken)) {
                $classe = $classesRepository->findOneBy(['id' => $request->request->get('classe')]);
                $matiere = $matieresRepository->findOneBy(['id' => $request->request->get('matiere')]);
                $date_cours = $request->request->get('date_cours'); //date('d-m-Y',$request->request->get('date_cours'));
                $datetime_debut = $request->request->get('datetime_debut');
                $datetime_fin = $request->request->get('datetime_fin');                
                $dure_cours =  $request->request->get('dure_cours');
                $libelle = $request->request->get('libelle');
                $description =  $request->request->get('description');
                $document = $request->files->get('document');
                $newFileName = "";
                if($document)
                {
                    $originalName = pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFileName = $originalName;
                    $newFileName = $safeFileName.'-'.uniqid().'.'.$document->guessExtension();
                    try {
                    
                        $document->move($this->getParameter('DOCUMENT_COURS'), $newFileName);
    
                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage(), 1);
                        
                    } 
                }

                $cours = new Cours();
                $cours->setIdProf($user);
                $cours->setIdClasse($classe);
                $cours->setIdMatiere($matiere);
                $cours->setDateCours(new \DateTime($date_cours));
                $cours->setDatetimeDebut(new \DateTime($datetime_debut));
                $cours->setDatetimeFin(new \DateTime($datetime_fin));
                $cours->setDureCours($dure_cours);
                $cours->setLibelle($libelle);
                $cours->setDescription($description);
                $cours->setDocument($newFileName);
                $cours->setIdEnseigne($id_enseigne);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($cours);
                $entityManager->flush();
                //Envoie de notifivation aux élèves et parents

                //obtenir la liste des élèves d'une classe
                //$inscitOfEnseigne = $inscriptionsRepository->findBy(['id_classe' => $request->request->get('classe')]);
                $inscitOfEnseigne = $classe->getInscriptions();
                $arrayEmail = [];
                foreach ($inscitOfEnseigne as $value) {
                   $arrayEmail[] = $value->getIdEleve()->getEmail();

                   //pere info
                   $pere = $userRepository->findOneBy(['id' => $value->getIdPere()]);
                   $arrayEmail[] = $pere->getEmail();

                   //mere info
                   $mere = $userRepository->findOneBy(['id' => $value->getIdMere()]);
                   $arrayEmail[] = $mere->getEmail();

                   //tuteur info
                    if($value->getIdTuteur() != null)
                    {
                        $tuteur = $userRepository->findOneBy(['id' => $value->getIdTuteur()]);
                        $arrayEmail[] = $tuteur->getEmail();
                    }
                }
                
                //enseigne info pour le mail
                $enseigneData = null;
                //Liste des enseignes affilié à InetSCHOOLS
                $responseEnseigneAffilier = $client->request('POST', $this->getParameter('API_URL').$this->apiAllEnseigneAffilierUrl, [
                    'query' => [
                        'id_communaute' => $this->getParameter('ID_COMMUNAUTE')
                    ]
                ]);

                $content = $responseEnseigneAffilier->getContent();
                $content_array = json_decode($content, true);
                $AllEnseigneAffiliees= array_key_exists("0", $content_array['server_responses'])  && $content_array['server_responses'][0]['founded'] === 0 ? [] : $content_array['server_responses'];
                foreach ($AllEnseigneAffiliees as $enseigneAffiliee) 
                {
                    if($id_enseigne == $enseigneAffiliee['id_enseigne'])
                        $enseigneData = $enseigneAffiliee;
                }
                //throw new \Exception(var_dump($arrayEmail), 1);
                
                $message = (new \Swift_Message('Nouveau cours'))
                    ->setFrom('darellnet2all@gmail.com')
                    ->setBcc($arrayEmail)
                    ->setBody(
                        $this->renderView('email/new_cours.html.twig', [
                            'cours' => $cours->getLibelle(),
                            'date_cours' => $cours->getDateCours()->format('m-d-Y'),
                            'debut_heure' => $cours->getDatetimeDebut()->format('H:i'),
                            'fin_heure' => $cours->getDatetimeFin()->format('H:i'),
                            'description' => $cours->getDescription(),
                            'classe' => $classe->getLibelle(),
                            'enseigne_nom' => $enseigneData['nom_enseigne'],
                            'id_enseigne' => $enseigneData['id_enseigne']
                        ]), 'text/html')
                    ->attach(\Swift_Attachment::fromPath($this->getParameter('DOCUMENT_COURS').'/'.$newFileName));
                $mailer->send($message);

                $flashy->success("Cours créé avec succès!");
                return $this->redirectToRoute('enseigne');

            } else {
                $flashy->error("Formulaire invalid");
                return $this->redirectToRoute("cours", ['id_enseigne' => $id_enseigne]);
            }
            
        } 
        

        return $this->render('cours/index.html.twig', [
            'controller_name' => 'CoursController',
            'position' => 'Cours',
            'chemin' => 'Enseigne  /  Cours',
            'allEnseigneClasses' => $allEnseigneClasses,
            'allEnseigneMatiere' => $allEnseigneMatiere
        ]);
    }

    /**
     * @Route("/timeline/{id_enseigne}", name="timeline", methods={"POST", "GET"})
     */
    public function timelineView(Request $request, FlashyNotifier $flashy, CoursRepository $coursRepository, $id_enseigne,
        UserJoinedEnseigneRepository $userJoinedEnseigneRepository) : Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }

        $submittedToken  = $request->request->get('_token');

        if($submittedToken)
        {
            if($this->isCsrfTokenValid('timeline-consulte', $submittedToken))
            {
                $id_classe = $request->request->get('classe');
                //récupération de tous les cours de l'enseigne et de la classe choisie
                $allCours = $coursRepository->findBy(['id_enseigne' => $id_enseigne, 'id_classe' => $id_classe]);
                
                //récupérer les profiles de l'user
                $UserDetails = $userJoinedEnseigneRepository->findOneBy(['id_enseigne' => $id_enseigne, 'id_user' => $user->getId()]);
                $session = new Session();
                $session->set('UserProfile', $UserDetails->getProfiles());

                return $this->render('cours/timeline.html.twig', [
                    'position' => 'Programme',
                    'chemin' => 'Programme',
                    'allCours' => $allCours,
                    'UserDetails' => $UserDetails
                ]);    
            }
            else{
                $flashy->error('Formualire invalide');
                return $this->redirectToRoute('enseigne');
            }
        }
        else
        {
            $flashy->error('Erreur du formulaire');
            return $this->redirectToRoute('enseigne');
        }
        

    }

   
}
