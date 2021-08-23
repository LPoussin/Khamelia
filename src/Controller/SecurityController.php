<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\TypeEnsRepository;
use App\Repository\EnseigneTypeEnsRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


class SecurityController extends AbstractController
{
    private $apiAllEnseigneAffilierUrl = 'all_enseigne_affilier.php';

    /**
     * @Route("/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, TypeEnsRepository $typeEnsRepo, EnseigneTypeEnsRepository $enseigneTypeEnsRepo, HttpClientInterface $client): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        //liste de tous les type d'enseigne
        $allTypeEns = $typeEnsRepo->findBy(['active' => true]);

        //liste des enseignes appartenant à un type
        $AllEnseigneTypeEns = $enseigneTypeEnsRepo->findAll();

        //Toutes les enseignes affilié a inetSchool
        $responseEnseigneAffilier = $client->request('POST', $this->getParameter('API_URL').$this->apiAllEnseigneAffilierUrl, [
                'query' => [
                    'id_communaute' => $this->getParameter('ID_COMMUNAUTE')
                ]
            ]);

            $content = $responseEnseigneAffilier->getContent();
            $content_array = json_decode($content, true);
		//dd($content,$content_array);
            if( isset($content_array['server_responses'][0]) )
            {
                $AllEnseigneAffiliees = $content_array['server_responses'][0]['founded'] == 0 ? [] :            $content_array['server_responses'];
            }
            else{
                $AllEnseigneAffiliees = [];
            }
            

            //$AllEnseigneAffiliees= array_key_exists("0", $content_array['server_responses'])  && $content_array['server_responses'][0]['founded'] === 0 ? [] : $content_array['server_responses'];



        //traitement pour avoir un array contenant la liste des enseignes lié au type
        $typesEns = [];
        $i = 0;
        foreach ($allTypeEns as $UntypeEns) 
        {
            $typesEns[$i] = [];
            foreach ($AllEnseigneTypeEns as $enseigneTypeEns) 
            {
                if ($UntypeEns->getId() == $enseigneTypeEns->getIdTypeEns()) {
                    
                    foreach ($AllEnseigneAffiliees as $enseigneAffiliee) {
                        if ($enseigneAffiliee['id_enseigne'] == $enseigneTypeEns->getIdEnseigne()) {
                            array_push($typesEns[$i], ([
                                'unTypeEns' => $UntypeEns, 'enseigneAffiliee' => $enseigneAffiliee
                            ]));
                            
                        }
                    }
                    
                }
                
            }
            $i++;
        }


        

        return $this->render('security/login.html.twig', 
            [
                'last_username' => $lastUsername, 
                'error' => $error,
                'allTypeEns' => $allTypeEns,
                'resultats' => $typesEns
            ]
        );
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
