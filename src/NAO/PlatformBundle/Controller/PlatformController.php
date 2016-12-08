<?php

namespace NAO\PlatformBundle\Controller;

use NAO\PlatformBundle\Entity\EspeceNomVern;
use NAO\PlatformBundle\Entity\Observation;
use NAO\PlatformBundle\Entity\User;
use NAO\PlatformBundle\Form\EspeceNomVernType;
use NAO\PlatformBundle\Form\RechercheType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use NAO\PlatformBundle\Form\ObservationType;
use NAO\PlatformBundle\Form\UserType;
use NAO\PlatformBundle\Form\UserParticulierType;
use Symfony\Component\HttpFoundation\Request;
use PUGX\AutocompleterBundle\Form\Type\AutocompleteType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Cookie;


class PlatformController extends Controller
{
    public function indexAction(Request $request)
    {
        $first_visit = $request->cookies->has('popup_first_visit');
        if(!$first_visit) {
            $response = new Response();
            $response->headers->setCookie(new Cookie('popup_first_visit', 'charte_not_approved', time() + 3600 * 24 * 365, '/'));
            $response->send();
        }
        $user = $this->getUser();
        $typeCompte = ($user == null) ? null : $user->getTypeCompte();
        $espece = new EspeceNomVern();
        $form = $this->createForm(EspeceNomVernType::class, $espece);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            return $this->render('NAOPlatformBundle:Platform:rechercher.html.twig', array(
                'form' => $form->createView(),
                'espece' => $espece
            ));
        }

        $manager = $this->getDoctrine()->getManager();
        $listDerObs = $manager
            ->getRepository('NAOPlatformBundle:Observation')
            ->getDerObsValides(30); //Observation des X derniers jours

        // les observations sont encodées en json pour être affichées sur la carte, via le service dédié
        $observation_JSON = $this->get('service_container')->get('nao_platform.jsonencode')->jsonEncode($listDerObs);

        return $this->render('NAOPlatformBundle:Platform:index.html.twig', array(
            'form' => $form->createView(),
            'DerObs' => $listDerObs,
            'observation_JSON' => $observation_JSON,
            'typeCompte' => $typeCompte,
            'first_visit' => $first_visit
        ));
    }

    public function rechercherAction(Request $request)
    {
        $user = $this->getUser();
        $typeCompte = ($user == null) ? null : $user->getTypeCompte();
        $form=$this->createForm(RechercheType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // data is an array with "nomVern" keys
            $data = $form->getData();

            /*Afficher la carte avec l'espece recherchée */
            $manager = $this->getDoctrine()->getManager();
            $listObserv = $manager
                ->getRepository('NAOPlatformBundle:Observation')
                ->getListObsByNomVernValides($data["nomVern"]);

            // les observations sont encodées en json pour être affichées sur la carte, via le service dédié
            $observation_JSON = $this->get('service_container')->get('nao_platform.jsonencode')->jsonEncode($listObserv);

            return $this->render('NAOPlatformBundle:Platform:rechercher.html.twig', array(
                'form' => $form->createView(),
                'observation_JSON' => $observation_JSON,
                'typeCompte' => $typeCompte,
                'nomEspece' => $data["nomVern"]
            ));
        }

        return $this->render('NAOPlatformBundle:Platform:rechercher.html.twig', array(
            'form' => $form->createView()
        ));

    }

    public function observerAction(Request $request)
    {
        $user = $this->getUser();

        if($user === null){
            $request->getSession()->getFlashBag()->add('notice', 'Merci de vous enregistrer pour réaliser une observation !');
            return $this->redirectToRoute('fos_user_security_login');
        }

        $observation = new Observation();
        $form = $this->createForm(ObservationType::class, $observation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            {
                $observation->setValide(true);
                $observation->setEnAttente(false);
                $request->getSession()->getFlashBag()->add('notice', 'Observation bien enregistrée.');
            }
            else
            {
                $observation->setValide(false);
                $observation->setEnAttente(true);
                $request->getSession()->getFlashBag()->add('notice', 'Observation bien enregistrée. En attente de validation.');
            }
            $observation->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($observation);
            $em->flush();

            return $this->redirectToRoute('nao_platform_home');
        }

        return $this->render('NAOPlatformBundle:Platform:observer.html.twig', array(
            'form' => $form->createView()
        ));
    }

/*    public function compteAction(Request $request)
    {
        $particulier = new User();
        $form = $this->createForm(UserParticulierType::class, $particulier);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            /* modifier certains attributs comme "valide", "role"*/
 /*           $em = $this->getDoctrine()->getManager();
            $em->persist($particulier);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Compte enregistré. Vous allez recevoir un email de confirmation.');
            // Faire une page avec message du type : Vous allez recevoir un email vous demandant de cliquer pour valider la création de votre compte ?
            return $this->redirectToRoute('nao_platform_home');
        }

        return $this->render('NAOPlatformBundle:Platform:compte.html.twig', array(
            'formInscription' => $form->createView()
        ));
    }*/

    public function demandeAction(Request $request)
    {
        $naturaliste = new User();
        $form = $this->createForm(UserType::class, $naturaliste);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            /* modifier certains attributs comme "valide"*/
            $em = $this->getDoctrine()->getManager();
            $em->persist($naturaliste);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Demande de compte naturaliste bien enregistrée. Vous allez être contacter par nos équipes.');
            return $this->redirectToRoute('nao_platform_home');
        }

        return $this->render('NAOPlatformBundle:Platform:demandeCompteNaturaliste.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
