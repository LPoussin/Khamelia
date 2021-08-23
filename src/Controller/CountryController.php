<?php

namespace App\Controller;

use App\Repository\CountryRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/country")
 */
class CountryController extends AbstractController
{   
    /**
     * @Route("/", name="country_index", methods={"GET"})
     */
    public function index(CountryRepository $paysRepository, FlashyNotifier $flashy): Response
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
            return $this->redirectToRoute('app_login');
        }

        return $this->render('country/index.html.twig', [
            'pays' => $paysRepository->findAll(),
            'position' => 'Pays',
            'chemin' => 'Pays',
        ]);
    }
}
