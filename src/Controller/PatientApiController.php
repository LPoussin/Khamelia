<?php

namespace App\Controller;

use App\Entity\PatientApi;
use App\Form\PatientApiType;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/dashboard/enseigne/patient")
 */
class PatientApiController extends AbstractController
{
    
    /**
     * @Route("/{id_enseigne}/new", name="patient_new", methods={"GET","POST"})
     */
    /*public function new($id_enseigne, Request $request, FlashyNotifier $flashy, Swift_Mailer $mailer, UserPasswordEncoderInterface $encoder): Response
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
            $entityManager->persist($patientApi);
            $entityManager->flush();


            $message = (new Swift_Message('Nouveau compte'))
                ->setFrom('test.inetschools@gmail.com')
                ->setTo($patient->getEmail())
                ->setBody(
                    $this->renderView(
                        'email/patient.html.twig',
                        compact('patient')
                    ),
                    'text/html'
                )
            ;

            $re = $mailer->send($message);
            //dd($re);
            $flashy->success('Patient créé avec success');
           // return $this->forward('App\Controller\EnseigneController::membres_inscription', ['id_enseigne' => $id_enseigne]);
        }

        return $this->render('patient_api/index.html.twig', [
            'medecin' => $patientApi,
            'form' => $form->createView(),
            'position' => 'Nouveau Médecin',
            'chemin' => 'Médecin / Nouveau Médecin',
        ]);
    }*/
}
