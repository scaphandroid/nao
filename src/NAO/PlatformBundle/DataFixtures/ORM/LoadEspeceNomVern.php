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
        $listNomVern = array(
            "Epervier bicolore",
            "Autour des palombe",
            "Aigle imperial",
            "Buse pattue",
            "Élanion blanc",
            "Milan des marais",
            "Canard bride",
            "Harle huppe",
            "Colibri tout-vert",
            "Pluvier guignard",
            "Goeland pontique",
            "Becasseau de Bonaparte",
            "Pigeon bise",
            "Coucou de Madagasca",
            "Faucon crecerelle"
        );

        foreach ($listNomVern as $nomVern) {
            $espece = new EspeceNomVern($nomVern);
            $this->addReference($nomVern, $espece);
            $manager->persist($espece);
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