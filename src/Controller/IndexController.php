<?php

namespace App\Controller;

use App\Entity\EnseigneAffiliee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\CoursRepository;
use App\Repository\EnseigneAffilieeRepository;
use App\Repository\UserJoinedEnseigneRepository;
use Crypto\Cryptor;
use App\Repository\UserRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;




class IndexController extends AbstractController
{

    private $apiUpdateInfosEntreprise = 'update_infos_entreprise.php';
    private $apiUpdateInfosParticulier = 'update_infos.php';
    private $apiUpdatePassword = 'update_passe.php';
    private $apiAllEnseigneAffilierUrl = 'all_enseigne_affilier.php';
    //
    private $apiTotalItems='total_items_info.php';

    /**
     * @Route("/index", name="index", methods={"GET", "POST"})
     */
    public function index(Request $request)
    {
    	return $this->render('index.html.twig');
    }

    /**
     * @Route("/dashboard", name="dashboard", methods={"GET"})
     */
    public function dashboard(Request $request, HttpClientInterface $client, 
        UserJoinedEnseigneRepository $userJoinedEnseigneRepository, EnseigneAffilieeRepository $enseigneAffilieeRepository,
        CoursRepository $coursRepository)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        //
        
        $response = $client->request('POST', 
            $this->getParameter('API_URL').$this->apiTotalItems, [
            'body' => [
                
            ]
        ]);

        $content = $response->getContent();
        $content_array = json_decode($content, true);
        $totalEnseigne = (int)$content_array['nombreEnseigne'];
        $totalParticulier = (int)$content_array['nombreParticulier'];
        //
        //Nombre d'enseigne affiliée en (fonction) par du mois
        $AllEnseigneAffiliees = $enseigneAffilieeRepository->findAll();
        $enseigneParMois = [];
        $enseigneTotal = 0;
        foreach ($AllEnseigneAffiliees as $value) {
            if(key_exists($value->getDateAffiliation()->format('M'), $enseigneParMois)){
                $enseigneParMois[$value->getDateAffiliation()->format('M')] = $enseigneParMois[$value->getDateAffiliation()->format('M')] + 1;            }
            else{
                $enseigneParMois[$value->getDateAffiliation()->format('M')] = 1;
            }
            $enseigneTotal++;
        }

       //Nombre d'enseignant ayant rejoint l'enseigne par mois
        $AllUsersJoinedEnseigne = $userJoinedEnseigneRepository->findAll();
        $medecinsJoinedParMois = [];
        $medecinTotal = 0;
        $patientsJoinedParMois = [];
        $patientTotal = 0;
        $parentsJoinedParMois = [];
        $parentTotal = 0;
        //
        $users_id=[];
        $usersele_id=[];
        $userspar_id=[];
        //medecin
        foreach ($AllUsersJoinedEnseigne as $value) {
            if (in_array('medecin', $value->getProfiles()) and !in_array($value->getIdUser(), $users_id)) {
                if (key_exists($value->getJoinedAt()->format('M'), $medecinsJoinedParMois)) {
                    $medecinsJoinedParMois[$value->getJoinedAt()->format('M')] = $medecinsJoinedParMois[$value->getJoinedAt()->format('M')] + 1;
                } else {
                    $medecinsJoinedParMois[$value->getJoinedAt()->format('M')] = 1;
                }
                $users_id[]=$value->getIdUser();
                $medecinTotal++;
            }          
        }
        //patient
        foreach ($AllUsersJoinedEnseigne as $value) {
           if(in_array('patient', $value->getProfiles()) and !in_array($value->getIdUser(), $usersele_id)){
                if(key_exists($value->getJoinedAt()->format('M'), $patientsJoinedParMois))
                {
                    $patientsJoinedParMois[$value->getJoinedAt()->format('M')] = $patientsJoinedParMois[$value->getJoinedAt()->format('M')] + 1;
                } else {
                    $patientsJoinedParMois[$value->getJoinedAt()->format('M')] = 1;
                }
                $patientTotal++;
                $usersele_id[]=$value->getIdUser();
            }  
        }
        //parent
        foreach ($AllUsersJoinedEnseigne as $value) {
            if(in_array('parent', $value->getProfiles()) and !in_array($value->getIdUser(), $userspar_id)){
                if(key_exists($value->getJoinedAt()->format('M'), $parentsJoinedParMois))
                {
                    $parentsJoinedParMois[$value->getJoinedAt()->format('M')] = $parentsJoinedParMois[$value->getJoinedAt()->format('M')] + 1;
                } else {
                    $parentsJoinedParMois[$value->getJoinedAt()->format('M')] = 1;
                }
                $parentTotal++;
                $userspar_id[]=$value->getIdUser();
            }
        }
        
    	return $this->render('dashboard/index.html.twig', [
            'allEnseigneParMois' => $enseigneParMois,
            'medecinsJoinedParMois' => $medecinsJoinedParMois,
            'patientsJoinedParMois' => $patientsJoinedParMois,
            'parentsJoinedParMois' => $parentsJoinedParMois,
            'enseigneTotal' => $enseigneTotal,
            'medecinTotal' => $medecinTotal,
            'patientTotal' => $patientTotal,
            'parentTotal' => $parentTotal,
            "totalParticulier"=>$totalParticulier,
            "totalEnseigne"=>$totalEnseigne
        ]);
    } 

    /**
     * @Route("/dashboard/profile", name="my_profile", methods={"GET","POST"})
     */
    public function profile(Request $request, FlashyNotifier $flashy, 
        HttpClientInterface $client, UserPasswordEncoderInterface $userPwdEncoderInter)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $session = new Session();
        $submittedToken = $request->request->get('_token');
        if($submittedToken !== null)
        {
            if($this->isCsrfTokenValid('update-profile', $submittedToken))
            {
                $password = $request->request->get("mot_passe");
                $confpwd = $request->request->get("conf_mot_passe");
                $old_pwd = $request->request->get('hold_mot_passe');
                if($userPwdEncoderInter->isPasswordValid($user, $old_pwd) )
                {
                    if($password != $confpwd)
                    {
                        $flashy->error("Erreur champ mot de passe différent du champ de confirmation.");
                        return $this->redirectToRoute('my_profile');
                    }
                    else
                    {
                        $response = $client->request('POST', 
                            $this->getParameter('API_URL').$this->apiUpdatePassword, [
                            'body' => [
                                'old_passe' => $old_pwd,
                                'new_passe' => $password,
                                'id_user' => $user->getId()
                            ]
                        ]);
                
                        $content = $response->getContent();
                        $content_array = json_decode($content, true);
                        //throw new \Exception(var_dump($content_array), 1);
                        
                        if($content_array['server_response'][0]['status'] == 1)
                        {
                            $user->setPassword($userPwdEncoderInter->encodePassword($user, $password));
                            $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->flush();

                            $flashy->success('Mot de passe bien mis à jour');
                            return $this->redirectToRoute('dashboard');
                        }
                        else{
                            $flashy->error('Erreur, mot de passe ne peut être mis à jour');
                            return $this->redirectToRoute('my_profile');
                        }
                    }
                }
                else
                {
                    $flashy->error('Ancien mot de passe incorrecte!');
                    return $this->redirectToRoute('my_profile');
                }
            }
        }
        else
        {
            $session->set("mdp_hold", $user->getPassword());
        }

        return $this->render("security/profile.html.twig", [
            'user' => $user,
        ]);

    }
}
