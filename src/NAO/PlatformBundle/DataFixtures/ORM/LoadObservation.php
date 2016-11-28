<?php

namespace NAO\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NAO\PlatformBundle\Entity\Observation;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use NAO\PlatformBundle\Entity\EspeceNomVern;
use NAO\PlatformBundle\Entity\EspeceNomLatin;
use NAO\PlatformBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\File;

class LoadObservation extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
    /*    $user = new User();
        $manager->persist($user);*/
        $espece = new EspeceNomVern('EspeceFixture');
        $photo = new File(__DIR__ .'/../../../../../web/images/oiseau1.jpg');
        $manager->persist($espece);
        // On crée 4 observations qu'on va assigner à nos user

        $listObservations = array(
            array(
                'dateObserv' => new \DateTime('2016-11-20'),
                'especeVern' => 'Aigle imperial',
                'localise' => true,
                'lat' => 44.806004,
                'long' => 4.420166,
                'valide' => false,
                'validateur' => null,
                'user' => 'user-admin'),
            array(
                'dateObserv' => new \DateTime('2016-11-15'),
                'especeVern' => 'Colibri tout-vert',
                'localise' => true,
                'lat' => 47.246424,
                'long' => -0.150146,
                'valide' => false,
                'validateur' => null,
                'user' => 'user-part2'),
            array(
                'dateObserv' => new \DateTime('2016-11-11'),
                'especeVern' => 'Pluvier guignard',
                'localise' => false,
                'lat' => 49.707431,
                'long' => 4.112549,
                'valide' => true,
                'validateur' => 'user-nat',
                'user' => 'user-part1'),
            array(
                'dateObserv' => new \DateTime('2016-11-12'),
                'especeVern' => 'Becasseau de Bonaparte',
                'localise' => false,
                'lat' => 43.513502,
                'long' => 6.046143,
                'valide' => true,
                'validateur' => null,
                'user' => 'user-nat')
        );

        foreach ($listObservations as $listObservation) {
            $observation = new Observation();
            $observation->setDateObs($listObservation['dateObserv']);
            $observation->setEspeceNomVern($this->getReference($listObservation['especeVern']));
        /*    $observation->setEspeceNomVern($listObservation['especeVern']);*/
            $observation->setPhoto($photo);
            $observation->setLocalise($listObservation['localise']);
            $observation->setLat($listObservation['lat']);
            $observation->setLon($listObservation['long']);
            $observation->setValide($listObservation['valide']);
            if ($listObservation['validateur'] !== null) {
                $observation->setValidateur($this->getReference($listObservation['validateur']));
            }
            $observation->setUser($this->getReference($listObservation['user']));
            $manager->persist($observation);
        }
        $manager->flush();

    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 3;
    }


}