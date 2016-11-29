<?php

namespace NAO\PlatformBundle\Controller;

use NAO\PlatformBundle\Entity\EspeceNomVern;
use NAO\PlatformBundle\Entity\Observation;
use NAO\PlatformBundle\Entity\User;
use NAO\PlatformBundle\Form\EspeceNomVernType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use NAO\PlatformBundle\Form\ObservationType;
use NAO\PlatformBundle\Form\UserType;
use NAO\PlatformBundle\Form\UserParticulierType;
use Symfony\Component\HttpFoundation\Request;


class PlatformController extends Controller
{
    public function indexAction(Request $request)
    {
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
        /*Encode en JSON les coordonnées de ces dernières observations */
        $observation = [];
        for ($i=0; $i<count($listDerObs); $i++) {
            $observation[$i] = array(
                "username" => $listDerObs[$i]->getUser()->getUsername(),
                "date" => $listDerObs[$i]->getDateObs()->format('d-m-Y'),
                "photoObs" => basename($listDerObs[$i]->getPhoto()),
                "lat" => $listDerObs[$i]->getLat(),
                "lon" => $listDerObs[$i]->getLon(),
                "valide" => $listDerObs[$i]->getValide(),
                "espece" => $listDerObs[$i]->getEspeceNomVern()->getNomVern()
            );
        }
        $observation_JSON = json_encode($observation);

        return $this->render('NAOPlatformBundle:Platform:index.html.twig', array(
            'form' => $form->createView(),
            'DerObs' => $listDerObs,
            'observation_JSON' => $observation_JSON
        ));
    }

    public function rechercherAction(Request $request)
    {
        $espece = new EspeceNomVern();
        $form = $this->createForm(EspeceNomVernType::class, $espece);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            /*Afficher la carte avec l'espece recherchée */
            return $this->render('NAOPlatformBundle:Platform:rechercher.html.twig', array(
                'form' => $form->createView(),
                'espece' => $espece
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
