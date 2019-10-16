<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use App\Entity\Books;
use App\Entity\Authors;
use App\Entity\Genres;
use App\Entity\BookAuthors;
use App\Entity\BookGenres;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataController extends AbstractFOSRestController
{

    /**
    * @Rest\Get("/genre/list/")
    */
    public function getJSList()
    {
        $queryGenres = $this->getDoctrine()->getRepository('App:Genres')->findAll();
        $queryAuthors = $this->getDoctrine()->getRepository('App:Authors')->findAll();
        $genres = [];
        $authors = [];
        $restresult = [];
        foreach ($queryGenres as $key => $value){
            array_push($genres,$value->getName());
        }
        foreach ($queryAuthors as $key => $value){
            array_push($authors,$value->getName());
        }
        array_push($restresult,$genres);
        array_push($restresult,$authors);
        if (($restresult === null)) {
            return new View("there are no Genres or Authors exist", Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($restresult);
    }
    /**
    * @Rest\Get("/book")
    */
    public function getAction()
    {
        $restresult = $this->getDoctrine()->getRepository('App:Books')->findAll();
        if ($restresult === null) {
            return new View("there are no books exist", Response::HTTP_NOT_FOUND);
        }
        return $restresult;
    }

    /**
     * @Rest\Post("/book/")
     * @param Request $request
     * @return Response
     */
    public function postAction(Request $request)
    {
        $books = new Books;
        $requestData = json_decode($request->request->all()['dataPost'],true);

        $title = $requestData['title'];
        $genre = $requestData['genres'];
        $author = $requestData['authors'];
        $pubDate = $requestData['pubDate'];

        $idAuthors = [];
        $idGenres = [];
        if(empty($title) || empty($genre) || empty($author) || empty($pubDate)) {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }else {
            $em = $this->getDoctrine()->getManager();
            $books->setTitle($title); // добавление новой книги в таблицу books
            $books->setPubDate($pubDate);
            $em->persist($books);
            $em->flush();
            foreach ($author as $key => $value){// добавление новых авторов в таблицу authors и создание массиова с id авторов добавляемой книги
                $authors = new Authors;
                $idAuthor = NULL;
                $idAuthor = $em->getRepository(Authors::class)->findOneBy(['name'=>$value]);
                if (empty($idAuthor)){
                    $authors->setName($value);
                    $em->persist($authors);
                    $em->flush();
                    array_push($idAuthors,$authors->getId());
                }else{
                    array_push($idAuthors,$idAuthor->getId());
                }
            }
            foreach ($genre as $key => $value){// добавление новых жанров в таблицу Genres и создание массиова с id жанров добавляемой книги
                $genres = new Genres;
                $idGenre = NULL;
                $idGenre = $em->getRepository(Genres::class)->findOneBy(['name'=>$value]);
                if (empty($idGenre)){
                    $genres->setName($value);
                    $em->persist($genres);
                    $em->flush();
                    array_push($idGenres,$genres->getId());
                }else{
                    array_push($idGenres,$idGenre->getId());
                }
            }
            foreach ($idAuthors as $key => $value){
                $bookAuthors = new BookAuthors;
                $bookAuthors->setIdAuthor($em->getReference(Authors::class,$value));
                $bookAuthors->setIdBook($em->getReference(Books::class,$books->getId()));
                $em->persist($bookAuthors);
                $em->flush();
                //$queryStr = 'INSERT INTO book_authors (id_author_id, id_book_id) VALUES ('.$value.','.$books->getId().')';
                //$em->getConnection()->executeUpdate($queryStr);
            }
            foreach ($idGenres as $key => $value) {
                $bookGenres = new BookGenres;
                $bookGenres->setIdGenre($em->getReference(Genres::class,$value));
                $bookGenres->setIdBook($em->getReference(Books::class,$books->getId()));
                $em->persist($bookGenres);
                $em->flush();
            }
            return new Response($books->getId(), 200);
        }
    }
    /**
     * @Rest\Put("/book/")
     * @param Request $request
     * @return Response
     */
    public function updateAction(Request $request)
    {
        $requestData = json_decode($request->request->all()['dataPut'],true);

        $id = $requestData['id'];
        $title = $requestData['title'];
        $genre = $requestData['genres'];
        $author = $requestData['authors'];
        $pubDate = $requestData['pubDate'];

        if(empty($title) || empty($genre) || empty($author) || empty($pubDate) || empty($id))
        {
            return new Response('Empty data', Response::HTTP_NOT_ACCEPTABLE);
        }else {
            $current_book = $this->getDoctrine()->getRepository('App:Books')->find($id);
            $currentAuthors = $this->getDoctrine()->getRepository('App:BookAuthors')->findBy(["id_book"=>$id]);
            $currentGenres = $this->getDoctrine()->getRepository('App:BookGenres')->findBy(["id_book"=>$id]);
            $currentAuthorsNames = [];
            $currentGenresNames = [];
            $em = $this->getDoctrine()->getManager();
            foreach ($title as $key => $value){
                if($key != $value){
                    $current_book->setTitle($value);
                }
            }
            foreach ($pubDate as $key => $value){
                if($key != $value){
                    $current_book->setPubDate($value);
                }
            }
            $em->persist($current_book);
            $em->flush();

            foreach ($currentAuthors as $key => $value){
                array_push($currentAuthorsNames,$value->getIdAuthor()->getName());
            }
            foreach ($currentGenres as $key => $value){
                array_push($currentGenresNames,$value->getIdGenre()->getName());
            }
            foreach ($author as $key => $value){// изменение/добавление/удаление авторов книги
                if($value === 'DELETE'){//Удаление авторов, которые были до обновления с ней связаны
                    $em->remove($em->getRepository(BookAuthors::class)->findOneBy([
                        'id_author'=>($em->getRepository('App:Authors')->findOneBy(['name'=>$key])->getId()),
                        'id_book'=>($em->getRepository('App:Books')->find($id))
                    ]));
                    $em->flush();
                    if(count($em->getRepository(BookAuthors::class)->findBy(['id_author'=>($em->getRepository('App:Authors')->findBy(['name'=>$key]))]))==0){
                        $em->remove($this->getDoctrine()->getRepository('App:Authors')->findOneBy(['name'=>$key]));
                        $em->flush();
                    }
                }else{
                    if ($value == 'new') {
                        $authors = $em->getRepository(Authors::class)->findOneBy(['name'=>$key]);
                        if($authors === NULL){
                            $authors = new Authors;
                            $authors->setName($key);
                            $em->persist($authors);
                            $em->flush();
                        }
                        $bookAuthors = new BookAuthors;
                        $bookAuthors->setIdAuthor($em->getReference(Authors::class, $authors->getId()));
                        $bookAuthors->setIdBook($em->getReference(Books::class, $id));
                        $em->persist($bookAuthors);
                        $em->flush();
                    }elseif ($key!==$value){
                        $name_change = $em->getRepository(Authors::class)->findOneBy(['name'=>$key]);
                        $name_change->setName($value);
                        $em->persist($name_change);
                        $em->flush();
                    }
                }
            }
            foreach ($genre as $key => $value){// изменение/добавление/удаление жанров книги
                if($value === 'DELETE'){//Удаление жанров, которые были до обновления с ней связаны
                    $em->remove($em->getRepository(BookGenres::class)->findOneBy([
                        'id_genre'=>($em->getRepository('App:Genres')->findOneBy(['name'=>$key])->getId()),
                        'id_book'=>($em->getRepository('App:Books')->find($id))
                    ]));
                    $em->flush();
                    if(count($em->getRepository(BookGenres::class)->findBy(['id_genre'=>($em->getRepository('App:Genres')->findBy(['name'=>$key]))]))==0){
                        $em->remove($this->getDoctrine()->getRepository('App:Genres')->findOneBy(['name'=>$key]));
                        $em->flush();
                    }
                }else{
                    if ($value == 'new') {
                        $genres = $em->getRepository(Genres::class)->findOneBy(['name'=>$key]);
                        if($genres === NULL){
                            $genres = new Genres();
                            $genres->setName($key);
                            $em->persist($genres);
                            $em->flush();
                        }
                        $bookGenres = new BookGenres();
                        $bookGenres->setIdGenre($em->getReference(Genres::class, $genres->getId()));
                        $bookGenres->setIdBook($em->getReference(Books::class, $id));
                        $em->persist($bookGenres);
                        $em->flush();
                    }elseif ($key!==$value){
                        $name_change = $em->getRepository(Genres::class)->findOneBy(['name'=>$key]);
                        $name_change->setName($value);
                        $em->persist($name_change);
                        $em->flush();
                    }
                }
            }
            return new Response("Книга успешно изменена", 200);
        }
    }
    /**
     * @Rest\Delete("/book/{id}")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $this->getDoctrine()->getRepository('App:Books')->find($id);
        if (empty($book)) {
            return new View("Книга не найдена", Response::HTTP_NOT_FOUND);
        }
        else {
            $idGenres=[];
            $idAuthors=[];
            $currentAuthors = $this->getDoctrine()->getRepository('App:BookAuthors')->findBy(["id_book"=>$book->getId()]);
            $currentGenres = $this->getDoctrine()->getRepository('App:BookGenres')->findBy(["id_book"=>$book->getId()]);
            //удаление связей между книгой, автором и жанром
            foreach ($currentGenres as $key => $value){
                array_push($idGenres,$value->getIdGenre()->getId());
                $em->remove($value);
                $em->flush();
            }
            foreach ($currentAuthors as $key => $value){
                array_push($idAuthors,$value->getIdAuthor()->getId());
                $em->remove($value);
                $em->flush();
            }
            //удаление авторов и жанров, у которых нет книг
            foreach ($idAuthors as $value) {
                if(count($this->getDoctrine()->getRepository('App:BookAuthors')->findBy(["id_author"=>$value]))==0){
                    $em->remove($this->getDoctrine()->getRepository('App:Authors')->find($value));
                    $em->flush();
                }
            }
            foreach ($idGenres as $value) {
                if(count($this->getDoctrine()->getRepository('App:BookGenres')->findBy(["id_genre"=>$value]))==0){
                    $em->remove($this->getDoctrine()->getRepository('App:Genres')->find($value));
                    $em->flush();
                }
            }
            $em->remove($book);
            $em->flush();
            return new Response("Книга успешно удалена", 200);
        }
    }
}