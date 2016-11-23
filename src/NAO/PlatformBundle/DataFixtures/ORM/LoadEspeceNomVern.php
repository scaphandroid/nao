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
            "Épervier bicolore",
            "Autour des palombe",
            "Aigle impérial",
            "Buse pattue",
            "Élanion blanc",
            "Milan des marais",
            "Canard bridé",
            "Harle huppé",
            "Colibri tout-vert",
            "Pluvier guignard",
            "Goéland pontique",
            "Bécasseau de Bonaparte",
            "Pigeon bise",
            "Coucou de Madagasca",
            "Faucon crécerelle"
        );

        foreach ($listNomVern as $nomVern) {
            $espece = new EspeceNomVern();
            $espece->setNomVern($nomVern);
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
        return 3;
    }
}