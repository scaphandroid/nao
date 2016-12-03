<?php

namespace NAO\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EspeceNomVern
 *
 * @ORM\Table(name="espece_nom_vern")
 * @ORM\Entity(repositoryClass="NAO\PlatformBundle\Repository\EspeceNomVernRepository")
 */
class EspeceNomVern
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
     * @ORM\Column(name="nomVern", type="string", length=255, unique=false, nullable = true)
     */
    private $nomVern;

    /**
     * @var string
     *
     * @ORM\Column(name="nomLatin", type="string", length=255, unique=false, nullable=true)
     */
    private $nomLatin;

    /**
     * @var string
     *
     * @ORM\Column(name="nomConcat", type="string", length=255, unique=false)
     */
    private $nomConcat;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    public function __construct($nomVern = null) {
        $this->nomVern = $nomVern;
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
     * Set nomVern
     *
     * @param string $nomVern
     *
     * @return EspeceNomVern
     */
    public function setNomVern($nomVern)
    {
        $this->nomVern = $nomVern;

        return $this;
    }

    /**
     * Get nomVern
     *
     * @return string
     */
    public function getNomVern()
    {
        return $this->nomVern;
    }

    /**
     * Set nomLatin
     *
     * @param string $nomLatin
     *
     * @return EspeceNomVern
     */
    public function setNomLatin($nomLatin)
    {
        $this->nomLatin = $nomLatin;

        return $this;
    }

    /**
     * Get nomLatin
     *
     * @return string
     */
    public function getNomLatin()
    {
        return $this->nomLatin;
    }

    /**
     * Set nomConcat
     *
     * @param string $nomConcat
     *
     * @return EspeceNomVern
     */
    public function setNomConcat($nomConcat)
    {
        $this->nomConcat = $nomConcat;

        return $this;
    }

    /**
     * Get nomConcat
     *
     * @return string
     */
    public function getNomConcat()
    {
        return $this->nomConcat;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return EspeceNomVern
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
