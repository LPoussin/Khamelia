<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BalGroupeType;
use App\Entity\BalGroupe as Groupe;
use App\Entity\UserJoinedEnseigne;
use App\Entity\Bal;
use App\Form\BalType;
use App\Service\UploadFile;
use App\Entity\BalDestinataire;
use MercurySeries\FlashyBundle\FlashyNotifier;
use App\Entity\BalGroupe;
use Knp\Component\Pager\PaginatorInterface;

class BalController extends AbstractController
{
    /**
     * @Route("/bal/{page}/{limit}", name="bal",requirements={"page"="\d+","limit"="\d+"})
     */
    public function index(PaginatorInterface $paginator,$page=1,$limit=20): Response
    {
        $em = $this->getDoctrine()->getManager();
        if(is_null($this->getUser())){
            return $this->redirectToRoute('app_login');
        }
        if(is_null($user_enseigne= $em->getRepository(UserJoinedEnseigne::class)->findOneByUser($this->getUser()))){
            return $this->redirectToRoute('dashboard');
        }
        // toutes les discussions
        $all=$em->getRepository(BalDestinataire::class)->getBalParts($this->getUser());
        $discuss=[];
        $i=0;
        $ids=[];
        $idss=[];
        foreach ($all as $dest){ //$ind=''.$i++.'';        
          if($dest->getUser()){
            if($dest->getUser()->getId()==$this->getUser()->getId()){
                $id=$dest->getMessage()->getExpediteur()->getId();
                if(!in_array($id, $ids)){
                    $ids[]=$id;
                    $discuss[$id.'u']=["etat"=>$dest->getEtat(),"type"=>"user","lastMessage"=>$dest->getMessage(),'user'=>$dest->getMessage()->getExpediteur()];
                }else{
                    $discuss[$id.'u']['lastMessage']=$dest->getMessage();
                    $discuss[$id.'u']['etat']=$dest->getEtat();
                }
            }else{
                $id=$dest->getUser()->getId();
                if(!in_array($id, $ids)){
                    $ids[]=$id;
                    $discuss[$id.'u']=["etat"=>$dest->getEtat(),"type"=>"user","lastMessage"=>$dest->getMessage(),'user'=>$dest->getUser()];
                }else{
                    $discuss[$id.'u']['lastMessage']=$dest->getMessage();
                    $discuss[$id.'u']['etat']=$dest->getEtat();
                }
            }
          }
          else{
            $id=$dest->getGroupe()->getId();
            if(!in_array($id, $idss)){
                $idss[]=$id;
                $discuss[$id.'g']=["etat"=>$dest->getEtat(),"type"=>"groupe","lastMessage"=>$dest->getMessage(),"groupe"=>$dest->getGroupe()];
            }else{
                //dd($discuss,$discuss[$id],$id,$discuss[$id]["lastMessage"]);
               $discuss[$id.'g']["lastMessage"]=$dest->getMessage();
               $discuss[$id.'g']['etat']=$dest->getEtat();
            }
          }
        }
        //dd($discuss);
        return $this->render("bal/mail_folder/index.html.twig", [
            "discuss"=> $paginator->paginate($discuss,$page,$limit),
        ]);
    }

    
    /**
     * @Route("/bal/discussion-{type}/{id}/{page}/{limit}", name="bal_discussion",requirements={"page"="\d+","limit"="\d+","id"="\d+","type"="user|groupe"})
     */
    public function discussion(Request $request,UploadFile $uploadFile,$id,$type,PaginatorInterface $paginator,$page=1,$limit=10): Response
    {//id de user
        $em = $this->getDoctrine()->getManager();
        $us="";$gs="";
        if(is_null($this->getUser())){
            return $this->redirectToRoute('app_login');
        }
        if(is_null($user_enseigne= $em->getRepository(UserJoinedEnseigne::class)->findOneByUser($this->getUser()))){
            return $this->redirectToRoute('dashboard');
        }
        //voir la possibilite d'envoi de message
        if($type=="user"){
            if(is_null($us=$em->getRepository(UserJoinedEnseigne::class)->findOneByUser($id))){
                return $this->redirectToRoute('dashboard');
            }
            $mess=$em->getRepository(BalDestinataire::class)->getMessageUser($paginator,$page,$limit,$this->getUser(),$us->getUser());
            $de=count($mess); 
            if($de){
                $mess[0]->setEtat(true);
                $em->flush();
            }   
        }else{
           if(is_null($gs=$em->getRepository(BalGroupe::class)->findOneById($id))){
                return $this->redirectToRoute('dashboard');
            }
            if(!$gs->getMembers()->contains($this->getUser())){
                return $this->redirectToRoute('dashboard'); 
            }
            $mess=$em->getRepository(BalDestinataire::class)->getMessageGroupe($paginator,$page,$limit,$gs); 
            $de=count($mess); 
            if($de){
                $mess[0]->setEtat(true);
                $em->flush();
            }       
        }
        // form
        $bal = new Bal();
        $form = $this->createForm(BalType::class,$bal,['user'=>$user_enseigne]);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $bal=$form->getData();
                $sx=new BalDestinataire();
               if($type=="user"){
                $sx->setEtat(false)->setUser($us->getUser())->setMessage($bal);
                $bal->addDest($sx);
               }else{
                   $sx->setEtat(false)->setGroupe($gs)->setMessage($bal);
                   $bal->addDest($sx);
               }

            $bal->setExpediteur($this->getUser())
                    ->setDate(new \DateTime())->setSubject(' ')
                 ;
            $filename="";
            if($bal->getF()){
               $filename=$uploadFile->upload($bal->getF(), $this->getParameter('FILE_CHAT_DIR')); 
               $array = explode('.', $filename);
               $end=end($array);
               if($end=="pdf"){
                 $bal->setFiletype('pdf');
               }else{
                $bal->setFiletype('img');
               }
            }
            $bal->setFile($filename);
            $em->persist($bal);
            $em->flush();
            return $this->redirectToRoute('bal_discussion',['type'=>$type,'id'=>$id]);
        }
        //dd($mess);
        return $this->render("bal/detail/index.html.twig", [
            'dests'=>$mess,
            "type"=>$type,
            "user"=>$us,
            "groupe"=>$gs,
            'form'=>$form->createView()
        ]);
    }
    
    /**
     * @Route("/bal/new", name="bal_new")
     */
    public function new(Request $request,UploadFile $uploadFile,FlashyNotifier $flash): Response
    {
        $em = $this->getDoctrine()->getManager();
        if(is_null($this->getUser())){
            return $this->redirectToRoute('app_login');
        }
        if(is_null($user_enseigne= $em->getRepository(UserJoinedEnseigne::class)->findOneByUser($this->getUser()))){
            return $this->redirectToRoute('dashboard');
        }
        $bal = new Bal();
        $form = $this->createForm(BalType::class,$bal,['user'=>$user_enseigne]);
        $form->handleRequest($request);
        
        if($form->isSubmitted() and $form->isValid()){
            $bal=$form->getData();
            $dests=$bal->getDestinataires();
            $destgs=$bal->getDestinatairegroupes();
            foreach ($dests as $ff){
                $sx=new BalDestinataire();
                $sx->setEtat(false)->setUser($ff)->setMessage($bal);
                $bal->addDest($sx);
            }
            foreach ($destgs as $ff){
                $sx=new BalDestinataire();$sx->setEtat(false)->setGroupe($ff)->setMessage($bal);
                $bal->addDest($sx);
            }
            if($bal->getDestinataires()->isEmpty() and $bal->getDestinatairegroupes()->isEmpty()){
                return $this->render("bal/new.html.twig", [
                    'form'=>$form->createView(),
                    'error'=>"Veuillez choisir au moins un destinataire"
                ]);
            }
            $bal->setExpediteur($this->getUser())
                    ->setDate(new \DateTime())
                 ;
            $filename="";$ext="";
            if($bal->getF()){
               $filename=$uploadFile->upload($bal->getF(), $this->getParameter('FILE_CHAT_DIR')); 
               $array = explode('.', $filename);
               $end=end($array);
               if($end=="pdf"){
                 $ext='pdf';
               }else{
                $ext='img';
               }
            }
            $bal->setFile($filename);
            $bal->setFiletype($ext);
            $em->persist($bal);
            $em->flush();
            $flash->success("Message envoyé avec succes");
            if($request->isXmlHttpRequest()){
                return new JsonResponse(['status'=>"success"]);
            }
            return $this->redirectToRoute("bal");
        }
        return $this->render("bal/new.html.twig", [
            'form'=>$form->createView(),
            "error"=>''
        ]);
    }
     /**
     * @Route("/bal/groupe", name="bal_groupe")
     */
    public function groupe(): Response
    {
        $em = $this->getDoctrine()->getManager();
        if(is_null($this->getUser())){
            return $this->redirectToRoute('app_login');
        }
        if(is_null($user_enseigne= $em->getRepository(UserJoinedEnseigne::class)->findOneByUser($this->getUser()))){
            return $this->redirectToRoute('dashboard');
        }
        $gs=$em->getRepository(Groupe::class)->getAllGroupeWithMembers();
        $groupes=[];
        $mid=$this->getUser()->getId();
        foreach ($gs as $g){
            $mems=$g->getMembers();
            foreach ($mems as $m){
                if($m->getId()==$mid){
                   $groupes[]=$g; 
                }
            }
        }
        return $this->render("bal/groupes.html.twig", [
            'groupes' => $groupes,
        ]);
    }
    
    
    /**
     * @Route("/bal/new-groupe", name="bal_new_groupe")
     */
    public function createGroupe(Request $request){
        $em = $this->getDoctrine()->getManager();
        if(is_null($this->getUser())){
            return $this->redirectToRoute('app_login');
        }
        if(is_null($user_enseigne= $em->getRepository(UserJoinedEnseigne::class)->findOneByUser($this->getUser()))){
            return $this->redirectToRoute('dashboard');
        }
       
        $groupe = new Groupe();
        $form = $this->createForm(BalGroupeType::class,$groupe,['user'=> $user_enseigne]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $groupe=$form->getData();
            $groupe->addMember($this->getUser());
            $em->persist($groupe);
            $em->flush();
            return $this->redirectToRoute('bal');
        }
        
        return $this->render('bal/newgroupe.html.twig',['form'=>$form->createView()]);
    }
    
    /**
     * @Route("/bal/groupe/{id}", name="bal_groupe_detail",requirements={"id"="\d+"})
     */
    public function groupeDetail($id){
        $em = $this->getDoctrine()->getManager();
        if(is_null($this->getUser())){
            return $this->redirectToRoute('app_login');
        }
        if(is_null($user_enseigne= $em->getRepository(UserJoinedEnseigne::class)->findOneByUser($this->getUser()))){
            return $this->redirectToRoute('dashboard');
        }
       
        if(is_null($gs=$em->getRepository(Groupe::class)->findOneById($id))){
             return $this->redirectToRoute('dashboard');
        }
        $c=false;
        $mid=$this->getUser()->getId();
        $mems=$gs->getMembers();
        foreach ($mems as $m){
            if($m->getId()==$mid){
               $c=true;
               goto fin;
            }
        }
        fin :
        if(!$c){
           return $this->redirectToRoute('dashboard'); 
        }
        return $this->render('bal/groupe_detail.html.twig',['groupe'=>$gs]);
    }

    /**
     * @Route("/bal/unred/number", name="bal_unred_number")
     */
    public function unredDiscussionNumber(){
        $em = $this->getDoctrine()->getManager();
        if(is_null($this->getUser())){
            return $this->redirectToRoute('app_login');
        }
        $all=$em->getRepository(BalDestinataire::class)->findBy(['user'=>$this->getUser(),'etat'=>false]);
        $ids=[];
        $i=0;
        foreach ($all as $dest) {
            $id=$dest->getMessage()->getExpediteur()->getId();
            if(!in_array($id, $ids)){
              $ids[]=$id;
              $i++;
            }
        }
        return $i == 0 ? new Response('') : new Response($i);
    }
	
	
    /**
     * @Route("/bal/unred/{page}{limit}", name="bal_unred",requirements={"page"="\d+","limit"="\d+"})
     */
    public function unredDiscussion(PaginatorInterface $paginator){
        $em = $this->getDoctrine()->getManager();
        if(is_null($this->getUser())){
            return $this->redirectToRoute('app_login');
        }
        $all=$em->getRepository(BalDestinataire::class)->findBy(['user'=>$this->getUser(),'etat'=>false]);
        $ids=[];
        $messages=[];
        //$i=0;
        foreach ($all as $dest) {
            $id=$dest->getMessage()->getExpediteur()->getId();
            if(!in_array($id, $ids)){
              $ids[]=$id;
              $messages[]=$dest->getMessage();
              //$i++;
            }
        }
        return $this->render('notification/message_notif_liste.html.twig',[
            "messages"=>$messages
        ]);
    }
}
