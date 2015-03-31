<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * torrent
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Torrent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="magnet", type="string", length=255)
     */
    private $magnet;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=255)
     */
    private $hash;

    /**
     * @var integer
     *
     * @ORM\Column(name="seeders", type="integer")
     */
    private $seeders;

    /**
     * @var integer
     *
     * @ORM\Column(name="leechers", type="integer")
     */
    private $leechers;

    /**
     * @var array
     *
     * @ORM\Column(name="quality", type="string", length=255)
     */
    private $quality;
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Film", inversedBy="torrents")
     * @ORM\JoinColumn(name="film_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $film;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return torrent
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set magnet
     *
     * @param string $magnet
     * @return torrent
     */
    public function setMagnet($magnet)
    {
        $this->magnet = $magnet;

        return $this;
    }

    /**
     * Get magnet
     *
     * @return string 
     */
    public function getMagnet()
    {
        return $this->magnet;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return torrent
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set seeders
     *
     * @param integer $seeders
     * @return torrent
     */
    public function setSeeders($seeders)
    {
        $this->seeders = $seeders;

        return $this;
    }

    /**
     * Get seeders
     *
     * @return integer 
     */
    public function getSeeders()
    {
        return $this->seeders;
    }

    /**
     * Set leechers
     *
     * @param integer $leechers
     * @return torrent
     */
    public function setLeechers($leechers)
    {
        $this->leechers = $leechers;

        return $this;
    }

    /**
     * Get leechers
     *
     * @return integer 
     */
    public function getLeechers()
    {
        return $this->leechers;
    }

    /**
     * Set quality
     *
     * @param array $quality
     * @return torrent
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Get quality
     *
     * @return array 
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Set film
     *
     * @param \AppBundle\Entity\Film $film
     * @return Torrent
     */
    public function setFilm(\AppBundle\Entity\Film $film = null)
    {
        $this->film = $film;

        return $this;
    }

    /**
     * Get film
     *
     * @return \AppBundle\Entity\Film 
     */
    public function getFilm()
    {
        return $this->film;
    }
}
