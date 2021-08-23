<?php

namespace App\Controller;

use App\Entity\Services;
use App\Form\ServicesType;
use App\Form\ServiceUpdateType;
use App\Repository\ServicesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\File\File;
use App\Repository\UserJoinedEnseigneRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * @Route("/services")
 */
class ServicesController extends AbstractController
{
    /**
     * @Route("/", name="services_index", methods={"GET"})
     */
    public function index(ServicesRepository $servicesRepository, UserJoinedEnseigneRepository $userJoinedRepo, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
       /* if(!in_array('SUPER_ADMIN', $user->getRoles()))
        {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_logout');
        }*/
        $joinedEnseigne = $userJoinedRepo->findBy(['id_user' => $user->getId()]);



        $position = "Services";
        $chemin = "Services";
        return $this->render('services/index.html.twig', [
            'services' => $servicesRepository->findAll(),
            'position' => $position,
            'chemin' => $chemin,
            'joinedEnseigne' => !$joinedEnseigne ? false : true
        ]);
    }

    /**
     * @Route("/new", name="services_new", methods={"GET","POST"})
     */
    public function new(Request $request,  FlashyNotifier $flashy, UserRepository $userRepo, UserJoinedEnseigneRepository $userJoinedRepo): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if(!in_array('SUPER_ADMIN', $user->getRoles()))
        {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_logout');
        }

        $service = new Services();
        $form = $this->createForm(ServicesType::class, $service);
        $form->handleRequest($request);

        $position = "Nouveau service";
        $chemin = "Services / Nouveau";

        if ($form->isSubmitted() && $form->isValid()) {
            $logo = $form->get('logo')->getData();
            if($logo != null)
            {
                $originalName = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $originalName;
                $newFileName = $safeFileName.'-'.uniqid().'.'.$logo->guessExtension();

                try {
                    
                    $logo->move($this->getParameter('LOGO_SERVICE_PATH'), $newFileName);

                } catch (FileException $e) {
                    throw new \Exception($e->getMessage(), 1);
                    
                }

                $service->setLogo($newFileName);
            }
            else
                $service->setLogo("");
            $joinedEnseigne = $userJoinedRepo->findAll();


            if ($service->getType()) {
                $allUsers = $userRepo->findAll();
                foreach ($allUsers as $user) {
                    if($joinedEnseigne)
                    {
                        $user->addService($service);
                    }
                }
            }


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($service);
            $entityManager->flush();


            if ($service->getType() == true) {

                $allUsers = $userRepo->findAll();

                foreach ($allUsers as $user) {
                    foreach ($joinedEnseigne as $userAyantJoin) {
                        if($userAyantJoin->getIdUser() == $user->getId())
                        {
                            $user->addService($service);
                          
                            $entityManager->flush();
                        }
                    }
                }
            }

            $flashy->success('Service créé avec success');
            return $this->redirectToRoute('services_index');
        }

        return $this->render('services/new.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
            'position' => $position,
            'chemin' => $chemin
        ]);
    }

    /**
     * @Route("/{id}", name="services_active", methods={"GET"})
     */
    public function active(Services $service, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            return $this->redirectToRoute('app_login');
        }

        $service->setEtat(!$service->getEtat());
        $this->getDoctrine()->getManager()->flush();
        $flashy->success('Etat modifié avec succès');

        return $this->redirectToRoute('services_index');
    }

    /**
     * @Route("/{id}/edit", name="services_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Services $service, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            return $this->redirectToRoute('app_login');
        }  
        $form = $this->createForm(ServiceUpdateType::class, $service);
      
        $form->handleRequest($request);
        $session = new Session();
        if(!$form->isSubmitted())
        {
            
            $session->set("logo", $service->getLogo());
        }
        
        $position = "Edition d'un service";
        $chemin = "Services / Editer";
        if ($form->isSubmitted() && $form->isValid()) {

            $logo = $form->get('logo')->getData();
            if($logo != "" && $logo != null && $logo != "vide")
            {
                $originalName = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $originalName;
                $newFileName = $safeFileName.'-'.uniqid().'.'.$logo->guessExtension();

                try {
                    
                    $logo->move($this->getParameter('LOGO_SERVICE_PATH'), $newFileName);

                } catch (FileException $e) {
                    throw new \Exception($e->getMessage(), 1);
                    
                }

                $service->setLogo($newFileName);
            }
            else
                $service->setLogo($session->get("logo"));

            $this->getDoctrine()->getManager()->flush();

           
            $flashy->success('Mise à jour bien effectuée');

            return $this->redirectToRoute('services_index');
        }

        return $this->render('services/edit.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
            'position' => $position,
            'chemin' => $chemin
        ]);
    }

    /**
     * @Route("/{id}/joinService", name="service_join", methods={"GET"})
     */
    public function join(Request $request, Services $service, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            return $this->redirectToRoute('app_login');
        }

        $user->addService($service);
        $this->getDoctrine()->getManager()->flush();

        $flashy->success("Service join avec success");

        return $this->redirectToRoute('services_index');
    } 
    /**
     * @Route("/{id}", name="services_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Services $service): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($service);
            $entityManager->flush();
        }

        return $this->redirectToRoute('services_index');
    }
}
