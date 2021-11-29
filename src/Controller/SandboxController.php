<?php

namespace App\Controller;

use App\Entity\Casting;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SandboxController extends AbstractController
{
    
    /**
     * @Route("/sandbox/genres", name="sandbox_genre")
     */
    public function demoGenre(GenreRepository $repo)
    {
        $genres = $repo->findGenreDemo('Comédie');

        dump($genres);

        return $this->render('sandbox/index.html.twig', [
            'controller_name' => 'SandboxController',
        ]);
    }


    /**
     * @Route("/sandbox/db_init", name="sandbox_init")
     */
    public function db_init(EntityManagerInterface $entityManager): Response
    {

        // on récupère l'entity manager dans le code
        // $entityManager = $this->getDoctrine()->getManager();

        // création de genres
        $genreAction = new Genre();
        $genreAction->setName('Action');
        $entityManager->persist($genreAction);

        $genreHorreur = new Genre();
        $genreHorreur->setName('Horreur');
        $entityManager->persist($genreHorreur);

        $genreDrame = new Genre();
        $genreDrame->setName('Drame');
        $entityManager->persist($genreDrame);

        $genreScienceFiction = new Genre();
        $genreScienceFiction->setName('Science Fiction');
        $entityManager->persist($genreScienceFiction);

        $genreThriller = new Genre();
        $genreThriller->setName('Thriller');
        $entityManager->persist($genreThriller);


        $genreComedie = new Genre();
        $genreComedie->setName('Comédie');
        $entityManager->persist($genreComedie);
        // création de movie

        $movieFargo = new Movie();
        $movieFargo->setTitle('Fargo');
        $movieFargo->addGenre($genreThriller);
        $movieFargo->addGenre($genreComedie);
        $entityManager->persist($movieFargo);

        $movieGodzilla = new Movie();
        $movieGodzilla->setTitle('Godzilla');

        // comme la méthode addGenre renvoie $this (c'est à dire movieGodzilla)
        // on peut "chainer" les méthodes en PHP
        $movieGodzilla
            ->addGenre($genreScienceFiction)
            ->addGenre($genreDrame)
            ->addGenre($genreComedie);
        $entityManager->persist($movieGodzilla);


        // Ajoutons des personnes
        $personMacy = new Person();
        $personMacy->setName('William H Macy');
        $entityManager->persist($personMacy);

        $personReno = new Person();
        $personReno->setName('Jean Reno');
        $entityManager->persist($personReno);

        $personBroderick = new Person();
        $personBroderick->setName('Matthew Broderick');
        $entityManager->persist($personBroderick);

        $personDenver = new Person();
        $personDenver->setName('Denver');
        $entityManager->persist($personDenver);

        $personLauriane = new Person();
        $personLauriane->setName('Lauriane');
        $entityManager->persist($personLauriane);

        $personJessica = new Person();
        $personJessica->setName('Jessica');
        $entityManager->persist($personJessica);

        $personEmilie = new Person();
        $personEmilie->setName('Emilie');
        $entityManager->persist($personEmilie);

        // casting 
        $casting1 = new Casting();
        $casting1
            ->setMovie($movieFargo)
            ->setPerson($personMacy)
            ->setRole('THE méchant')
            ->setCreditOrder(3);
        $entityManager->persist($casting1);

        $casting2 = new Casting();
        $casting2
            ->setMovie($movieFargo)
            ->setPerson($personJessica)
            ->setRole('THE gentille')
            ->setCreditOrder(1);
        $entityManager->persist($casting2);

        $casting3 = new Casting();
        $casting3
            ->setMovie($movieFargo)
            ->setPerson($personEmilie)
            ->setRole('THE détective')
            ->setCreditOrder(2);
        $entityManager->persist($casting3);

        $casting4 = new Casting();
        $casting4
            ->setMovie($movieGodzilla)
            ->setPerson($personDenver)
            ->setRole('Godzilla')
            ->setCreditOrder(1);
        $entityManager->persist($casting4);

        $casting5 = new Casting();
        $casting5
            ->setMovie($movieGodzilla)
            ->setPerson($personLauriane)
            ->setRole('Femme en détresse')
            ->setCreditOrder(2);
        $entityManager->persist($casting5);

        $casting6 = new Casting();
        $casting6
            ->setMovie($movieGodzilla)
            ->setPerson($personJessica)
            ->setRole('Actrice très connue')
            ->setCreditOrder(3);
        $entityManager->persist($casting6);

        // permet d'exécuter les requetes dans la BDD
        $entityManager->flush();

        return $this->render('sandbox/index.html.twig', [
            'controller_name' => 'SandboxController',
        ]);
    }
}
