<?php

namespace NAO\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use NAO\PlatformBundle\Entity\User;

class LoadUser extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $listUsers = array(
            array(
                'nom' => 'admin',
                'prenom' => 'admin',
                'email' => 'admin@nao.com',
                'username' => 'admin',
                'password' => 'admin',
                'profession' => 'naturaliste',
                'enabled' => true,
                'typeCompte' => 2,
                'enAttente' => false,
                'role' => 'ROLE_SUPER_ADMIN',
                'reference' => 'user-admin'),
            array(
                'nom' => 'naturaliste',
                'prenom' => 'naturaliste',
                'email' => 'naturaliste@nao.com',
                'username' => 'naturaliste',
                'password' => 'naturaliste',
                'profession' => 'Biologiste',
                'enabled' => true,
                'typeCompte' => 1,
                'enAttente' => false,
                'role' => 'ROLE_ADMIN',
                'reference' => 'user-nat'),
            array(
                'nom' => 'particulier',
                'prenom' => 'particulier',
                'email' => 'particulier1@nao.com',
                'username' => 'particulier1',
                'password' => 'particulier1',
                'profession' => 'medecin',
                'enabled' => true,
                'typeCompte' => 0,
                'enAttente' => false,
                'role' => 'ROLE_USER',
                'reference' => 'user-part1'),
            array(
                'nom' => 'particulier',
                'prenom' => 'particulier',
                'email' => 'particulier2@nao.com',
                'username' => 'particulier2',
                'password' => 'particulier2',
                'profession' => 'chercheur',
                'enabled' => true,
                'typeCompte' => 0,
                'enAttente' => false,
                'role' => 'ROLE_USER',
                'reference' => 'user-part2'),
            array(
                'nom' => 'particulierendevenir',
                'prenom' => 'particulier',
                'email' => 'particulier3@nao.com',
                'username' => 'particulier3',
                'password' => 'particulier3',
                'profession' => 'chercheur bis',
                'enabled' => false,
                'typeCompte' => 1,
                'enAttente' => false,
                'role' => 'ROLE_USER',
                'reference' => 'user-part3')
        );

        foreach ($listUsers as $listUser) {
            $user = $userManager->createUser();
            $user->setNom($listUser['nom']);
            $user->setPrenom($listUser['prenom']);
            $user->setEmail($listUser['email']);
            $user->setUserName($listUser['username']);
            $user->setPlainPassword($listUser['password']);
            $user->setProfession($listUser['profession']);
            $user->setEnabled($listUser['enabled']);
            $user->setTypeCompte($listUser['typeCompte']);
            $user->setEnAttente($listUser['enAttente']);
            $user->addRole($listUser['role']);
            $this->addReference($listUser['reference'], $user);
            $userManager->updateUser($user);
        }

    }

    /**
     * Sets the container.
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 1;
    }
}