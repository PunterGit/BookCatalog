<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BooksRepository")
 */
class Books
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ORM\OneToMany(targetEntity="App\Entity\BookGenres", mappedBy="id_book")
     * @ORM\OneToMany(targetEntity="App\Entity\BookAuthors", mappedBy="id_book")
     */
    private $id;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getPubDate()
    {
        return $this->pub_date;
    }

    /**
     * @param mixed $pub_date
     */
    public function setPubDate($pub_date): void
    {
        $this->pub_date = $pub_date;
    }

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $pub_date;

}
