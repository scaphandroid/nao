<?php

namespace NAO\PlatformBundle\Controller;

use NAO\PlatformBundle\Entity\EspeceNomVern;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AutocompleteController extends Controller {

    public function autosearchAction(Request $request){
        $q = $request->query->get('q');
        $results = $this->getDoctrine()->getRepository('NAOPlatformBundle:EspeceNomVern')->findLikeByName($q);
        return $this->render('NAOPlatformBundle:Autocomplete:autocomplete.json.twig', ['results' => $results]);

    }

    public function autogetAction($id = null){
        $espece= $this->getDoctrine()->getRepository('NAOPlatformBundle:EspeceNomVern')->find($id);

        return new Response($espece->getNomVern());
    }
}