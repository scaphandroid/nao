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
     * @ORM\Column(name="nomVern", type="string", length=255, unique=true)
     */
    private $nomVern;


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
}

