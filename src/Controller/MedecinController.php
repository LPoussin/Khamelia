<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Form\MedecinType;
use App\Repository\MedecinRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/medecin")
 */
class MedecinController extends AbstractController
{
    private $apiAllClientUrl = 'all_client.php';
    private $apiLiaisonMedecinParticulierUrl = 'liaison_medecin_particulier.php';
    private $apiLiaisonMedecinVerifyUrl = 'liaison_medecin_verify.php';
    private $apiLiaisonCouperUrl = 'liaison_couper.php';
    /**
     * @Route("/", name="medecins_index", methods={"GET"})
     */
    public function index(MedecinRepository $medecinsRepository, FlashyNotifier $flashy, HttpClientInterface $client): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('SUPER_ADMIN', $user->getRoles())) {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_login');
        }

        $all_medecin = $medecinsRepository->findAll();

        foreach ($all_medecin as $medeccin) {
            $responseMyLiaison = $client->request(
                'GET',
                $this->getParameter('API_URL') . $this->apiLiaisonMedecinVerifyUrl,
                [
                    'query' => [
                        'id_medecin' => $medeccin->getId(),
                    ]
                ]
            );

            $content = $responseMyLiaison->getContent();
            $content_array = json_decode($content, true);

            //dd($content_array);
            if ($content_array['server_responses'][0]['founded'] == 1) {
                //$flashy->success("Opération réussie");
                //return $this->redirectToRoute('enseigne');
                $medeccin->setLiaison(1);
            } else {
                $medeccin->setLiaison(0);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($medeccin);
            $entityManager->flush();
        }
        $all_medecin = $medecinsRepository->findAll();

        return $this->render('medecin/index.html.twig', [
            'medecins' => $all_medecin,
            'position' => 'Médecin',
            'chemin' => 'Médecin',
        ]);
    }


    /**
     * @Route("/new", name="medecin_new", methods={"GET","POST"})
     */
    public function new(Request $request, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('SUPER_ADMIN', $user->getRoles())) {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_login');
        }

        $medecin = new Medecin();
        $form = $this->createForm(MedecinType::class, $medecin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $medecin->getPhoto();
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter(('upload_directory')), $filename);
            $medecin->setPhoto($filename);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($medecin);
            $entityManager->flush();

            $flashy->success('Médecin créé avec success');
            return $this->redirectToRoute('medecins_index');
        }

        return $this->render('medecin/new.html.twig', [
            'medecin' => $medecin,
            'form' => $form->createView(),
            'position' => 'Nouveau Médecin',
            'chemin' => 'Médecin / Nouveau Médecin',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="medecin_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Medecin $medecin, FlashyNotifier $flashy): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('SUPER_ADMIN', $user->getRoles())) {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(MedecinType::class, $medecin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $medecin->getPhoto();
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter(('upload_directory')), $filename);
            $medecin->setPhoto($filename);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($medecin);
            $entityManager->flush();

            $flashy->success('Médecin édité avec success');
            return $this->redirectToRoute('medecins_index');
        }

        return $this->render('medecin/edit.html.twig', [
            'medecin' => $medecin,
            'form' => $form->createView(),
            'position' => 'Edition médecin',
            'chemin' => 'Médecin / Edition Médecin',
        ]);
    }

    /**
     * @Route("/{id}/particulier", name="medecin_particulier", methods={"GET","POST"})
     */
    public function medParticulier($id, FlashyNotifier $flashy, HttpClientInterface $client): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('SUPER_ADMIN', $user->getRoles())) {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_login');
        }

        //$nom_enseigne = $request->request->get('nom_enseigne');
        //$all_client = $request->request->get('all_client');

        $responseMyClients = $client->request(
            'POST',
            $this->getParameter('API_URL') . $this->apiAllClientUrl
        );

        $content = $responseMyClients->getContent();
        $content_array = json_decode($content, true);
        if ($content_array['server_responses'][0]['founded'] == 1) {
            $flashy->success("Opération réussie");
            //return $this->redirectToRoute('enseigne');
            return $this->render('medecin/particulier.html.twig', [
                'particuliers' => $content_array['server_responses'],
                'position' => 'Liste particulier',
                'id_medecin' => $id,
                'chemin' => 'Particulier / Liste Particulier',
            ]);
        } else {
            $flashy->error("Opération échouée");
            return $this->render('medecin/particulier.html.twig', [
                'particuliers' => [],
                'id_medecin' => $id,
                'position' => 'Liste particulier',
                'chemin' => 'Particulier / Liste Particulier',
            ]);
        }

        //dd($content_array);

    }

    /**
     * @Route("/{id}/particulier/{id_particulier}/liaison", name="medecin_liaison", methods={"GET","POST"})
     */
    public function medLiaison($id, $id_particulier, FlashyNotifier $flashy, HttpClientInterface $client): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('SUPER_ADMIN', $user->getRoles())) {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_login');
        }

        //$nom_enseigne = $request->request->get('nom_enseigne');
        //$all_client = $request->request->get('all_client');
        //$id = $request->request->get('id');
        //dd($id_particulier);

        $responseMyLiaison = $client->request(
            'GET',
            $this->getParameter('API_URL') . $this->apiLiaisonMedecinParticulierUrl,
            [
                'query' => [
                    'id_medecin' => $id,
                    'id_particulier' => $id_particulier
                ]
            ]
        );

        $content = $responseMyLiaison->getContent();
        $content_array = json_decode($content, true);
        if ($content_array['server_responses'][0]['founded'] == 1) {
            $flashy->success("Opération réussie");
            //return $this->redirectToRoute('enseigne');
            return $this->redirectToRoute('medecins_index');
        } else if ($content_array['server_responses'][0]['founded'] == 0) {
            $flashy->error("Opération échouée");
            return $this->redirectToRoute('medecins_index');
        } else {
            $flashy->error("Liaison deja etabli impossible de se lier!");
            return $this->redirectToRoute('medecins_index');

        }
    }

    /**
     * @Route("/{id}/deliaison", name="medecin_deliaison", methods={"GET","POST"})
     */
    public function medDeLiaison($id, FlashyNotifier $flashy, HttpClientInterface $client): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $flashy->error('Veuillez vous connecter');
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('SUPER_ADMIN', $user->getRoles())) {
            $flashy->error('Access denied');
            return $this->redirectToRoute('app_login');
        }

        //$nom_enseigne = $request->request->get('nom_enseigne');
        //$all_client = $request->request->get('all_client');
        //$id = $request->request->get('id');
        //dd($id_particulier);

        $responseMyLiaison = $client->request(
            'GET',
            $this->getParameter('API_URL') . $this->apiLiaisonCouperUrl,
            [
                'query' => [
                    'id_medecin' => $id,
                ]
            ]
        );

        $content = $responseMyLiaison->getContent();
        $content_array = json_decode($content, true);
        if ($content_array['server_responses'][0]['founded'] == 1) {
            $flashy->success("Opération réussie");
            //return $this->redirectToRoute('enseigne');
            return $this->redirectToRoute('medecins_index');
        } else if ($content_array['server_responses'][0]['founded'] == 0) {
            $flashy->error("Opération échouée");
            return $this->redirectToRoute('medecins_index');
        } else {
            $flashy->error("Impossible de délier!");
            return $this->redirectToRoute('medecins_index');

        }
    }
}
