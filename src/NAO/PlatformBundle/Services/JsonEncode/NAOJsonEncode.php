<?php

namespace NAO\PlatformBundle\Services\JsonEncode;

use Symfony\Component\HttpFoundation\Request;

class NAOJsonEncode
{

    public function jsonEncode($listObserv, $baseUrl){
        $observation = [];

        $request = new Request();
        $url = $baseUrl.'/Projet_6_NAO/nao/web/uploads/photos/' ; /*Pour Alexis*/
    /*    $url = $baseUrl.'/Projets_OC/Projet6_NAO/web/uploads/photos/' ; Pour Phil*/

        for ($i=0; $i<count($listObserv); $i++) {
            $observation[$i] = array(
                "username" => $listObserv[$i]->getUser()->getUsername(),
                "date" => $listObserv[$i]->getDateObs()->format('d-m-Y'),
                "photoObs" => $url.$listObserv[$i]->getPhoto(),
                "lat" => $listObserv[$i]->getLat(),
                "lon" => $listObserv[$i]->getLon(),
                "valide" => $listObserv[$i]->getValide(),
                "espece" => $listObserv[$i]->getEspece()->getNomConcat()
            );
        }

        return  json_encode($observation);
    }
}