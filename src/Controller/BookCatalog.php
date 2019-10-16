<?php


namespace App\Controller;

use App\Entity\Authors;
use App\Entity\BookGenres;
use App\Entity\BookAuthors;
use App\Entity\Genres;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BookCatalog extends AbstractController
{
    public function fAll($class)// Выбор всех полей переданной таблицы
    {
        $query = $this->getDoctrine()
            ->getRepository($class)
            ->findAll();

        return $query;
    }
    /**
     * @Route("/")
     */
    public function number()
    {
        $books=[];
        $booksAuthorsId=[];
        $booksGenresId=[];
        $booksTitles=[];
        $booksPubDates=[];
        $booksAuthors=[];
        $booksGenres=[];
        $booksId = [];
        $booksQuery = $this->getDoctrine()->getRepository('App:Books')
            ->findBy([], ['id' => 'DESC']);//получение отсортированной по убыванию данных таблицы Books

        foreach ($booksQuery as $key => $value){ //запись в переменные данных из таблицы Books
            $booksAuthorsId+=[$value->getId() =>[]];
            $booksGenresId+=[$value->getId() =>[]];
            $booksTitles+=[$value->getId() => $value->getTitle()];
            $booksPubDates+=[$value->getId() => $value->getPubDate()];
            array_push($booksId,$value->getId());
        }

        $booksAuthorsQuery = $this -> fAll(Authors::class); //получение данных таблицы Authors
        if(!$booksAuthorsQuery){
            return $this->render('base.html.twig', array(//передача массива books шаблону base
                'Books' => NULL,
                'Ids' => NULL,
            ));
        }
        foreach ($booksAuthorsQuery as $key => $value){//запись в переменные данных из табллицы Authors
            $booksAuthors+=[$value->getId()=>$value->getName()];
        }

        $booksGenresQuery = $this -> fAll(Genres::class);//получение данных таблицы Genres

        foreach ($booksGenresQuery as $key => $value){//запись в переменные данных из таблицы Genres
            $booksGenres+=[$value->getId()=>$value->getName()];
        }

        $booksAuthorsIdQuery = $this -> fAll(BookAuthors::class);//получение данных таблицы BookAuthors

        foreach ($booksAuthorsIdQuery as $key => $value){//запись данных из таблицы BookAuthors в двумерный массив $booksAuthorsId для соотношения нескольких авторов к 1 книге
            array_push($booksAuthorsId[$value->getIdBook()->getId()],$booksAuthors[$value->getIdAuthor()->getId()]);
        }

        $booksGenresIdQuery = $this -> fAll(BookGenres::class);//получение данных таблицы BookGenres


        foreach ($booksGenresIdQuery as $key => $value){//запись данных из таблицы BookGenres в двумерный массив $booksGenresId для соотношения нескольких жанров к 1 книге
            array_push($booksGenresId[$value->getIdBook()->getId()],$booksGenres[$value->getIdGenre()->getId()]);
        }


        foreach ($booksTitles as $key => $value){// запись в массив books всех полученных данных
            $books+=[$value => [
                'PubDate'=>$booksPubDates[$key],
                'Genres'=>$booksGenresId[$key],
                'Authors'=>$booksAuthorsId[$key]
            ]];
        }
        //return new Response(var_dump($books));
        return $this->render('base.html.twig', array(//передача массива books шаблону base
            'Books' => $books,
            'Ids' => $booksId,
        ));
    }

}