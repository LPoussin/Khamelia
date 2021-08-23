<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\UserRepository;
use Symfony\Component\Dotenv\Dotenv;
use App\Repository\UserJoinedEnseigneRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use MercurySeries\FlashyBundle\FlashyNotifier;




class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $apiLoginUrl;
    private $client;
    private $dotenv;
    private $userRepository;
    private $userJoinedEnseigneRepository;
    private $profile;
    private $flashy;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator,
                                CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder,
                                HttpClientInterface $client, UserRepository $userRepository,
                                UserJoinedEnseigneRepository $userJoinedEnseigneRepo, FlashyNotifier $flashy)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->client = $client;
        $this->apiLoginUrl = 'login.php';
        $this->dotenv = new Dotenv();
        $this->dotenv->load('../.env');
        $this->userRepository = $userRepository;
        $this->userJoinedEnseigneRepository = $userJoinedEnseigneRepo;
        $this->profile="";
        $this->flashy = $flashy;

    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        $session = new Session();


        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $response = $this->client->request('POST', $_ENV['API_URL'].$this->apiLoginUrl, [
                'query' => [
                    'email' => $credentials['email'],
                    'passe' => $credentials['password']
                ]
            ]);


        $content = $response->getContent();
        $content_array = json_decode($content, true);


        $result = $content_array['server_response'][0];// dd($result);
        if ($result['status'] == 1) {
            $user = $this->userRepository->findOneBy(['email' => $result['email']]);

            if(!$user)
            {
                $user = new User();
                if($result['type'] == 1)
                {
                    $user->setId($result['id_client']);
                    $user->setRoles(['PARTICULIER']);

                }
                elseif ($result['type'] == 6)
                {
                    $user->setId($result['id_entreprise']);
                    $user->setRoles(['ENTREPRISE']);
                }

//dd($result);
                $user->setEmail($result['email'])
                     ->setNom($result['nom']!==null ? $result['nom'] : "")
                     ->setPrenom(key_exists('prenoms', $result) ? $result['prenoms'] : "")
                     ->setType($result['type'])
                     ->setPassword($this->passwordEncoder->encodePassword($user, $credentials['password']));
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }else{//--ril
				//Si l'user tente de se connecter a partir d'une autre extension de net2all alors qu'il avait changé son password
				$encc=$this->passwordEncoder->encodePassword($user, $credentials['password']);
				if($encc!=$user->getPassword()){
					$user->setPassword($encc);
					$this->entityManager->flush();
				}
			}

            return $user;
        }
        else
        {
            throw new CustomUserMessageAuthenticationException('Adresse mail introuvable.');
        }

        /*$session = new Session();
        $onlineUserProfile = $this->userJoinedEnseigneRepository->findOneBy(['id_user' => $user->getId()]);

        if(in_array('secretaire', $onlineUserProfile->getProfiles())){


            $this->profiles = 'secretaire';
            $session->set('profile',  $this->profiles );
        }*/


    }

    public function checkCredentials($credentials, UserInterface $user)
    {
		//dd($user,$this->passwordEncoder->isPasswordValid($user, $credentials['password']));
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        // For example : return new RedirectResponse($this->urlGenerator->generate('some_route'));
       // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
        return new RedirectResponse($this->urlGenerator->generate('dashboard'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
