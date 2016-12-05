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
     * @ORM\Column(name="cd_nom", type="bigint", unique=true, nullable=false)
     */
    private $cd_nom;

    /**
     * @var string
     *
     * @ORM\Column(name="nomVern", type="string", length=255, unique=false, nullable = true)
     */
    private $nomVern;

    /**
     * @var string
     *
     * @ORM\Column(name="nomComplet", type="string", length=255, unique=true, nullable=false)
     */
    private $nomComplet;

    /**
     * @var string
     *
     * @ORM\Column(name="nomConcat", type="string", length=255, unique=true, nullable=false)
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

    /**
     * Set cdNom
     *
     * @param integer $cdNom
     *
     * @return EspeceNomVern
     */
    public function setCdNom($cdNom)
    {
        $this->cd_nom = $cdNom;

        return $this;
    }

    /**
     * Get cdNom
     *
     * @return integer
     */
    public function getCdNom()
    {
        return $this->cd_nom;
    }

    /**
     * Set nomComplet
     *
     * @param string $nomComplet
     *
     * @return EspeceNomVern
     */
    public function setNomComplet($nomComplet)
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    /**
     * Get nomComplet
     *
     * @return string
     */
    public function getNomComplet()
    {
        return $this->nomComplet;
    }
}
