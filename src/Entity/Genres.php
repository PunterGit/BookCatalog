<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GenresRepository")
 */
class Genres
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ORM\OneToMany(targetEntity="App\Entity\BookGenres", mappedBy="Genres")
     */
    private $id;

    public function getId()
    {
        return $this->id;
    }
/*    public function __construct() {
        $this->id = new ArrayCollection();
    }*/

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

}
