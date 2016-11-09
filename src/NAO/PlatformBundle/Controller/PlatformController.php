<?php

namespace NAO\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PlatformController extends Controller
{
    public function indexAction()
    {
        return $this->render('NAOPlatformBundle:Platform:index.html.twig');
    }
}
