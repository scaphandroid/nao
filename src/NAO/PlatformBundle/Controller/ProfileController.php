<?php

namespace NAO\PlatformBundle\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use NAO\PlatformBundle\Entity\User;
use NAO\PlatformBundle\Form\ObservationsSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use NAO\PlatformBundle\Form\DevenirNaturalisteType;
use NAO\PlatformBundle\Form\ValiderType;




class ProfileController extends Controller
{
    /**
     * Show the user.
     */
    public function showAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('FOSUserBundle:Profile:show.html.twig', array(
            'user' => $user,
        ));
    }

    public function mesObservationsAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace est réservé aux utilisateurs enregistrés !');
            return $this->redirectToRoute('fos_user_security_login');
        }
        // l'administrateur ne fait pas d'observations
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
            $request->getSession()->getFlashBag()->add('notice', 'L\'espace "mes observations" est réservé aux particuliers et naturalistes !');
            return $this->redirectToRoute('fos_user_profile_show');
        }

        $listObserv = $this->getDoctrine()->getManager()
            ->getRepository('NAOPlatformBundle:Observation')
            ->getListObsByUser($user->getId());

        // les observations sont encodées en json pour être affichées sur la carte, via le service dédié
        $observation_JSON = $this->get('service_container')->get('nao_platform.jsonencode')->jsonEncode($listObserv, $request->getSchemeAndHttpHost());

        return $this->render('@NAOPlatform/Profile/mesObservations.html.twig', array(
            'user' => $user,
            'observation_JSON' => $observation_JSON,
        ));
    }

    public function observationsEnAttenteAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace est réservé aux utilisateurs enregistrés !');
            return $this->redirectToRoute('fos_user_security_login');
        }
        // cet espace est réservé aux naturalistes
        $checker = $this->get('security.authorization_checker');
        if ($checker->isGranted('ROLE_ADMIN') == false || $checker->isGranted('ROLE_SUPER_ADMIN')){
            $request->getSession()->getFlashBag()->add('notice', 'L\'espace "observations en attente" est réservé aux naturalistes !');
            return $this->redirectToRoute('fos_user_profile_show');
        }

        $ObservNonValide = $this->getDoctrine()->getManager()
            ->getRepository('NAOPlatformBundle:Observation')
            ->getListObsNonvalideEnAttente();

        return $this->render('@NAOPlatform/Profile/observationsEnAttente.html.twig', array(
            'user' => $user,
            'ObservNonValide' => $ObservNonValide
        ));
    }

    public function listeNaturalistesAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace est réservé aux utilisateurs enregistrés !');
            return $this->redirectToRoute('fos_user_security_login');
        }
        // cet espace est réservé aux administrateurs
        if ( $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') == false){
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace ne vous est pas accessible !');
            return $this->redirectToRoute('fos_user_profile_show');
        }


        $userRepo = $this->getDoctrine()->getManager()->getRepository('NAOPlatformBundle:User');

        //on récupère les comptes naturalistes en attente à part
        $comptesNatNonValides = $userRepo->getComptesNatNonValides();

        $comptesNaturalistes = $userRepo->getComptesNat();
            
        return $this->render('@NAOPlatform/Profile/listeNaturalistes.html.twig', array(
            'user' => $user,
            'comptesNatNonValides' => $comptesNatNonValides,
            'comptesNaturalistes' => $comptesNaturalistes
            
        ));
    }

    public function observationRefuseesAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace est réservé aux utilisateurs enregistrés !');
            return $this->redirectToRoute('fos_user_security_login');
        }
        // cet espace est réservé aux naturalistes
        $checker = $this->get('security.authorization_checker');
        if ($checker->isGranted('ROLE_ADMIN') == false || $checker->isGranted('ROLE_SUPER_ADMIN')){
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace ne vous est pas accessible !');
            return $this->redirectToRoute('fos_user_profile_show');
        }

        $observRefusees = $this->getDoctrine()->getManager()
            ->getRepository('NAOPlatformBundle:Observation')
            ->getListObsRefuseesParNaturaliste($user->getId());

        return $this->render('NAOPlatformBundle:Profile:observationsRefusees.html.twig', array(
           'user'=> $user,
            'ObservRefusees' => $observRefusees
        ));
    }

    /**
     * Edit the user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('NAOPlatformBundle:Profile:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function devenirNaturalisteAction(Request $request) {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace est réservé aux utilisateurs enregistrés !');
            return $this->redirectToRoute('fos_user_security_login');
        }

        $form = $this->createForm(DevenirNaturalisteType::class, $user);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $user->setEnAttente(true);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Demande de compte naturaliste bien enregistrée. Vous allez être contacter par nos équipes.');
            return $this->redirectToRoute('nao_platform_home');
        }
        return $this->render('NAOPlatformBundle:Profile:devenirNaturaliste.html.twig', array(
            'user'=> $user,
            'form' => $form->createView(),
        ));
    }

    public function detailCompteNaturalisteAction(Request $request, User $naturaliste) {
        $user = $this->getUser();
        $form = $this->createForm(ValiderType::class);
        $validation = false;

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $naturaliste->setEnAttente(false);
            $naturaliste->setEnabled(true); // Même si une demande de compte naturaliste sans compte particulier a été faite, un compte particulier est crée
            if ($form->get('valider')->isClicked()) {
                $naturaliste->setTypeCompte(1);
                $naturaliste->addRole('ROLE_ADMIN');
                $message = "Compte naturaliste validé.";
                $validation = true;
              }
            if ($form->get('invalider')->isClicked()) {
                $naturaliste->setTypeCompte(0);
                $message = "Compte naturaliste invalidé.";
                $validation = false;
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            /* envoi du mail au particulier avec le service sendMail*/
            $this->get('nao_platform.sendmail')->sendMail($naturaliste, $validation);
            $request->getSession()->getFlashBag()->add('notice', $message);

            return $this->redirectToRoute('nao_profile_listenaturalistes');
        }

        return $this->render('NAOPlatformBundle:Profile:detailCompteNaturaliste.html.twig', array(
            'user'=> $user,
            'naturaliste' => $naturaliste,
            'form' => $form->createView()
        ));
    }

    public function modererObservationsAction(Request $request) {
        $user = $this->getUser();

        $form=$this->createForm(ObservationsSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // on récupère les données de recherche
            $data = $form->getData();

            $manager = $this->getDoctrine()->getManager();

            //on récupère les espèces correspondant à la recherche
            $listObserv = $manager->getRepository('NAOPlatformBundle:Observation')
                ->getListObsByParameters($data);

            var_dump($listObserv);
            /*    $listeEspeces = $manager->getRepository('NAOPlatformBundle:Espece')
                    ->findLikeByName($data["nomConcat"], 100);*/

            //on récupère les observations valides correspondants aux espèces
            /*    $listObserv = $manager
                    ->getRepository('NAOPlatformBundle:Observation')
                    ->getListObsByEspeceValides($listeEspeces);*/
        }

        return $this->render('NAOPlatformBundle:Profile:modererObservations.html.twig', array(
            'user'=> $user,
            'form' => $form->createView()
        ));
    }

    public function observationAction($id, Request $request){

        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace est réservé aux utilisateurs enregistrés !');
            return $this->redirectToRoute('fos_user_security_login');
        }

        $observation = $this->getDoctrine()->getManager()->getRepository('NAOPlatformBundle:Observation')->find($id);

        // vérification des accès
        $checker = $this->get('security.authorization_checker');
        // un particulier ne peut consulter que sa propre observation
        if ($checker->isGranted('ROLE_ADMIN') == false && $observation->getUser() !== $user->getId())
        {
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace ne vous est pas accessible !');
            return $this->redirectToRoute('fos_user_profile_show');
        }
        // TODO Mettre d'autres limitations?

        $observation_JSON = $this->get('nao_platform.jsonencode')->jsonEncode(array($observation), $request->getSchemeAndHttpHost());
        
        return $this->render('@NAOPlatform/Profile/observation.html.twig', array(
            "observation" => $observation,
            "user" => $user,
            'observation_JSON' => $observation_JSON,
        ));
    }
}