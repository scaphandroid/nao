<?php

namespace NAO\PlatformBundle\Services\JsonEncode;

class NAOJsonEncode
{

    public function jsonEncode($listObserv){
        $observation = [];

        for ($i=0; $i<count($listObserv); $i++) {
            $observation[$i] = array(
                "username" => $listObserv[$i]->getUser()->getUsername(),
                "date" => $listObserv[$i]->getDateObs()->format('d-m-Y'),
                "photoObs" => basename($listObserv[$i]->getPhoto()),
                "lat" => $listObserv[$i]->getLat(),
                "lon" => $listObserv[$i]->getLon(),
                "valide" => $listObserv[$i]->getValide(),
                "espece" => $listObserv[$i]->getEspeceNomVern()->getNomVern()
            );
        }

        return  json_encode($observation);
    }
}