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

        $manager = $this->getDoctrine()->getManager();
        $listObserv = $manager
            ->getRepository('NAOPlatformBundle:Observation')
            ->getListObsByUser($user->getId());

        /*Encode en JSON les coordonnées de ces observations FAIRE UN SERVICE (on s'en sert aussi dans indexaction)*/
        $observation = [];
        for ($i=0; $i<count($listObserv); $i++) {
            $observation[$i] = array(
                "username" => $listObserv[$i]->getUser()->getUsername(),
                "date" => $listObserv[$i]->getDateObs()->format('d-m-Y'),
                "photoObs" => basename($listObserv[$i]->getPhoto()),
                "lat" => $listObserv[$i]->getLat(),
                "lon" => $listObserv[$i]->getLon(),
                "valide" => $listObserv[$i]->getValide(),
                "espece" => $listObserv[$i]->getEspeceNomVern()->getNomVern()
            );
        }
        $observation_JSON = json_encode($observation);

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            $comptesNatNonValides = $manager
                ->getRepository('NAOPlatformBundle:User')
                ->getComptesNatNonValides();
            return $this->render('FOSUserBundle:Profile:show.html.twig', array(
                'user' => $user,
                'comptesNatNonValides' => $comptesNatNonValides
            ));
        }


        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $ObservNonValide = $manager
                ->getRepository('NAOPlatformBundle:Observation')
                ->getListObsNonvalideEnAttente();
            return $this->render('FOSUserBundle:Profile:show.html.twig', array(
                'user' => $user,
                'observation_JSON' => $observation_JSON,
                'ObservNonValide' => $ObservNonValide
            ));
        }


        return $this->render('FOSUserBundle:Profile:show.html.twig', array(
            'user' => $user,
            'observation_JSON' => $observation_JSON,
        ));
    }

}