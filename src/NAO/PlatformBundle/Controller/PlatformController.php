<?php

namespace NAO\PlatformBundle\Controller;

use NAO\PlatformBundle\Entity\Espece;
use NAO\PlatformBundle\Entity\Observation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use NAO\PlatformBundle\Form\ObservationType;
use NAO\PlatformBundle\Form\EspeceType;
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
}
