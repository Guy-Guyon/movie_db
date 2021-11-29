<?php

namespace App\Controller\Back;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Service\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    /**
     * @Route("/admin/movie", name="admin_movie_browse", methods="GET")
     */
    public function browse(MovieRepository $movieRepo): Response
    {
        $allMovie = $movieRepo->findBy([], ['title' => 'ASC']);
        return $this->render('back/movie/browse.html.twig', [
            'movie_list' => $allMovie,
        ]);
    }

    /**
     * Cette route est facultative, on la mets ici car on fait du BREAD
     * @Route("/admin/movie/{id}", name="admin_movie_read", methods="GET", requirements={"id"="\d+"})
     */
    public function read(Movie $movie): Response
    {
        // ici on est sur d'avoir récupéré un objet car le ParamConverter renvoit une 404 dans le cas contraire
        // l'équivalent serait
        // $movie = $this->getDoctrine()->getRepository(Movie::class)->find($id);
        // if (null === $movie)
        // {
        //     throw $this->createNotFoundException('Ce movie n existe pas');
        // }

        return $this->render('back/movie/read.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/admin/movie/edit/{id}", name="admin_movie_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Movie $movie, Request $request): Response
    {
        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $movie->setUpdatedAt(new \DateTime());
            $em->flush();

            // TODO flash message

            return $this->redirectToRoute('admin_movie_browse');
        }

        return $this->render('back/movie/edit.html.twig', [
            'form' => $form->createView(),
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/admin/movie/new", name="admin_movie_add", methods={"GET","POST"})
     */
    public function add(Request $request, Slugger $slugger): Response
    {
        $movie = new Movie();
        // je crée un objet form type
        $form = $this->createForm(MovieType::class, $movie);
        // cette méthode va vérifier si un formulaire html a été soumis en post
        // et si ce formulaire concerne l'entité Movie
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // ici tout est ok, les champs sont valides et on peut continuer

            $movie = $form->getData();

            // on génère le slug du film
            $slugger->slugifyMovie($movie);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($movie);
            $entityManager->flush();

            // ajout d'un flash message
            // on enregistre en bdd par exemple 
            // puis on redirige
            return $this->redirectToRoute('admin_movie_browse');
        }

        return $this->render('back/movie/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/movie/delete/{id}", name="admin_movie_delete", methods="GET")
     */
    public function delete(Movie $movie, EntityManagerInterface $em): Response
    {
        $em->remove($movie);

        $em->flush();
        // ajouter un flash message

        return $this->redirectToRoute('admin_movie_browse');
    }
}
