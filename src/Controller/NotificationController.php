<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\NotificationRepository;


/**
 * @Route("/notification")
 */
class NotificationController extends AbstractController
{
    /**
     * @Route("", name="notification")
     */
    public function index(NotificationRepository $nRepo): Response
    {
    	if(is_null($this->getUser())){
            return $this->redirectToRoute('app_login');
        }
        $ns=$nRepo->findBy(['destinataire'=>$this->getUser()]);
		foreach ($ns as $n) {
          if(!$n->getVu()){
          	$n->setVu(true);
          }
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->render('notification/index.html.twig', [
            'notifications' => $ns,
            'position' => 'Notification',
            'chemin' => 'Notification ',
        ]);
    }
    /**
     * @Route("/liste/{type}/{page}/{limit}", name="notification_liste",requirements={"type"="all|vu|nonvu"})
     */
    public function listeNotification(PaginatorInterface $paginator,NotificationRepository $nRepo,$type,$page=1,$limit=20){
       $em = $this->getDoctrine()->getManager();
        if(is_null($this->getUser())){
            return $this->redirectToRoute('app_login');
        }
        if($type=="nonvu"){
        	$ns=$nRepo->findBy(['destinataire'=>$this->getUser(),"vu"=>false]);
        }elseif($type=="vu"){
        	$ns=$nRepo->findBy(['destinataire'=>$this->getUser(),"vu"=>true]);
        }else{
        	$ns=$nRepo->findBy(['destinataire'=>$this->getUser()]);
        }
        
        
        return $this->render('notification/liste.html.twig',[
            "notifications"=>$paginator->paginate($ns,$page,$limit)
        ]);
    }
    
    /**
     * @Route("/non-vu/number", name="notification_non_vu_num")
     */
    public function nonVuNum(NotificationRepository $nRepo){
       if(is_null($this->getUser())){
            return new Response('');
        }
        $ns=$nRepo->findBy(['destinataire'=>$this->getUser(),'vu'=>false]); 
        $n=count($ns);
        return $n == 0 ? new Response('') : new Response($n);
    }
}
