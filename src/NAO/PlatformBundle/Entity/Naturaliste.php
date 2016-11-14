<?php

namespace NAO\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Naturaliste
 *
 * @ORM\Table(name="naturaliste")
 * @ORM\Entity(repositoryClass="NAO\PlatformBundle\Repository\NaturalisteRepository")
 */
class Naturaliste
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
     * @ORM\Column(name="prenom", type="string", length=255)
     * @Assert\Length(
     *     min=2,
     *     minMessage = "Votre prénom doit contenir au moins {{ limit }} caractères",
     *     )
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Assert\Length(
     *     min=2,
     *     minMessage = "Votre nom doit contenir au moins {{ limit }} caractères",
     *     )
     */
    private $nom;

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
     * @var string
     *
     * @ORM\Column(name="profession", type="string", length=255)
     * @Assert\Length(
     *     min=2,
     *     minMessage = "Votre profession doit contenir au moins {{ limit }} caractères",
     *     )
     */
    private $profession;

    /**
     * @var string
     *
     * @ORM\Column(name="cv", type="string", length=255)
     *
     * @Assert\NotBlank(message="Votre CV est obligatoire.")
     * @Assert\File(
     *     mimeTypes={ "application/pdf" },
     *     maxSize = "3M",
     *     mimeTypesMessage = "Votre CV doit être au format PDF.",
     *     uploadErrorMessage = "Une erreur s'est produite. Merci de recommencer."
     *     )
     */
    private $cv;

    /**
     * @var string
     *
     * @ORM\Column(name="motivation", type="text")
     * @Assert\Length(
     *      min = 200,
     *      minMessage = "Votre texte de motivation doit être plus long."
     * )
     */
    private $motivation;

    /**
     * @var bool
     *
     * @ORM\Column(name="valide", type="boolean")
     */
    private $valide;

/* A voir si l'on garde avec le système de gestion des user*/
    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;


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
     * @return Naturaliste
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
     * @return Naturaliste
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
     * Set email
     *
     * @param string $email
     *
     * @return Naturaliste
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
     * Set profession
     *
     * @param string $profession
     *
     * @return Naturaliste
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
     * @param File $cv
     *
     * @return Naturaliste
     */
    public function setCv(File $cv)
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
     * @return Naturaliste
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
     * @return Naturaliste
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
     * Set password
     *
     * @param string $password
     *
     * @return Naturaliste
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}

