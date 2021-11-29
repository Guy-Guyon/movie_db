<?php

namespace App\Controller\Back;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    /**
     * @Route("/admin/genre", name="admin_genre_browse", methods="GET")
     */
    public function browse(GenreRepository $genreRepo): Response
    {
        $allGenre = $genreRepo->findBy([], ['name' => 'ASC']);
        return $this->render('back/genre/browse.html.twig', [
            'genre_list' => $allGenre,
        ]);
    }

    /**
     * Cette route est facultative, on la mets ici car on fait du BREAD
     * @Route("/admin/genre/{id}", name="admin_genre_read", methods="GET", requirements={"id"="\d+"})
     */
    public function read(Genre $genre): Response
    {
        // ici on est sur d'avoir récupéré un objet car le ParamConverter renvoit une 404 dans le cas contraire
        // l'équivalent serait
        // $genre = $this->getDoctrine()->getRepository(Genre::class)->find($id);
        // if (null === $genre)
        // {
        //     throw $this->createNotFoundException('Ce genre n existe pas');
        // }

        return $this->render('back/genre/read.html.twig', [
            'genre' => $genre,
        ]);
    }

    /**
     * @Route("/admin/genre/edit/{id}", name="admin_genre_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Genre $genre, Request $request): Response
    {
        $form = $this->createForm(GenreType::class, $genre);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            // $genre->setUpdatedAt(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'Genre `' . $genre->getName() . '` mis à jour avec brio !');

            return $this->redirectToRoute('admin_genre_browse');
        }

        return $this->render('back/genre/edit.html.twig', [
            'form' => $form->createView(),
            'genre' => $genre,
        ]);
    }

    /**
     * @Route("/admin/genre/new", name="admin_genre_add", methods={"GET","POST"})
     */
    public function add(Request $request): Response
    {
        $genre = new Genre();
        // je crée un objet form type
        $form = $this->createForm(GenreType::class, $genre);
        // cette méthode va vérifier si un formulaire html a été soumis en post
        // et si ce formulaire concerne l'entité Genre
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // ici tout est ok, les champs sont valides et on peut continuer

            $genre = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($genre);
            $entityManager->flush();

            // ajout d'un flash message
            // on enregistre en bdd par exemple 
            // puis on redirige
            return $this->redirectToRoute('admin_genre_browse');
        }

        return $this->render('back/genre/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/genre/delete/{id}", name="admin_genre_delete", methods="GET")
     */
    public function delete(Genre $genre, EntityManagerInterface $em): Response
    {
        $em->remove($genre);

        $em->flush();
        // ajouter un flash message

        return $this->redirectToRoute('admin_genre_browse');
    }
}
