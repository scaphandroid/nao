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

class LoadObservation extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
    /*    $user = new User();
        $manager->persist($user);*/
        $espece = new EspeceNomVern('EspeceFixture');
        $manager->persist($espece);
        // On crée 4 observations qu'on va assigner à nos user

        $listObservations = array(
            array(
                'dateObserv' => new \DateTime('2016-11-20'),
                'especeVern' => $espece, // A corriger avec un getnomvern(id) qd on aura fait les repository
                'localise' => true,
                'lat' => 48.172402,
                'long' => 6.449403,
                'valide' => false,
                'user' => 'user-admin'),
            array(
                'dateObserv' => new \DateTime('2016-11-15'),
                'especeVern' => $espece,
                'localise' => true,
                'lat' => 108.172402,
                'long' => 26.449403,
                'valide' => false,
                'user' => 'user-part2'),
            array(
                'dateObserv' => new \DateTime('2016-11-11'),
                'especeVern' => $espece,
                'localise' => false,
                'lat' => 78.172402,
                'long' => 96.449403,
                'valide' => true,
                'user' => 'user-part1'),
            array(
                'dateObserv' => new \DateTime('2016-11-12'),
                'especeVern' => $espece,
                'localise' => false,
                'lat' => 118.172402,
                'long' => 106.449403,
                'valide' => true,
                'user' => 'user-nat')
        );

        foreach ($listObservations as $listObservation) {
            $observation = new Observation();
            $observation->setDateObs($listObservation['dateObserv']);
            $observation->setEspeceNomVern($listObservation['especeVern']);
            $observation->setLocalise($listObservation['localise']);
            $observation->setLat($listObservation['lat']);
            $observation->setLon($listObservation['long']);
            $observation->setValide($listObservation['valide']);
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
        return 2;
    }


}