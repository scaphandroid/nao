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
                'nom' => 'Puchon',
                'prenom' => 'Frédéric',
                'email' => 'admin@nao.com',
                'username' => 'admin',
                'password' => 'admin',
                'profession' => 'Président NAO',
                'enabled' => true,
                'typeCompte' => 2,
                'enAttente' => false,
                'valide' => true,
                'role' => 'ROLE_SUPER_ADMIN',
                'reference' => 'user-admin',
                'cv' => '',
                'motivation' => ''),
            array( // naturaliste1
                'nom' => 'Durand',
                'prenom' => 'Jean-Paul',
                'email' => 'jpdurand@yopmail.com',
                'username' => 'naturaliste1',
                'password' => 'naturaliste1',
                'profession' => 'Biologiste',
                'enabled' => true,
                'typeCompte' => 1,
                'enAttente' => false,
                'valide' => true,
                'role' => 'ROLE_ADMIN',
                'reference' => 'user-nat',
                'cv' => 'cvNaturaliste.pdf',
                'motivation' => 'Je suis biologiste de formation. J\'aime observer les oiseaux, les photographier et me renseigner sur leurs modes de vie, leur alimentation, leurs habitudes, etc. Je souhaite obtenir un compte naturaliste pour aider au programme de recherche et ainsi me rendre utile. Merci d\'avance pour votre investissement dans la lecture de ma demande. Cordialement.'),
            array( // naturaliste2
                'nom' => 'Polochoir',
                'prenom' => 'Dominique',
                'email' => 'poldom@yopmail.com',
                'username' => 'naturaliste2',
                'password' => 'naturaliste2',
                'profession' => 'Naturopathe',
                'enabled' => true,
                'typeCompte' => 1,
                'enAttente' => false,
                'valide' => true,
                'role' => 'ROLE_ADMIN',
                'reference' => 'user-nat2',
                'cv' => 'cvNaturaliste.pdf',
                'motivation' => 'Je suis naturopathe de formation. J\'aime observer les oiseaux, les photographier et me renseigner sur leurs modes de vie, leur alimentation, leurs habitudes, etc. Je souhaite obtenir un compte naturaliste pour aider au programme de recherche et ainsi me rendre utile. Merci d\'avance pour votre investissement dans la lecture de ma demande. Cordialement.'),
            array( // particulier simple
                'nom' => '',
                'prenom' => '',
                'email' => 'particulier1@yopmail.com',
                'username' => 'particulier1',
                'password' => 'particulier1',
                'profession' => '',
                'enabled' => true,
                'typeCompte' => 0,
                'enAttente' => false,
                'valide' => true,
                'role' => 'ROLE_USER',
                'reference' => 'user-part1',
                'cv' => '',
                'motivation' => ''),
            array( // particulier simple
                'nom' => '',
                'prenom' => '',
                'email' => 'particulier2@yopmail.com',
                'username' => 'particulier2',
                'password' => 'particulier2',
                'profession' => 'chercheur au CNRS',
                'enabled' => true,
                'typeCompte' => 0,
                'enAttente' => false,
                'valide' => true,
                'role' => 'ROLE_USER',
                'reference' => 'user-part2',
                'cv' => '',
                'motivation' => ''),
            array(// particulier avec demande en attente
                'nom' => 'Dupont',
                'prenom' => 'Alain',
                'email' => 'particulier3@yopmail.com',
                'username' => 'particulier3',
                'password' => 'particulier3',
                'profession' => 'Vétérinaire',
                'enabled' => true,
                'typeCompte' => 0,
                'enAttente' => true,
                'valide' => true,
                'role' => 'ROLE_USER',
                'reference' => 'user-part3',
                'cv' => 'cvNaturaliste.pdf',
                'motivation' => 'Je suis vétérinaire de formation. J\'aime observer les oiseaux, les photographier et me renseigner sur leurs modes de vie, leur alimentation, leurs habitudes, etc. Je souhaite obtenir un compte naturaliste pour aider au programme de recherche et ainsi me rendre utile. Merci d\'avance pour votre investissement dans la lecture de ma demande. Cordialement.'),
            array(// particulier avec demande en attente
                'nom' => 'Alandron',
                'prenom' => 'Frédéric',
                'email' => 'particulier4@yopmail.com',
                'username' => 'particulier4',
                'password' => 'particulier4',
                'profession' => 'Soigneur dans un parc animalier',
                'enabled' => true,
                'typeCompte' => 0,
                'enAttente' => true,
                'valide' => true,
                'role' => 'ROLE_USER',
                'reference' => 'user-part4',
                'cv' => 'cvNaturaliste.pdf',
                'motivation' => 'Je suis soigneur de formation. J\'aime observer les oiseaux, les photographier et me renseigner sur leurs modes de vie, leur alimentation, leurs habitudes, etc. Je souhaite obtenir un compte naturaliste pour aider au programme de recherche et ainsi me rendre utile. Merci d\'avance pour votre investissement dans la lecture de ma demande. Cordialement.'),
            array(// particulier avec demande refusée
                'nom' => 'Polon',
                'prenom' => 'Gilbert',
                'email' => 'particulier5@yopmail.com',
                'username' => 'particulier5',
                'password' => 'particulier5',
                'profession' => 'sans emploi',
                'enabled' => true,
                'typeCompte' => 0,
                'enAttente' => false,
                'valide' => false,
                'role' => 'ROLE_USER',
                'reference' => 'user-part5',
                'cv' => 'cvNaturaliste.pdf',
                'motivation' => 'Je suis actuellement sans emploi. J\'aime observer les oiseaux, les photographier et me renseigner sur leurs modes de vie, leur alimentation, leurs habitudes, etc. Je souhaite obtenir un compte naturaliste pour aider au programme de recherche et ainsi me rendre utile. Merci d\'avance pour votre investissement dans la lecture de ma demande. Cordialement.'),
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
            $user->setValide($listUser['valide']);
            $user->addRole($listUser['role']);
            $this->addReference($listUser['reference'], $user);
            $user->setCv($listUser['cv']);
            $user->setMotivation($listUser['motivation']);
            $user->setEnAttente($listUser['enAttente']);
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