<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use App\Repository\UserJoinedEnseigneRepository;
use App\Repository\UserRepository;
use App\Repository\MatieresRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\ProfesseurMatiere;
use App\Repository\ProfesseurMatiereRepository;

class ProfesseurController extends AbstractController
{
    /**
     * @Route("/professeur/{id_enseigne}", name="professeur", methods={"GET", "POST"})
     */
    public function index(Request $request, UserJoinedEnseigneRepository $userJoinedEnseigneRepo, 
        FlashyNotifier $flashy, UserRepository $userRepo, MatieresRepository $matieresRepo, ProfesseurMatiereRepository $professeurMatRepo,
        $id_enseigne): Response
    {
        $user = $this->getUser();
        if(!$user)
        {
            $flashy->error('Veuillez vous connecter!');
            return $this->redirectToRoute('app_login');
        }

        //details des enseignants concernant les matières
        $allProfesseurMatieres = $professeurMatRepo->findAll();
        $allMatiereParEnseignants = [];

        //Les enseignants 
        $allEnseignantsId = $userJoinedEnseigneRepo->findByProfilesField("enseignant");
        $allEnseignants = [];
        foreach ($allEnseignantsId as $enseigne) {
            $allEnseignants[] = $userRepo->findOneBy(['id' => $enseigne->getIdUser()]);
            
            foreach ($allProfesseurMatieres as $value) {
                if ($value->getIdProf()->getId() == $enseigne->getIdUser() and $value->getIdEnseigne() == $id_enseigne) {
                    $allMatiereParEnseignants[$enseigne->getIdUser()] = $value->getMatieres();
                }
            }
        }

        //Les matières
        $allMatieres = $matieresRepo->findAll();

        //sauvegarde de l'id enseigne dans la session
        $session = new Session();
        $session->set('id_enseigne', $id_enseigne);

        $submittedToken = $request->request->get('_token');
        $id_enseigne = $session->get('id_enseigne');
        
       
        foreach ($allProfesseurMatieres as $value) {
            
        }

        if ($this->isCsrfTokenValid('associationMatiere', $submittedToken)) {
            $entityManager = $this->getDoctrine()->getManager();
            $id_enseignant = $request->request->get('id_enseignant');
            $matieres = $request->request->get('matieres');

            $professeurMatiere = $professeurMatRepo->findOneBy(['id_prof' => $id_enseignant, 'id_enseigne' => $id_enseigne]);
           //dd($professeurMatiere,$id_enseignant,$id_enseigne);
            if (!$professeurMatiere) {
                $profMat = new ProfesseurMatiere();
                $profMat->setIdProf($userRepo->findOneBy(['id' => $id_enseignant]) );
                $profMat->setIdEnseigne($id_enseigne);
                foreach ($matieres as $value) {
                    $profMat->addMatiere($matieresRepo->findOneBy(['id' => $value]));
                }
                
                $entityManager->persist($profMat);
                $entityManager->flush();
            } else {
                //dd($professeurMatiere);
                foreach ($allMatieres as $value) {
                    if(!in_array($value->getId(), $matieres))
                        $professeurMatiere->removeMatiere($value);
                    else
                        $professeurMatiere->addMatiere($value);
                }
                $entityManager->flush();
            }
            $flashy->success("Association bien effectuée.");
            return $this->redirectToRoute('professeur', ['id_enseigne' => $id_enseigne]);
            
        } 

        return $this->render('professeur/professeurs_index.html.twig', [
            'position' => 'Médécin / Specialité',
            'chemin' => 'Enseigne / Médécin / Specialité',
            'allEnseignants' => $allEnseignants,
            'allMatieres' => $allMatieres,
            'id_enseigne' => $id_enseigne,
            'allMatiereParEnseignants' => $allMatiereParEnseignants
        ]);
    }

    
}
