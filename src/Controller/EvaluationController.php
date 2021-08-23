<?php

namespace App\Controller;

use App\Entity\Classes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MercurySeries\FlashyBundle\FlashyNotifier;
use App\Repository\TypeEvalRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Evaluation;
use App\Repository\ClassesRepository;
use App\Repository\MatieresRepository;
use App\Repository\EnseigneAffilieeRepository;
use App\Repository\EvaluationRepository;
use App\Repository\UserJoinedEnseigneRepository;
use App\Entity\EvaluationNote;
use App\Repository\EvaluationNoteRepository;
use App\Entity\User;

class EvaluationController extends AbstractController
{
    /**
     * @Route("/evaluation/{id_enseigne}", name="evaluation",requirements={"id_enseigne"})
     */
    public function index(FlashyNotifier $flashy,EnseigneAffilieeRepository $ensAff,$id_enseigne): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        //--ril
        if(is_null($ens=$ensAff->findOneById($id_enseigne))){
           $flashy->error("Enseigne inconnu");
            return $this->redirectToRoute('app_login'); 
        }
        $ids=[];
        $cs=[];
        $classes=$ens->getClasses();
        foreach ($classes as $classe) {
            $profs=$classe->getProfesseurs();
            foreach ($profs as $prof) {
                if($user->getId()==$prof->getId() and !in_array($prof->getId(), $ids)){
                    $cs[]=$classe;
                    $ids[]=$prof->getId();
                }
            }
        }
        $meclasses=$user->getClasses();//prof principal
        foreach ($meclasses as $c) {
            if(!in_array($c->getId(), $ids)){
                $cs[]=$c;
                $ids[]=$c->getId();
            }
        }
        return $this->render('evaluation/index.html.twig', [
            'controller_name' => 'EvaluationController',
            'position' => 'Evaluation',
            'chemin' => 'Evaluation ',
            'classes' => $cs
        ]);
    }

    /**
     * @Route("/evaluation/new/{classe}", name="evaluation_new",requirements={"classe"="\d+"})
     */
    public function new(Classes $classe, FlashyNotifier $flashy, TypeEvalRepository $typeEvalRepository, 
        Request $request, ClassesRepository $classeRepository, MatieresRepository $matieresRepository) : Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }

        $matieres = $user->getProfesseurMatiere()->getMatieres();

        $submittedToken = $request->request->get('__token');
        if ($submittedToken != null) 
        {
            if($this->isCsrfTokenValid("nouvelle-evaluation", $submittedToken))
            {
                $matiereId = $request->request->get('matiere');
                $typeEvalId = $request->request->get('typeEval');
                $classeId = $request->request->get('classe');
                $dateCompo = $request->request->get('date_compo');
                $libelle = $request->request->get('libelle');
                $description = $request->request->get('description');
                $isActif = $request->request->get('is_actif');

                $evaluation = new Evaluation();
                $evaluation->setClasse($classeRepository->findOneBy(['id' => $classeId]))
                           ->setProf($user)
                           ->setMatiere($matieresRepository->findOneBy(['id' => $matiereId]))
                           ->setTypeEval($typeEvalRepository->findOneBy(['id' => $typeEvalId]))
                           ->setDateCompo(new \DateTime($dateCompo))
                           ->setLibelle($libelle)
                           ->setDescription($description)
                           ->setIsActif($isActif);
                
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($evaluation);
                $entityManager->flush();

                $flashy->success('Succes');
                return $this->redirectToRoute('evaluation',['id_enseigne'=>$classe->getEnseigne()->getId()]);
            }
            else
            {
                $flashy->error("Formulaire invalide");
                return $this->redirectToRoute('enseigne');
            }
        }
        

        return $this->render('evaluation/new.html.twig', [
            'position' => 'Evaluation',
            'chemin' => 'Evaluation ',
            'classe' => $classe,
            'matieres' => $matieres,
            'typeEvals' => $typeEvalRepository->findAll()
        ]);
    }
    
    /**
     * @Route("/evaluation/timeline/{id_enseigne}", name="evaluation_timeline",requirements={"id_enseigne"="\d+"})
     */
    public function listeEvaluation(FlashyNotifier $flashy, EvaluationRepository $evRepo,UserJoinedEnseigneRepository $userJoinRepo,$id_enseigne){
        $user = $this->getUser();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }
        $ud=$userJoinRepo->findOneBy(["id_enseigne"=>$id_enseigne,"user"=>$user]);
        if (!$ud) {
            $flashy->error("Une erreur");
            return $this->redirectToRoute('app_login');
        }
        //toutes mes evaluations
        $evaluations=$evRepo->findByProf($user);
        $evTab=[];
        foreach ($evaluations as $ev){
            if($ev->getClasse()->getIdEnseigne()==$id_enseigne){
                $evTab[]=$ev;
            }
        }
        
        return $this->render('evaluation/evaluation_timeline.html.twig',[
            'evaluations'=>$evTab,
            'position' => 'Evaluation timeline',
            'chemin' => 'Evaluation ',
            'UserDetails'=>$ud,
			"id_enseigne"=>$id_enseigne
        ]);
    }
    /**
     * @Route("/evaluation/note-students/{classe_id}/{evaluation_id}/", name="evaluation_note_student",requirements={"classe_id"="\d+","evaluation_id"="\d+"},methods={"GET", "POST"})
     */
    public function noterEleve($classe_id,$evaluation_id,Request $request,FlashyNotifier $flashy, EvaluationNoteRepository $evNoteRepo){
        
        $user = $this->getUser();
        $em= $this->getDoctrine()->getManager();
        if (!$user) {
            $flashy->error("Veuillez-vous connecter");
            return $this->redirectToRoute('app_login');
        }
		$classe=$em->getRepository(Classes::class)->findOneById($classe_id);
        if(is_null($classe)){
            return $this->createNotFoundException("Classe non trouvée"); 
        }
        //Les eleves de la classe;
        $eleves=[];
        $ins = $classe->getInscriptions();
        foreach ($ins as $i){
           $eleves[]=$i->getIdEleve(); 
        }
        $evaluation=$em->getRepository(Evaluation::class)->findOneBy(["prof"=>$user,"id"=>$evaluation_id]);
        if(is_null($evaluation)){
            return $this->createNotFoundException("Evaluation non trouvée"); 
        }
        
        $submittedToken = $request->request->get('__token');
		//dd($request->getData());
        if ($submittedToken != null) {
            if($this->isCsrfTokenValid("evaluation-noter", $submittedToken)){
                $eleve_id = $request->request->get('eleve');
                $note = $request->request->get('note');
                if(is_null($eleve=$em->getRepository(User::class)->findOneById($eleve_id))){
                    return $this->createNotFoundException("Eleve non trouvé");  
                }
                
                if(!is_null($note_ancien=$evNoteRepo->findOneBy(['evaluation'=>$evaluation,"eleve"=>$eleve_id]))){
                   $note_ancien->setNote($note);
                   $em->flush();
                   $flashy->success("Modifier avec succes");
                }else{
                    $n=new EvaluationNote();
                    
                    $n->setEleve($eleve)->setEvaluation($evaluation)->setNote($note);
                    $em->persist($n);
                    $em->flush();
                    $flashy->success("Assigner avec succes");
                }
            }
        }
        $elevesNotes=[];
        foreach ($eleves as $el){
            $elevesNotes[]=["user"=>$el,"note"=>$evNoteRepo->findOneBy(['eleve'=>$el,"evaluation"=>$evaluation])];
        }
            return $this->render("evaluation/noter_eleve.html.twig",[
                "classe"=>$classe,
                "evaluation"=>$evaluation,
                "elevesNotes"=>$elevesNotes,
                 'position' => 'Evaluation Noter Eleves',
                 'chemin' => 'Evaluation',
            ]);
    }
}
