<?php

namespace NAO\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use NAO\PlatformBundle\Entity\Observation;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadObservation extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        // On crée 5 observations qu'on va assigner à nos user
        $listObservations = array(
            array(
                'dateObserv' => new \DateTime('2016-12-16 11:00:00'),
                'espece' => 'Aigle impérial',
                'localise' => true,
                'lat' => 44.806004,
                'long' => 4.420166,
                'valide' => true,
                'enAttente' => false,
                'validateur' => null,
                'user' => 'user-admin',
                'photo' => 'oiseau1.jpg',),
            array(
                'dateObserv' => new \DateTime('2016-12-15 12:00:00'),
                'espece' => 'Colibri tout-vert ',
                'localise' => true,
                'lat' => 47.246424,
                'long' => -0.150146,
                'valide' => false,
                'enAttente' => true,
                'validateur' => null,
                'user' => 'user-part2',
                'photo' => 'oiseau2.jpg',),
            array(
                'dateObserv' => new \DateTime('2016-12-11 14:00:00'),
                'espece' => 'Pluvier guignard',
                'localise' => false,
                'lat' => 49.707431,
                'long' => 4.112549,
                'valide' => true,
                'enAttente' => false,
                'validateur' => 'user-nat',
                'user' => 'user-part1',
                'photo' => 'oiseau3.jpg',),
            array(
                'dateObserv' => new \DateTime('2016-12-13 02:00:00'),
                'espece' => 'Bécasseau de Bonaparte, Bécasseau à croupion blanc',
                'localise' => false,
                'lat' => 43.513502,
                'long' => 6.046143,
                'valide' => true,
                'enAttente' => false,
                'validateur' => null,
                'user' => 'user-nat',
                'photo' => 'oiseau4.jpg',),
            array(
                'dateObserv' => new \DateTime('2016-12-12 04:00:00'),
                'espece' => 'Bécasseau de Bonaparte, Bécasseau à croupion blanc',
                'localise' => false,
                'lat' => 45.513502,
                'long' => 3.046143,
                'valide' => true,
                'enAttente' => false,
                'validateur' => null,
                'user' => 'user-nat',
                'photo' => 'oiseau4.jpg',),
            array(
                'dateObserv' => new \DateTime('2016-12-01 04:00:00'),
                'espece' => 'Pigeon biset',
                'localise' => false,
                'lat' => 41.513502,
                'long' => 3.046143,
                'valide' => false,
                'enAttente' => false,
                'validateur' => 'user-nat',
                'user' => 'user-part1',
                'photo' => 'oiseau4.jpg',),
            array(
                'dateObserv' => new \DateTime('2016-12-09 04:00:00'),
                'espece' => 'Pigeon biset',
                'localise' => false,
                'lat' => 41.513502,
                'long' => 3.046143,
                'valide' => false,
                'enAttente' => false,
                'validateur' => 'user-nat',
                'user' => 'user-part1',
                'photo' => 'oiseau4.jpg',)
        );

        foreach ($listObservations as $listObservation) {
            $observation = new Observation();
            $observation->setDateObs($listObservation['dateObserv']);
            $observation->setEspece($this->getReference($listObservation['espece']));
            $observation->setPhoto($listObservation['photo']);
            $observation->setLocalise($listObservation['localise']);
            $observation->setLat($listObservation['lat']);
            $observation->setLon($listObservation['long']);
            $observation->setValide($listObservation['valide']);
            $observation->setEnAttente($listObservation['enAttente']);
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