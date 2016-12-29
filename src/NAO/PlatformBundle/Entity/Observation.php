<?php

namespace NAO\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Observation
 *
 * @ORM\Table(name="observation")
 * @ORM\Entity(repositoryClass="NAO\PlatformBundle\Repository\ObservationRepository")
 */
class Observation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="NAO\PlatformBundle\Entity\Espece")
     * @ORM\JoinColumn(nullable=false)
     */
    private $espece;

      /**
     * @var bool
     *
     * @ORM\Column(name="localise", type="boolean")
     */
    private $localise;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull(
     *     message="Vous devez préciser une localisation."
     * )
     *
     * @Assert\Type(
     *     type="float",
     *     message="Latitude incohérente !"
     * )
     * @ORM\Column(name="lat", type="float")
     */
    private $lat;
    /**
     * @var string
     *
     * @Assert\Type(
     *     type="float",
     *     message="Latitude incohérente !"
     * )
     *
     * @ORM\Column(name="lon", type="float")
     */
    private $lon;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_obs", type="datetimetz")
     * @Assert\NotBlank()
     * @Assert\DateTime()
     * @Assert\LessThanOrEqual(
     *      "now UTC",
     *      message = "Merci de vérifier la date de l'observation, nous n'enregistrons pas les observations du futur."
     * )
     */
    private $dateObs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_saisie", type="datetimetz")
     */
    private $dateSaisie;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     *
     * @Assert\File(
     *     mimeTypes={
     *          "image/png",
     *          "image/jpeg",
     *          "image/jpg",
     *          "image/gif"
     *      },
     *     maxSize = "5M",
     *     mimeTypesMessage = "La photo doit être au format png, jpeg, jpg ou gif.",
     *     uploadErrorMessage = "Une erreur s'est produite. Merci de recommencer."
     * )
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="NAO\PlatformBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var bool
     *
     * @ORM\Column(name="valide", type="boolean")
     */
    private $valide;

    /**
     * @var bool
     *
     * @ORM\Column(name="en_attente", type="boolean")
     */
    private $enAttente;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_p", type="text", nullable=true)
     */
    private $commentaireP;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_n", type="text", nullable=true)
     */
    private $commentaireN;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="NAO\PlatformBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $validateur;


    public function __construct()
    {
        $this->dateSaisie   = new \Datetime();
        $this->dateObs = new \Datetime();
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
     * Set espece
     *
     * @param Espece $espece
     *
     * @return Observation
     */
    public function setEspece(Espece $espece)
    {
        $this->espece = $espece;
        return $this;
    }

    /**
     * Get espece
     *
     * @return \NAO\PlatformBundle\Entity\espece
     */
    public function getEspece()
    {
        return $this->espece;
    }

    /**
     * Set lat
     *
     * @param string $lat
     *
     * @return Observation
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lon
     *
     * @param string $lon
     *
     * @return Observation
     */
    public function setLon($lon)
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Get lon
     *
     * @return string
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * Set dateObs
     *
     * @param \DateTime $dateObs
     *
     * @return Observation
     */
    public function setDateObs($dateObs)
    {
        $this->dateObs = $dateObs;

        return $this;
    }

    /**
     * Get dateObs
     *
     * @return \DateTime
     */
    public function getDateObs()
    {
        return $this->dateObs;
    }

    /**
     * Set dateSaisie
     *
     * @param \DateTime $dateSaisie
     *
     * @return Observation
     */
    public function setDateSaisie($dateSaisie)
    {
        $this->dateSaisie = $dateSaisie;

        return $this;
    }

    /**
     * Get dateSaisie
     *
     * @return \DateTime
     */
    public function getDateSaisie()
    {
        return $this->dateSaisie;
    }

    /**
     * Set photo
     *
     * @param String $photo
     *
     * @return Observation
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Observation
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }


    /**

     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set valide
     *
     * @param boolean $valide
     *
     * @return Observation
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
     * Set localise
     *
     * @param boolean $localise
     *
     * @return Observation
     */
    public function setLocalise($localise)
    {
        $this->localise = $localise;
    }

    /**
     * Get localise
     *
     * @return boolean
     */
    public function getLocalise()
    {
        return $this->localise;
    }

    /**
     * Set commentaireP
     *
     * @param string $commentaireP
     *
     * @return Observation
     */
    public function setCommentaireP($commentaireP)
    {
        $this->commentaireP = $commentaireP;

        return $this;
    }

    /**
     * Get commentaireP
     *
     * @return string
     */
    public function getCommentaireP()
    {
        return $this->commentaireP;
    }

    /**
     * Set commentaireN
     *
     * @param string $commentaireN
     *
     * @return Observation
     */
    public function setCommentaireN($commentaireN)
    {
        $this->commentaireN = $commentaireN;

        return $this;
    }

    /**
     * Get commentaireN
     *
     * @return string
     */
    public function getCommentaireN()
    {
        return $this->commentaireN;
    }

    /**
     * Set validateur
     *
     * @param \NAO\PlatformBundle\Entity\User $validateur
     *
     * @return Observation
     */
    public function setValidateur(\NAO\PlatformBundle\Entity\User $validateur = null)
    {
        $this->validateur = $validateur;

        return $this;
    }

    /**
     * Get validateur
     *
     * @return \NAO\PlatformBundle\Entity\User
     */
    public function getValidateur()
    {
        return $this->validateur;
    }

    /**
     * Set enAttente
     *
     * @param boolean $enAttente
     *
     * @return Observation
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
