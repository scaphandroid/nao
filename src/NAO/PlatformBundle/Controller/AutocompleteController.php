<?php

namespace NAO\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AutocompleteController extends Controller {

    //pour le traitement de la requête en autocomplete
    public function autosearchAction(Request $request){
        $q = $request->query->get('term');
        if($request->isXmlHttpRequest()){
            $results = $this->getDoctrine()->getRepository('NAOPlatformBundle:Espece')->findLikeByName($q, 10);
            return $this->render('NAOPlatformBundle:Autocomplete:autocomplete.json.twig', ['results' => $results]);
        }
        return $this->redirectToRoute('nao_platform_home');
    }

    public function autogetAction($id = null){
        $espece= $this->getDoctrine()->getRepository('NAOPlatformBundle:Espece')->find($id);
        return new Response($espece->getNomConcat());
    }
}