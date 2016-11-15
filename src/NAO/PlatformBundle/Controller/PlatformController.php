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

    public function rechercherAction()
    {
        return $this->render('NAOPlatformBundle:Platform:rechercher.html.twig');
    }

    public function observerAction()
    {
        return $this->render('NAOPlatformBundle:Platform:observer.html.twig');
    }
}
