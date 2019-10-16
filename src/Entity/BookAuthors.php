<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookAuthorsRepository")
 */
class BookAuthors
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Authors", inversedBy="Id")
     * @ORM\JoinColumn(nullable=true)
     */
    private $id_author;

    public function getIdAuthor()
    {
        return $this->id_author;
    }

    /**
     * @param mixed $idBook
     */
    public function setIdBook($idBook)
    {
        $this->id_book = $idBook;
    }
    /*   public function __construct() {
        $this->id_author = new ArrayCollection();
        $this->id_book = new ArrayCollection();
    }*/
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Books", inversedBy="Id")
     * @ORM\JoinColumn(nullable=true)
     */
    private $id_book;

    public function getIdBook()
    {
        return $this->id_book;
    }

    /**
     * @param mixed $idAuthor
     */
    public function setIdAuthor($idAuthor)
    {
        $this->id_author = $idAuthor;
    }


}
