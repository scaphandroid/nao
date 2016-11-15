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
     *
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
     *
     * @ORM\Column(name="lat", type="float")
     */
    private $lat;

    /**
     * @var string
     *
     * @ORM\Column(name="lon", type="float")
     */
    private $lon;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_obs", type="datetimetz")
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
     * @ORM\Column(name="auteur", type="string")
     */
    /* particullier ou naturaliste*/
    private $auteur;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * * @Assert\Email(
     *     message = "L'email '{{ value }}' n'est pas valide."
     * )
     */
    private $email;
    

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="NAO\PlatformBundle\Entity\Naturaliste")
     * @ORM\JoinColumn(nullable=true)
     */
    private $naturaliste;

    /**
     * @var bool
     *
     * @ORM\Column(name="valide", type="boolean")
     */
    private $valide;

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
     * @return string
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
     * @param File $photo
     *
     * @return Observation
     */
    public function setPhoto(File $photo)
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
     * Set auteur
     *
     * @param string $auteur
     *
     * @return Observation
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return string
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set naturaliste
     *
     * @param Naturaliste $naturaliste
     *
     * @return Observation
     */
    public function setNaturaliste(Naturaliste $naturaliste)
    {
        $this->naturaliste = $naturaliste;

        return $this;
    }

    /**
     * Get naturaliste
     *
     * @return int
     */
    public function getNaturaliste()
    {
        return $this->naturaliste;
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
     * Set email
     *
     * @param string $email
     *
     * @return Observation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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

        return $this;
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
}
