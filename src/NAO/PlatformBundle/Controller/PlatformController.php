<?php

namespace NAO\PlatformBundle\Controller;

use NAO\PlatformBundle\Entity\Espece;
use NAO\PlatformBundle\Entity\Observation;
use NAO\PlatformBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use NAO\PlatformBundle\Form\ObservationType;
use NAO\PlatformBundle\Form\EspeceType;
use NAO\PlatformBundle\Form\UserType;
use NAO\PlatformBundle\Form\UserParticulierType;
use Symfony\Component\HttpFoundation\Request;

class PlatformController extends Controller
{
    public function indexAction(Request $request)
    {
        $espece = new Espece();
        $form = $this->createForm(EspeceType::class, $espece);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            return $this->render('NAOPlatformBundle:Platform:rechercher.html.twig', array(
                'form' => $form->createView(),
                'espece' => $espece
            ));
        }

        return $this->render('NAOPlatformBundle:Platform:index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function rechercherAction(Request $request)
    {
        $espece = new Espece();
        $form = $this->createForm(EspeceType::class, $espece);

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
        $observation = new Observation();
        $form = $this->createForm(ObservationType::class, $observation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($observation);
            $em->flush();
            /*Faire une disctinction du message si particulier ou naturaliste */
            $request->getSession()->getFlashBag()->add('notice', 'Observation bien enregistrée.');
            return $this->redirectToRoute('nao_platform_home');

        }

        return $this->render('NAOPlatformBundle:Platform:observer.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function compteAction(Request $request)
    {
        $particulier = new User();
        $form = $this->createForm(UserParticulierType::class, $particulier);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            /* modifier certains attributs comme "valide", "role"*/
            $em = $this->getDoctrine()->getManager();
            $em->persist($particulier);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Compte enregistré. Vous allez recevoir un email de confirmation.');
            // Faire une page avec message du type : Vous allez recevoir un email vous demandant de cliquer pour valider la création de votre compte ?
            return $this->redirectToRoute('nao_platform_home');
        }

        return $this->render('NAOPlatformBundle:Platform:compte.html.twig', array(
            'formInscription' => $form->createView()
        ));
    }

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
