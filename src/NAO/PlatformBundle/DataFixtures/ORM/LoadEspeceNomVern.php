<?php

namespace NAO\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use NAO\PlatformBundle\Entity\EspeceNomVern;


class LoadEspeceNomVern extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $fichierCSV = fopen(dirname(__FILE__).'\Entite_Espece_pour import.csv', 'r');

        //on fera un flush toutes les 20 entrées
        $batchSize = 20;

        $i = 0;
        while(!feof($fichierCSV)){

            $i++;

            $ligne = fgetcsv($fichierCSV, 300, ';');

            //en cas de ligne vide
            if($ligne[0] !== null || $ligne[0] === ''){
                $espece = new EspeceNomVern();

                $espece->setNomVern($ligne[1]);
                $espece->setNomLatin($ligne[2]);
                $espece->setNomConcat($ligne[3]);
                $espece->setUrl($ligne[4]);

                //pour récupérer dans la fixture suivante
                $lignesPourRef = array( '32', '516', '580','827','1002');
                if(in_array($ligne[0], $lignesPourRef)){
                    $this->addReference($ligne[1], $espece);
                }

                $manager->persist($espece);
                if($i === $batchSize){
                    $manager->flush();
                    $manager->clear();
                    $i = 0;
                }
            }
        }

        $manager->flush();
        $manager->clear();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 2;
    }
}