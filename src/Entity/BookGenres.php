<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookGenresRepository")
 */
class BookGenres
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Genres", inversedBy="Id")
     * @ORM\JoinColumn(nullable=true)
     */
    private $id_genre;

    public function getIdGenre()
    {
        return $this->id_genre;
    }

    /**
     * @param mixed $idGenre
     */
    public function setIdGenre($idGenre)
    {
        $this->id_genre = $idGenre;
    }

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Books", inversedBy="Id")
     * @ORM\JoinColumn(nullable=true)
     */
    private $id_book;

    /**
     * @param mixed $idBook
     */
    public function setIdBook($idBook)
    {
        $this->id_book = $idBook;
    }

    public function getIdBook()
    {
        return $this->id_book;
    }

}
