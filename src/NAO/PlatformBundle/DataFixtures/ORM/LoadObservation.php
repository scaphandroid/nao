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
            array( // observation par admin
                'dateObserv' => new \DateTime('2016-12-30 11:00:00'),
                'espece' => 'Aigle impérial (Aquila heliaca Savigny, 1809)',
                'localise' => true,
                'lat' => 44.806004,
                'long' => 4.420166,
                'valide' => true,
                'enAttente' => false,
                'validateur' => null,
                'user' => 'user-admin',
                'photo' => 'oiseau1.jpg',),
            array( // observation par particulier2 en attente
                'dateObserv' => new \DateTime('2016-12-31 12:00:00'),
                'espece' => 'Colibri tout-vert  (Polytmus theresiae (Da Silva Maia, 1843))',
                'localise' => true,
                'lat' => 47.246424,
                'long' => -0.150146,
                'valide' => false,
                'enAttente' => true,
                'validateur' => null,
                'user' => 'user-part2',
                'photo' => 'oiseau2.jpg',),
            array( // observation par particulier1 validée par naturaliste1
                'dateObserv' => new \DateTime('2016-12-29 14:00:00'),
                'espece' => 'Dryade à queue fourchue  (Thalurania furcata (Gmelin, 1788))',
                'localise' => false,
                'lat' => 49.707431,
                'long' => 4.112549,
                'valide' => true,
                'enAttente' => false,
                'validateur' => 'user-nat',
                'user' => 'user-part1',
                'photo' => 'oiseau3.jpg',),
            array(// observation par naturaliste1 validée
                'dateObserv' => new \DateTime('2016-12-30 02:00:00'),
                'espece' => 'Bécasseau de Bonaparte, Bécasseau à croupion blanc (Calidris fuscicollis (Vieillot, 1819))',
                'localise' => false,
                'lat' => 43.513502,
                'long' => 6.046143,
                'valide' => true,
                'enAttente' => false,
                'validateur' => null,
                'user' => 'user-nat',
                'photo' => 'oiseau4.jpg',),
            array(// observation par naturaliste2 validée
                'dateObserv' => new \DateTime('2016-12-31 12:00:00'),
                'espece' => 'Pluvier guignard (Eudromias morinellus (Linnaeus, 1758))',
                'localise' => false,
                'lat' => 45.513502,
                'long' => 6.046143,
                'valide' => true,
                'enAttente' => false,
                'validateur' => null,
                'user' => 'user-nat2',
                'photo' => 'oiseau1.jpg',),
            array(// observation par naturaliste1 validée
                'dateObserv' => new \DateTime('2016-12-29 04:00:00'),
                'espece' => 'Vanneau à queue blanche (Chettusia leucura (Lichtenstein, 1823))',
                'localise' => false,
                'lat' => 45.513502,
                'long' => 3.046143,
                'valide' => true,
                'enAttente' => false,
                'validateur' => null,
                'user' => 'user-nat',
                'photo' => 'oiseau4.jpg',),
            array(// observation par particulier1 refusée par nat1
                'dateObserv' => new \DateTime('2016-12-31 04:00:00'),
                'espece' => 'Goéland brun (Larus fuscus Linnaeus, 1758)',
                'localise' => false,
                'lat' => 41.513502,
                'long' => 3.046143,
                'valide' => false,
                'enAttente' => false,
                'validateur' => 'user-nat',
                'user' => 'user-part1',
                'photo' => 'oiseau4.jpg',),
            array( // observation par particulier1 validée par nat1
                'dateObserv' => new \DateTime('2016-12-30 04:00:00'),
                'espece' => 'Mouette ivoire, Goéland sénateur, Mouette blanche (Larus eburneus Phipps, 1774)',
                'localise' => false,
                'lat' => 47.854454,
                'long' => -0.692139,
                'valide' => true,
                'enAttente' => false,
                'validateur' => 'user-nat',
                'user' => 'user-part1',
                'photo' => 'oiseau4.jpg',),
            array( // observation par particulier1 validée par nat2
                'dateObserv' => new \DateTime('2016-12-29 14:00:00'),
                'espece' => 'Bécasseau minuscule (Erolia minutilla (Vieillot, 1819))',
                'localise' => false,
                'lat' => 46.854454,
                'long' => -0.692139,
                'valide' => true,
                'enAttente' => false,
                'validateur' => 'user-nat2',
                'user' => 'user-part1',
                'photo' => 'oiseau3.jpg',),
            array( // observation par particulier1 en attente
                'dateObserv' => new \DateTime('2016-12-30 06:00:00'),
                'espece' => 'Limosa limosa islandica Brehm, 1831',
                'localise' => false,
                'lat' => 48.114583,
                'long' => 5.123062,
                'valide' => false,
                'enAttente' => true,
                'validateur' => null,
                'user' => 'user-part1',
                'photo' => 'oiseau2.jpg',)
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