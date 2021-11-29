<?php 

namespace App\Service;

use App\Entity\Genre;
use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class Slugger 
{
    private $em;
    private $slugger;

    public function __construct(EntityManagerInterface $em, SluggerInterface $symfonySlugger)
    {
        $this->em = $em;
        $this->slugger = $symfonySlugger;
    }

    public function slugify($string) 
    {
        return $this->slugger->slug(strtolower($string));
    }

    public function slugifyMovie(Movie $movie)
    {
        $sluggedTitle = $this->slugify($movie->getTitle());

        // pour gérer les homonymes, on décide de rajouter l'id à la fin du slug
        $sluggedTitle .= '-' . $movie->getId();
        // c'est la meme chose que : 
        // $sluggedTitle = $sluggedTitle . '-' . $movie->getId();
        $movie->setSlug($sluggedTitle);

        // j'aimerai bien faire le flush de mon movie dans cette méthode
        $this->em->flush();

        return $movie;
    }

}
