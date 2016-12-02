<?php

namespace NAO\PlatformBundle\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;



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
        $observation_JSON = $this->get('service_container')->get('nao_platform.jsonencode')->jsonEncode($listObserv);

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
}