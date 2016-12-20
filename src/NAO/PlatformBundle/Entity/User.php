<?php

namespace NAO\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Cet email est déjà utilisé."
 * )
 * @UniqueEntity(
 *     fields={"username"},
 *     message="Ce nom d'utilisateur est déjà utilisé."
 * )
 * @ORM\Entity(repositoryClass="NAO\PlatformBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255, nullable=true)
     * @Assert\Length(
     *     min=2,
     *     minMessage = "Votre prénom doit contenir au moins {{ limit }} caractères",
     *     )
     */
    protected $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     * @Assert\Length(
     *     min=2,
     *     minMessage = "Votre nom doit contenir au moins {{ limit }} caractères",
     *     )
     */
    protected $nom;
    /**
     * @var string
     *
     * @ORM\Column(name="profession", type="string", length=255, nullable=true)
     * @Assert\Length(
     *     min=2,
     *     minMessage = "Votre profession doit contenir au moins {{ limit }} caractères",
     *     )
     */

    protected $profession;

    /**
     * @var string
     *
     * @ORM\Column(name="cv", type="string", length=255, nullable=true)
     *
     * @Assert\File(
     *     mimeTypes={ "application/pdf" },
     *     maxSize = "3M",
     *     mimeTypesMessage = "Votre CV doit être au format PDF.",
     *     uploadErrorMessage = "Une erreur s'est produite. Merci de recommencer."
     *     )
     */
    protected $cv;

    /**
     * @var string
     *
     * @ORM\Column(name="motivation", type="text", nullable=true)
     * @Assert\Length(
     *      min = 200,
     *      minMessage = "Votre texte de motivation doit être plus long."
     * )
     */
    protected $motivation;

    /**
     * @var bool
     *
     * @ORM\Column(name="valide", type="boolean")
     */
    protected $valide;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_inscription", type="datetimetz")
     */
    protected $dateInscription;

    /**
     * @var int
     *
     * @ORM\Column(name="type_compte", type="integer")
     */
    protected $typeCompte; //0: particulier 1:naturaliste 2:admin

    /**
     * @var bool
     *
     * @ORM\Column(name="en_attente", type="boolean")
     */
    protected $enAttente;


    public function __construct()
    {
        parent::__construct();
        $this->dateInscription   = new \Datetime();
        $this->setValide(false);
        $this->roles = array('ROLE_USER');
    }
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return User
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return User
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }


    /**
     * Set profession
     *
     * @param string $profession
     *
     * @return User
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * Get profession
     *
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * Set cv
     *
     * @param String $cv
     *
     * @return User
     */
    public function setCv($cv)
    {
        $this->cv = $cv;

        return $this;
    }

    /**
     * Get cv
     *
     * @return string
     */
    public function getCv()
    {
        return $this->cv;
    }

    /**
     * Set motivation
     *
     * @param string $motivation
     *
     * @return User
     */
    public function setMotivation($motivation)
    {
        $this->motivation = $motivation;

        return $this;
    }

    /**
     * Get motivation
     *
     * @return string
     */
    public function getMotivation()
    {
        return $this->motivation;
    }

    /**
     * Set valide
     *
     * @param boolean $valide
     *
     * @return User
     */
    public function setValide($valide)
    {
        $this->valide = $valide;

        return $this;
    }

    /**
     * Get valide
     *
     * @return bool
     */
    public function getValide()
    {
        return $this->valide;
    }


    /**
     * Set dateInscription
     *
     * @param \DateTime $dateInscription
     *
     * @return User
     */
    public function setDateInscription($dateInscription)
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    /**
     * Get dateInscription
     *
     * @return \DateTime
     */
    public function getDateInscription()
    {
        return $this->dateInscription;
    }
   


    /**
     * Set typeCompte
     *
     * @param integer $typeCompte
     *
     * @return User
     */
    public function setTypeCompte($typeCompte)
    {
        $this->typeCompte = $typeCompte;

        return $this;
    }

    /**
     * Get typeCompte
     *
     * @return integer
     */
    public function getTypeCompte()
    {
        return $this->typeCompte;
    }

    /**
     * Set enAttente
     *
     * @param boolean $enAttente
     *
     * @return User
     */
    public function setEnAttente($enAttente)
    {
        $this->enAttente = $enAttente;

        return $this;
    }

    /**
     * Get enAttente
     *
     * @return boolean
     */
    public function getEnAttente()
    {
        return $this->enAttente;
    }
}
