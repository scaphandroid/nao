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
use NAO\PlatformBundle\Form\ValiderObsType;
use NAO\PlatformBundle\NAOPlatformBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use NAO\PlatformBundle\Form\DevenirNaturalisteType;
use NAO\PlatformBundle\Entity\Observation;
use NAO\PlatformBundle\Form\ObservationsSearchType;
use NAO\PlatformBundle\Form\ValiderType;
use Symfony\Component\HttpFoundation\RedirectResponse;


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
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
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
            'listeObservations' => $listObserv
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
        if ($checker->isGranted('ROLE_ADMIN') == false || $checker->isGranted('ROLE_SUPER_ADMIN')) {
            $request->getSession()->getFlashBag()->add('notice', 'L\'espace "observations en attente" est réservé aux naturalistes !');
            return $this->redirectToRoute('fos_user_profile_show');
        }

        $ObservNonValide = $this->getDoctrine()->getManager()
            ->getRepository('NAOPlatformBundle:Observation')
            ->getListObsNonvalideEnAttente();

        return $this->render('@NAOPlatform/Profile/observationsEnAttente.html.twig', array(
            'user' => $user,
            'listeObservations' => $ObservNonValide
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
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') == false) {
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace ne vous est pas accessible !');
            return $this->redirectToRoute('fos_user_profile_show');
        }


        $userRepo = $this->getDoctrine()->getManager()->getRepository('NAOPlatformBundle:User');

        //on récupère les comptes naturalistes en attente et invalidés à part
        $comptesNatNonValides = $userRepo->getComptesNatNonValides();
        $compteNatRefuses = $userRepo->getComptesNatRefuses();
        $comptesNaturalistes = $userRepo->getComptesNat();

        return $this->render('@NAOPlatform/Profile/listeNaturalistes.html.twig', array(
            'user' => $user,
            'comptesNatNonValides' => $comptesNatNonValides,
            'comptesNaturalistes' => $comptesNaturalistes,
            'compteNatRefuses' => $compteNatRefuses
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
        if ($checker->isGranted('ROLE_ADMIN') == false || $checker->isGranted('ROLE_SUPER_ADMIN')) {
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace ne vous est pas accessible !');
            return $this->redirectToRoute('fos_user_profile_show');
        }

        $observRefusees = $this->getDoctrine()->getManager()
            ->getRepository('NAOPlatformBundle:Observation')
            ->getListObsRefuseesParNaturaliste($user->getId());

        return $this->render('NAOPlatformBundle:Profile:observationsRefusees.html.twig', array(
            'user' => $user,
            'listeObservations' => $observRefusees
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
            throw new AccessDeniedException('Cet espace est réservé aux utilisateurs enregistrés !');
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

        return $this->render('FOSUserBundle:Profile:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function devenirNaturalisteAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace est réservé aux utilisateurs enregistrés !');
            return $this->redirectToRoute('fos_user_security_login');
        }

        $form = $this->createForm(DevenirNaturalisteType::class, $user);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $user->setEnAttente(true);

            //traitement du pdf , le traitement de l'upload(déplacement, nouveau nom) se fait via le service
            if($user->getCv()!== null){
                $pdf = $user->getCv();
                $fichierPdf = $this->get('nao_platform.fileuploader')->upload($pdf, 'pdfDirectory');
                $user->setCv($fichierPdf);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Demande de compte naturaliste bien enregistrée. Vous allez être contacté par nos équipes.');
            return $this->redirectToRoute('nao_platform_home');
        }
        return $this->render('FOSUserBundle:Profile:devenirNaturaliste.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    public function detailCompteNaturalisteAction(Request $request, User $naturaliste)
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

        $form = $this->createForm(ValiderType::class);
        $validation = false;

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $naturaliste->setEnAttente(false);
            $naturaliste->setEnabled(true); // Même si une demande de compte naturaliste sans compte particulier a été faite, un compte particulier est crée
            if ($form->get('valider')->isClicked()) {
                $naturaliste->setTypeCompte(1);
                $naturaliste->setValide(true);
                $naturaliste->addRole('ROLE_ADMIN');
                $message = "Compte naturaliste validé.";
                $validation = true;
              }
            if ($form->get('invalider')->isClicked()) {
                $naturaliste->setTypeCompte(0);
                $naturaliste->setValide(false);
                $message = "Compte naturaliste invalidé.";
                $naturaliste->removeRole('ROLE_ADMIN');
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
        if (!is_object($user) || !$user instanceof UserInterface) {
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace est réservé aux utilisateurs enregistrés !');
            return $this->redirectToRoute('fos_user_security_login');
        }
        // cet espace est réservé aux administrateurs
        if ( $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') == false){
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace ne vous est pas accessible !');
            return $this->redirectToRoute('fos_user_profile_show');
        }

        $form=$this->createForm(ObservationsSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère les données de recherche
            $data = $form->getData();
            $manager = $this->getDoctrine()->getManager();

            //on récupère les espèces correspondant à la recherche
            $listObserv = $manager->getRepository('NAOPlatformBundle:Observation')
                ->getListObsByParameters($data);

            /*    var_dump($listObserv);*/
            return $this->render('NAOPlatformBundle:Profile:modererObservations.html.twig', array(
                'user' => $user,
                'form' => $form->createView(),
                'listObserv' => $listObserv
            ));
        }

        return $this->render('NAOPlatformBundle:Profile:modererObservations.html.twig', array(
            'user'=> $user,
            'form' => $form->createView()
        ));
    }

    public function traiterObservationAction(Request $request, Observation $observation) {
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
        if ($observation == null) {
            throw $this->createNotFoundException("L'observation n°" . $observation->getId() . " n'existe pas.");
        }
     /*   $observation->setEnAttente(true);*/
        $message = null;
        if ($observation->getValide()) {
            $observation->setValide(false);
            $message = "L'observation a bien été invalidée.";
        }
        else {
            $observation->setValide(true);
            $message = "L'observation a bien été validée.";
        }
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', $message);
        return $this->redirectToRoute('nao_profile_modererobservations');
    }

    public function observationAction($id, Request $request){
        $user = $this->getUser();

        //on vérifie si l'utilisateur est enregistré, sinon il n'a dans tous les cas pas accès à cette page
        if (!is_object($user) || !$user instanceof UserInterface) {
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace est réservé aux utilisateurs enregistrés !');
            return $this->redirectToRoute('fos_user_security_login');
        }

        //on récupère l'observation
        $em = $this->getDoctrine()->getManager();
        $observation = $em->getRepository('NAOPlatformBundle:Observation')->find($id);

        // vérification des accès, en fonction notamment de l'auteur de l'observation
        $checker = $this->get('security.authorization_checker');
        //on vérifie si l'observation est consultée par son propre auteur
        $observationPerso = ( $observation->getUser() === $user ) ? true : false ;
        // un particulier ne peut consulter que sa propre observation
        if (($checker->isGranted('ROLE_ADMIN') == false && !$observationPerso))
        {
            $request->getSession()->getFlashBag()->add('notice', 'Cet espace ne vous est pas accessible !');
            return $this->redirectToRoute('fos_user_profile_show');
        }
        //un naturaliste ne peut consulter que les observations non en attente qu'il a traité et les siennes
        //on lui indique néamoins qui a traité l'observation
        if( $checker->isGranted('ROLE_ADMIN') && $checker->isGranted('ROLE_SUPER_ADMIN') == false && !$observation->getEnAttente() && !$observationPerso && $observation->getValidateur() !== $user)
        {
            $request->getSession()->getFlashBag()->add('notice', 'Cette observation est déjà traitée par '.$observation->getValidateur()->getUsername().' !');
            return $this->redirectToRoute('fos_user_profile_show');
        }

        //on prépare l'affichage de l'observation sur la carte
        $observation_JSON = $this->get('nao_platform.jsonencode')->jsonEncode(array($observation), $request->getSchemeAndHttpHost());

        //pour la validation/invalidation, le formulaire n'est pas accessible au particulier, ni au naturaliste si il s'agit de sa propre observation ou si l'observation est déjà traitée
        $form = null;
        if(($checker->isGranted('ROLE_ADMIN') && !$observationPerso && $observation->getEnAttente()) || $checker->isGranted('ROLE_SUPER_ADMIN')){
            if(!$checker->isGranted('ROLE_SUPER_ADMIN') && $observation->getEnAttente()) {
                $form = $this->createForm(ValiderObsType::class);
            }
            else{
                $form = $this->createForm(ValiderType::class);
            }

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                if($form->get('valider')->isClicked()){
                    $observation->setValide(true);
                    $observation->setEnAttente(false);
                    $message = "Observation validée";
                }
                elseif($form->get('invalider')->isClicked()){
                    $observation->setValide(false);
                    $observation->setEnAttente(false);
                    $message = "Observation invalidée";
                }
                $observation->setCommentaireN($form->get('commentaireN')->getData());
                $observation->setValidateur($user);
                $em->persist($observation);
                $em->flush();
                if (isset($message)) $this->addFlash('notice', $message);
            }
            $form = $form->createView();
        }

        //on veut s'assurer qu'après validation le naturaliste ne puisse plus modifier l'observation
        if(!$checker->isGranted('ROLE_SUPER_ADMIN') && !$observation->getEnAttente() ){
            $form = null;
        }

        return $this->render('@NAOPlatform/Profile/observation.html.twig', array(
            "observation" => $observation,
            "user" => $user,
            'observation_JSON' => $observation_JSON,
            'form' => $form
        ));
    }

    public function exportCSVObsAction()
    {
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getRepository('NAOPlatformBundle:Observation');
        $data = $repository->findAll();
        $filename = "export_obs_" . date("Y_m_d_His") . ".csv";

        $response = $this->render('NAOPlatformBundle:Exports:adminCsvObs.html.twig', array(
            'data' => $data,
            'user' => $user));
        $response->headers->set('Content-Type', 'text/csv');

        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        return $response;
    }
    public function exportCSVUsersAction()
    {
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getRepository('NAOPlatformBundle:User');
        $data = $repository->findAll();
        $filename = "export_usr_" . date("Y_m_d_His") . ".csv";

        $response = $this->render('NAOPlatformBundle:Exports:adminCsvUsr.html.twig', array(
            'data' => $data,
            'user' => $user));
        $response->headers->set('Content-Type', 'text/csv');

        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        return $response;
    }
    public function exportCSVEspecesAction()
    {
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getRepository('NAOPlatformBundle:Espece');
        $data = $repository->findAll();
        $filename = "export_esp_" . date("Y_m_d_His") . ".csv";

        $response = $this->render('NAOPlatformBundle:Exports:adminCsvEsp.html.twig', array(
            'data' => $data,
            'user' => $user));
        $response->headers->set('Content-Type', 'text/csv');

        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        return $response;
    }
}