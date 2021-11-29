<?php

namespace App\Controller\Front;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Service\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    /**
     * @Route("/movie/{id}", name="movie_show", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function show(Movie $movie, Slugger $slugger): Response
    {
        // modifier le code de la route movie/{id} pour
        //    - générer le slug du movie
        // $slugger = new Slugger();
        $movie = $slugger->slugifyMovie($movie);

        //    - flush notre entité pour avoir les modif en BDD
        $this->getDoctrine()->getManager()->flush();

        //    - rediriger vers la route sus-créée !
        return $this->redirectToRoute('movie_show_slug', ['slug' => $movie->getSlug()]);

        // // récupérer une instance de movieRepository
        // $movie = $movieRepo->findOneWithGenre($id);


        // return $this->render('movie/show.html.twig', [
        //     'movie' => $movie,
        // ]);
    }

    /**
     * @Route("/movie/{slug}", name="movie_show_slug", methods={"GET"})
     */
    public function showSlug(Movie $movie): Response
    {
        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }
}
