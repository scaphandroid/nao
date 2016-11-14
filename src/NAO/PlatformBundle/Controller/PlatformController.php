<?php

namespace NAO\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PlatformController extends Controller
{
    public function indexAction()
    {
        return $this->render('NAOPlatformBundle:Platform:index.html.twig');
    }

    public function rechercherAction($id)
    {
        if ($id == null) {// afficher toutes les observations
            return new Response("Affichage de toutes les observations");
        }
        else {
            return $this->render('NAOPlatformBundle:Platform:rechercher.html.twig', array(
                'especeId'=>$id
            ));

        }
    }

    public function observerAction()
    {
        return $this->render('NAOPlatformBundle:Platform:observer.html.twig');
    }
}
