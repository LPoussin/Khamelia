<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Cours;
use App\Repository\AvisRepository;
use App\Repository\InscriptionsRepository;
use App\Repository\UserJoinedEnseigneRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class AvisController extends AbstractController
{
    /**
     * @Route("/avis/{cours}", name="avis")
     */
    public function index(Request $request, FlashyNotifier $flashy, AvisRepository $avisRepository, Cours $cours, InscriptionsRepository $inscriptionsRepository, UserJoinedEnseigneRepository $userJoinedEnseigneRepository): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }

        $allAvis = $avisRepository->findBy(['cours' => $cours]);

       
        //profile de l'user
        $joinedUserProfile = $userJoinedEnseigneRepository->findOneBy(['id_enseigne' => $cours->getIdEnseigne(), 'id_user' => $user->getId()]);
        $content = [];
        $userAvis = false;
        
        if (in_array('parent', $joinedUserProfile->getProfiles())) {
            foreach ($allAvis as $value) {
                $content[$value->getId()] = $inscriptionsRepository->findByParent(['id_eleve' => $value->getEleve(), 'idparent' => $user->getId()]);
            }
        }
        elseif (in_array('eleve', $joinedUserProfile->getProfiles())) {
            //a-t-il deja donne son avis l'user connecté?
            foreach ($user->getAvis() as $value) {
                if ($value->getCours() == $cours) {
                    $userAvis = true;
                }
            }

        }
        

        return $this->render('avis/index.html.twig', [
            'position' => 'Avis',
            'chemin' => 'Avis',
            'allAvis' => $allAvis,
            'content' => $content,
            'joinedUserProfile' => $joinedUserProfile,
            'userAvis' => $userAvis,
            'cours' => $cours
            
        ]);
    }

     /**
     * @Route("/avis_new/{cours}", name="avis_cours")
     */
    public function avis_new(Request $request, FlashyNotifier $flashy, AvisRepository $avisRepository, Cours $cours): Response
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
            if($this->isCsrfTokenValid('new-avis', $submittedToken))
            {

                $cours_compris = $request->request->get('cours_compris');
                $cours_expliquer = $request->request->get('bien_explique');
                $eleve = $user;

                $avis = new Avis();
                $avis->setCoursCompris($cours_compris)
                    ->setBienExplique($cours_expliquer)
                    ->setCours($cours)
                    ->setEleve($user);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($avis);
                $entityManager->flush();

                $flashy->success('Avis envoyé');
                return $this->redirectToRoute('enseigne');

            }
            else
            {
                $flashy->error('Formualire invalide');
                return $this->redirectToRoute('enseigne');
            }
        }
       
        return $this->render('avis/avis.html.twig', [
            'position' => 'Avis',
            'chemin' => 'Avis',
        ]);
    }

    /**
     * @Route("/avis/validation/{avis}", name="valider_avis")
     */
    public function validateAvis(Request $request, Avis $avis, FlashyNotifier $flashy)
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }

        $avis->setIsValide(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        
        $flashy->success("Avis validé aves sucsès");
        return $this->redirectToRoute('enseigne');

    }
}
